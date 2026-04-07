<?php

namespace App\Livewire;

use App\Models\Kriteria;
use App\Models\Dokumen;
use App\Models\Workflow;
use App\Services\AccreditationAiService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentUpload extends Component
{
    use WithFileUploads;

    public $kriteriaId;
    public $prodiId;
    public $nama_file;
    public $file;
    public $isUploading = false;
    public $message = '';

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

    public function submitFile()
    {
        $this->validate([
            'nama_file' => 'required|string|max:255',
            'file' => 'required|mimes:pdf,docx,xlsx,zip|max:10240',
            'kriteriaId' => 'required',
        ], [
            'kriteriaId.required' => 'Kriteria belum dipilih. Silakan pilih kriteria di sidebar.',
            'file.required' => 'Anda belum memilih file dokumen.',
            'nama_file.required' => 'Nama dokumen wajib diisi.'
        ]);

        $this->isUploading = true;

        // Store file
        $fileName = time() . '_' . $this->file->getClientOriginalName();
        $path = $this->file->storeAs('dokumens', $fileName, 'public');

        // Create Dokumen Record
        $dokumen = Dokumen::create([
            'user_id' => Auth::id(),
            'prodi_id' => $this->prodiId,
            'kriteria_id' => $this->kriteriaId,
            'nama_file' => $this->nama_file,
            'file_path' => $path,
            'status' => 'submitted',
            'metadata' => [
                'original_name' => $this->file->getClientOriginalName(),
                'size' => $this->file->getSize(),
                'mime' => $this->file->getMimeType()
            ]
        ]);

        // Workflow logging
        Workflow::create([
            'trackable_id' => $dokumen->id,
            'trackable_type' => Dokumen::class,
            'from_status' => 'draft',
            'to_status' => 'submitted',
            'action' => 'upload',
            'user_id' => Auth::id(),
            'comment' => 'Initial upload to ' . optional($dokumen->kriteria)->nama
        ]);

        // (AI Analysis removed as per user request to speed up upload)
        // $aiService = new AccreditationAiService();
        // $aiService->analyzeDocument($dokumen);

        $this->message = "Dokumen berhasil diunggah.";
        $this->reset(['file', 'nama_file']);
        $this->isUploading = false;

        $this->dispatch('dokumen-uploaded');
    }

    public function render()
    {
        return view('livewire.document-upload', [
            'kriterias' => Kriteria::all()
        ]);
    }
}
