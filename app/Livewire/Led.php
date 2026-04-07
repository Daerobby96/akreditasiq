<?php

namespace App\Livewire;

use App\Models\Kriteria;
use Livewire\Component;

class Led extends Component
{
    public $kriterias;
    public $activeKriteria = null;
    public $narasi = [];
    public $lamType = 'ban-pt';
    public $sections = [];

    public function mount()
    {
        $prodiId = session('selected_prodi_id');
        $prodi = \App\Models\Prodi::find($prodiId) ?? auth()->user()->prodi ?? \App\Models\Prodi::first();
        
        $this->lamType = $prodi->lam_type ?? 'ban-pt';
        $this->kriterias = Kriteria::where('lam_type', $this->lamType)->orderBy('kode', 'asc')->get();
        
        $firstKriteria = $this->kriterias->first();
        if ($firstKriteria) {
            $this->selectKriteria($firstKriteria->id);
        }
    }

    public function selectKriteria($id)
    {
        $this->activeKriteria = $id;
        $this->isEditing = false;
        
        $kriteria = Kriteria::find($id);
        
        // Define dynamic sections
        $this->sections = $kriteria->template_narasi ?? $this->getDefaultSections($this->lamType);

        if (!isset($this->narasi[$id])) {
            $this->narasi[$id] = array_fill_keys(array_keys($this->sections), '');
        }
    }

    protected function getDefaultSections($lamType)
    {
        $templates = [
            'ban-pt' => [
                'kondisi' => 'Kondisi Saat Ini',
                'evaluasi' => 'Evaluasi Capaian',
                'rencana' => 'Rencana Tindak Lanjut',
            ],
            'lam-infokom' => [
                'kondisi' => 'Analisis Kurikulum & SDM Komputasi',
                'evaluasi' => 'Evaluasi Capaian Pembelajaran',
                'penjaminan' => 'Sistem Penjaminan Mutu Internal',
                'rencana' => 'Strategi Pengembangan IT',
            ],
            'lam-emba' => [
                'kondisi' => 'Kondisi Ekonomi & Bisnis Terkini',
                'akuntabilitas' => 'Akuntabilitas & Tata Kelola',
                'evaluasi' => 'Evaluasi Capaian Strategis',
                'rencana' => 'Sustainability Plan',
            ],
        ];

        return $templates[$lamType] ?? $templates['ban-pt'];
    }

    public $isEditing = false;
    public $editData = [];

    public function edit()
    {
        $this->editData = $this->narasi[$this->activeKriteria] ?? array_fill_keys(array_keys($this->sections), '');
        $this->isEditing = true;
    }

    public function save()
    {
        $this->narasi[$this->activeKriteria] = $this->editData;
        $this->isEditing = false;
        
        $this->dispatch('narasi-saved');
    }

    public function cancelEdit()
    {
        $this->isEditing = false;
    }

    public $isGeneratingAi = false;
    public $aiPrompt = '';
    public $showAiPanel = false;

    public function generateAiNarrative($section)
    {
        $this->isGeneratingAi = true;
        
        $kriteria = Kriteria::find($this->activeKriteria);
        $aiService = new \App\Services\AccreditationAiService();
        
        // 1. Fetch real context from Data Dukung (Documents)
        $documents = \App\Models\Dokumen::where('kriteria_id', $this->activeKriteria)
            ->where('prodi_id', session('selected_prodi_id'))
            ->whereIn('status', ['submitted', 'approved'])
            ->get();
            
        $docContext = [];
        foreach ($documents as $doc) {
            try {
                $docText = "";
                $path = storage_path('app/public/' . $doc->file_path);
                if (file_exists($path)) {
                    $parser = new \Smalot\PdfParser\Parser();
                    $pdf = $parser->parseFile($path);
                    $docText = substr($pdf->getText(), 0, 3000); // Limit per doc
                }
                
                $docContext[] = [
                    'id' => $doc->id,
                    'nama_file' => $doc->nama_file,
                    'content' => $docText
                ];
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Failed to extract text for LED context from doc {$doc->id}");
            }
        }

        // 2. Fetch LKPS Data for the entire Prodi (simplified mapping)
        $lkpsRows = \App\Models\LkpsData::where('prodi_id', session('selected_prodi_id'))->get();
        $lkpsContext = $lkpsRows->map(fn($r) => $r->data_values)->toJson();

        $context = [
            'prodi' => session('selected_prodi_name'),
            'kriteria' => $kriteria->nama,
            'kode_kriteria' => $kriteria->kode,
            'dokumen_pendukung_text' => $docContext ?: 'Tidak ada dokumen pendukung terunggah.',
            'data_tabel_lkps' => $lkpsContext
        ];

        try {
            $result = $aiService->generateNarrative($kriteria, $context, $this->aiPrompt);
            
            // AI now returns HTML, so we don't need nl2br
            $formattedResult = $result;
            
            if ($this->isEditing) {
                $this->editData[$section] = $formattedResult;
                // Dispatch event to update Trix editors via JS if needed
                $this->dispatch('ai-content-generated', field: $section, content: $formattedResult);
            } else {
                $this->narasi[$this->activeKriteria][$section] = $formattedResult;
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Gagal generate narasi: ' . $e->getMessage(), type: 'error');
        }

        $this->isGeneratingAi = false;
        $this->showAiPanel = false;
        $this->aiPrompt = '';
        
        $this->dispatch('notify', message: 'Narasi AI berhasil digenerate!', type: 'success');
    }

    public function render()
    {
        return view('livewire.led')->layout('layouts.app');
    }
}
