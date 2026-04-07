<?php

namespace App\Livewire;

use App\Models\Dokumen;
use App\Models\DocumentVersion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class CollaborativeEditor extends Component
{
    public Dokumen $document;
    public $content = '';
    public $isLocked = false;
    public $lockedBy = null;
    public $lockExpiresAt = null;
    public $activeUsers = [];
    public $lastSaved = null;
    public $isSaving = false;

    protected $listeners = [
        'refreshEditor' => '$refresh',
        'userActivity' => 'handleUserActivity'
    ];

    public function mount(Dokumen $document)
    {
        $this->document = $document;
        $this->loadDocumentContent();
        $this->checkDocumentLock();
        $this->updateUserPresence();
    }

    public function updated($property)
    {
        if ($property === 'content') {
            $this->updateUserPresence();
            $this->autoSave();
        }
    }

    public function loadDocumentContent()
    {
        // For now, we'll use the document metadata or create a simple text content
        // In a real implementation, you might extract text from PDF or have separate content storage
        $this->content = $this->document->metadata['editor_content'] ?? 'Mulai mengedit dokumen ini...';

        // Load last saved timestamp
        $this->lastSaved = $this->document->updated_at;
    }

    public function requestLock()
    {
        if ($this->isLocked && $this->lockedBy !== Auth::id()) {
            session()->flash('error', 'Dokumen sedang dikunci oleh pengguna lain.');
            return;
        }

        $this->lockDocument();
    }

    public function releaseLock()
    {
        if ($this->lockedBy === Auth::id()) {
            $this->unlockDocument();
        }
    }

    protected function lockDocument()
    {
        $lockKey = "document_lock_{$this->document->id}";
        $lockData = [
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'expires_at' => now()->addMinutes(30) // 30 minute lock
        ];

        Cache::put($lockKey, $lockData, 30 * 60); // 30 minutes

        $this->isLocked = true;
        $this->lockedBy = Auth::id();
        $this->lockExpiresAt = $lockData['expires_at'];

        $this->dispatch('lock-acquired');
    }

    protected function unlockDocument()
    {
        $lockKey = "document_lock_{$this->document->id}";
        Cache::forget($lockKey);

        $this->isLocked = false;
        $this->lockedBy = null;
        $this->lockExpiresAt = null;

        $this->dispatch('lock-released');
    }

    protected function checkDocumentLock()
    {
        $lockKey = "document_lock_{$this->document->id}";
        $lockData = Cache::get($lockKey);

        if ($lockData) {
            $this->isLocked = true;
            $this->lockedBy = $lockData['user_id'];
            $this->lockExpiresAt = $lockData['expires_at'];

            // Check if lock has expired
            if (now()->greaterThan($lockData['expires_at'])) {
                $this->unlockDocument();
            }
        }
    }

    protected function updateUserPresence()
    {
        $presenceKey = "document_presence_{$this->document->id}";
        $userData = [
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'last_activity' => now(),
            'avatar_color' => $this->getUserColor(Auth::id())
        ];

        // Get all active users for this document
        $presenceData = Cache::get($presenceKey, []);
        $presenceData[Auth::id()] = $userData;

        // Remove inactive users (no activity for 5 minutes)
        $presenceData = array_filter($presenceData, function($user) {
            return now()->diffInMinutes($user['last_activity']) < 5;
        });

        Cache::put($presenceKey, $presenceData, 5 * 60); // 5 minutes

        $this->activeUsers = array_values($presenceData);
    }

    public function handleUserActivity($userId)
    {
        $this->updateUserPresence();
    }

    protected function autoSave()
    {
        // Throttle auto-save to every 5 seconds
        static $lastSave = null;
        if ($lastSave && now()->diffInSeconds($lastSave) < 5) {
            return;
        }

        $this->isSaving = true;

        // Save to document metadata or separate content storage
        $metadata = $this->document->metadata ?? [];
        $oldContent = $metadata['editor_content'] ?? '';
        $metadata['editor_content'] = $this->content;
        $metadata['last_edited_by'] = Auth::id();
        $metadata['last_edited_at'] = now();

        $this->document->update([
            'metadata' => $metadata
        ]);

        // Create version if content has changed significantly
        if ($this->shouldCreateVersion($oldContent, $this->content)) {
            DocumentVersion::createVersion(
                $this->document,
                $this->content,
                'Auto-saved changes',
                Auth::id()
            );
        }

        $this->lastSaved = now();
        $lastSave = now();

        $this->isSaving = false;

        // Broadcast to other users
        broadcast(new \App\Events\ContentUpdated($this->document->id, Auth::user()->name, now()))->toOthers();
        $this->dispatch('content-updated', [
            'user' => Auth::user()->name,
            'timestamp' => now()
        ]);
    }

    protected function shouldCreateVersion(string $oldContent, string $newContent): bool
    {
        // Create version if content changed significantly (more than 10% difference)
        $oldLength = strlen($oldContent);
        $newLength = strlen($newContent);

        if ($oldLength === 0) return true;

        $changePercent = abs($newLength - $oldLength) / $oldLength * 100;
        return $changePercent > 10 || $oldContent !== $newContent;
    }

    public function saveDocument()
    {
        $this->autoSave();
        session()->flash('message', 'Dokumen berhasil disimpan.');
    }

    protected function getUserColor($userId)
    {
        $colors = [
            'bg-red-500', 'bg-blue-500', 'bg-green-500', 'bg-yellow-500',
            'bg-purple-500', 'bg-pink-500', 'bg-indigo-500', 'bg-teal-500'
        ];

        return $colors[$userId % count($colors)];
    }

    public function getVersionHistory()
    {
        return DocumentVersion::getVersionHistory($this->document->id);
    }

    public function restoreVersion($versionId)
    {
        $version = DocumentVersion::findOrFail($versionId);

        if ($version->document_id !== $this->document->id) {
            session()->flash('error', 'Versi tidak valid untuk dokumen ini.');
            return;
        }

        // Check if document is locked by another user
        if ($this->isLocked && $this->lockedBy !== Auth::id()) {
            session()->flash('error', 'Tidak dapat memulihkan versi karena dokumen sedang dikunci.');
            return;
        }

        if ($version->restore()) {
            $this->content = $version->content;
            $this->lastSaved = now();

            // Create a new version to record the restoration
            DocumentVersion::createVersion(
                $this->document,
                $this->content,
                "Dipulihkan dari versi {$version->version_number}",
                Auth::id()
            );

            session()->flash('message', "Dokumen berhasil dipulihkan ke versi {$version->version_number}.");
        } else {
            session()->flash('error', 'Gagal memulihkan versi dokumen.');
        }
    }

    public function createManualVersion($summary = null)
    {
        DocumentVersion::createVersion(
            $this->document,
            $this->content,
            $summary ?: 'Versi manual dibuat oleh pengguna',
            Auth::id()
        );

        session()->flash('message', 'Versi dokumen berhasil dibuat.');
    }

    public function render()
    {
        return view('livewire.collaborative-editor', [
            'document' => $this->document,
            'kriterias' => \App\Models\Kriteria::all(),
            'versionHistory' => $this->getVersionHistory()
        ])->layout('layouts.app');
    }
}
