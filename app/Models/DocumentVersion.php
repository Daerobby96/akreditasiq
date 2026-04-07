<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class DocumentVersion extends Model
{
    protected $fillable = [
        'document_id',
        'user_id',
        'version_number',
        'content',
        'metadata',
        'change_summary',
        'file_path'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    /**
     * Get the document this version belongs to
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Dokumen::class, 'document_id');
    }

    /**
     * Get the user who created this version
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a new version for a document
     */
    public static function createVersion(Dokumen $document, string $content, ?string $changeSummary = null, ?int $userId = null): self
    {
        $latestVersion = self::where('document_id', $document->id)
            ->orderBy('version_number', 'desc')
            ->first();

        $nextVersionNumber = $latestVersion
            ? self::incrementVersionNumber($latestVersion->version_number)
            : '1.0.0';

        return self::create([
            'document_id' => $document->id,
            'user_id' => $userId ?? Auth::id(),
            'version_number' => $nextVersionNumber,
            'content' => $content,
            'change_summary' => $changeSummary,
            'metadata' => [
                'created_from' => 'collaborative_editor',
                'timestamp' => now()->toISOString()
            ]
        ]);
    }

    /**
     * Increment version number (semantic versioning)
     */
    protected static function incrementVersionNumber(string $currentVersion): string
    {
        $parts = explode('.', $currentVersion);

        // For patch version increment (1.0.0 -> 1.0.1)
        if (count($parts) >= 3) {
            $parts[2] = (int) $parts[2] + 1;
        } else {
            $parts[] = 1;
        }

        return implode('.', $parts);
    }

    /**
     * Get the previous version
     */
    public function getPreviousVersion()
    {
        return self::where('document_id', $this->document_id)
            ->where('created_at', '<', $this->created_at)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Get the next version
     */
    public function getNextVersion()
    {
        return self::where('document_id', $this->document_id)
            ->where('created_at', '>', $this->created_at)
            ->orderBy('created_at', 'asc')
            ->first();
    }

    /**
     * Calculate diff with another version
     */
    public function getDiffWith(DocumentVersion $otherVersion): array
    {
        // Simple diff calculation - in production you might want to use a proper diff library
        $diffs = [];

        if ($this->content !== $otherVersion->content) {
            $diffs['content'] = [
                'from' => $otherVersion->content,
                'to' => $this->content,
                'changed' => true
            ];
        }

        return $diffs;
    }

    /**
     * Get version history for a document
     */
    public static function getVersionHistory(int $documentId): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('document_id', $documentId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Restore document to this version
     */
    public function restore(): bool
    {
        $document = $this->document;

        // Update document content
        $metadata = $document->metadata ?? [];
        $metadata['editor_content'] = $this->content;
        $metadata['restored_from_version'] = $this->version_number;
        $metadata['restored_by'] = Auth::id();
        $metadata['restored_at'] = now();

        return $document->update([
            'metadata' => $metadata
        ]);
    }
}
