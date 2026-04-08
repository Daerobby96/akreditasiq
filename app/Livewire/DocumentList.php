<?php

namespace App\Livewire;

use App\Models\Dokumen;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentList extends Component
{
    use WithFileUploads;

    public $prodiId;
    public $kriteriaId;
    public $previewingUrl = null;
    public $analyzingIds = [];
    public $analysisSteps = []; // Track status messages
    public $tempFile; // For quick upload

    public function finishDraft($id)
    {
        $this->validate([
            'tempFile' => 'required|mimes:pdf,docx,xlsx,zip|max:10240',
        ]);

        $dokumen = Dokumen::find($id);
        if ($dokumen) {
            $fileName = time() . '_' . $this->tempFile->getClientOriginalName();
            $path = $this->tempFile->storeAs('dokumens', $fileName, 'public');

            $dokumen->update([
                'file_path' => $path,
                'status' => 'submitted',
                'metadata' => [
                    'original_name' => $this->tempFile->getClientOriginalName(),
                    'size' => $this->tempFile->getSize(),
                    'mime' => $this->tempFile->getMimeType()
                ]
            ]);

            \App\Models\Workflow::create([
                'trackable_id' => $dokumen->id,
                'trackable_type' => Dokumen::class,
                'user_id' => auth()->id(),
                'from_status' => 'draft',
                'to_status' => 'submitted',
                'action' => 'upload_draft',
                'comment' => 'Completed AI-generated draft placeholder.',
            ]);

            $this->tempFile = null;
            $this->dispatch('notify', message: 'Dokumen berhasil diunggah!', type: 'success');
        }
    }

    protected $listeners = ['kriteria-selected' => 'updateKriteria'];

    public function mount($kriteriaId = null, $prodiId = null)
    {
        $this->kriteriaId = $kriteriaId;
        $this->prodiId = $prodiId;
    }

    public function updateKriteria($kriteriaId)
    {
        $this->kriteriaId = $kriteriaId;
    }

    public function preview($id)
    {
        $doc = Dokumen::find($id);
        if ($doc) {
            $this->previewingUrl = asset('storage/' . $doc->file_path);
        }
    }

    public function closePreview()
    {
        $this->previewingUrl = null;
    }

    #[On('dokumen-uploaded')]
    public function refreshDocuments()
    {
        // Re-rendered
    }

    public function delete($id)
    {
        $dokumen = Dokumen::find($id);
        if ($dokumen) {
            $dokumen->delete();
        }
    }

    public $editingId = null;
    public $editName = '';

    public function edit($id)
    {
        $dokumen = Dokumen::find($id);
        $this->editingId = $dokumen->id;
        $this->editName = $dokumen->nama_file;
    }

    public function saveName()
    {
        $dokumen = Dokumen::find($this->editingId);
        if ($dokumen) {
            $dokumen->update(['nama_file' => $this->editName]);
            $this->editingId = null;
            $this->dispatch('notify', message: 'Nama dokumen diperbarui!', type: 'success');
        }
    }

    public function cancelEdit()
    {
        $this->editingId = null;
    }

    public function updateStatus($id, $status)
    {
        $dokumen = Dokumen::find($id);
        if ($dokumen) {
            $oldStatus = $dokumen->status;
            $dokumen->update(['status' => $status]);

            \App\Models\Workflow::create([
                'trackable_id' => $dokumen->id,
                'trackable_type' => Dokumen::class,
                'user_id' => auth()->id(),
                'from_status' => $oldStatus,
                'to_status' => $status,
                'action' => 'status_update',
                'comment' => 'Status updated.',
            ]);

            $this->dispatch('status-updated');
        }
    }

    public function runAiAnalysis($id)
    {
        $this->analyzingIds[] = $id;
        $this->analysisSteps[$id] = ['step' => 1, 'label' => 'Mengekstrak teks dokumen...', 'progress' => 15];
        
        // Use stream to update UI (optional but better if we break it down)
        // For now we use standard steps if we execute synchronously
        
        try {
            $dokumen = Dokumen::find($id);
            if ($dokumen) {
                $aiService = new \App\Services\AccreditationAiService();
                
                // Step 1: Extraction (Done inside service, but we can simulate/update)
                // To actually update UI during a single request in LW3, we'd need multiple requests 
                // or wire:stream. Since we want to keep it simple, we'll rely on the frontend
                // animation for the 'Progress Bar' look.
                
                $this->analysisSteps[$id] = ['step' => 2, 'label' => 'Menghubungi AI Smart Engine...', 'progress' => 45];
                
                $aiService->analyzeDocument($dokumen);
                
                $this->analysisSteps[$id] = ['step' => 3, 'label' => 'Menyimpan hasil analisis...', 'progress' => 90];
                $this->dispatch('notify', message: 'Analisis AI selesai!', type: 'success');
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Gagal analisis: ' . $e->getMessage(), type: 'error');
        }

        unset($this->analysisSteps[$id]);
        $this->analyzingIds = array_diff($this->analyzingIds, [$id]);
    }

    public function render()
    {
        $query = Dokumen::with(['kriteria', 'penilaian_ai'])
                ->orderBy('created_at', 'desc');

        if ($this->kriteriaId) {
            $query->where('kriteria_id', $this->kriteriaId);
        }

        if ($this->prodiId) {
            $query->where('prodi_id', $this->prodiId);
        }

        return view('livewire.document-list', [
            'dokumens' => $query->get()
        ]);
    }
}
