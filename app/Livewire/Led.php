<?php

namespace App\Livewire;

use App\Models\Kriteria;
use Livewire\Component;

class Led extends Component
{
    public $kriterias;
    public $activeKriteria = null;
    public $narasi = [];
    public $currentNarasiModel = null;
    public $lamType = 'ban-pt';
    public $sections = [];
    public $users = [];
    public $prodiId;
    public $auditResults = [];
    public $consistencyResults = [];
    public $evidenceSuggestions = [];
    public $isAuditing = false;
    public $isCheckingConsistency = false;
    public $isFindingEvidence = false;
    public $showPreview = false;
    public $statusOptions = [
        'todo' => 'To Do',
        'in_progress' => 'In Progress',
        'review' => 'Review',
        'done' => 'Done'
    ];

    public function mount()
    {
        $this->prodiId = session('selected_prodi_id');
        $prodi = \App\Models\Prodi::find($this->prodiId) ?? auth()->user()->prodi ?? \App\Models\Prodi::first();
        $this->prodiId = $prodi->id;
        
        $this->lamType = $prodi->lam_type ?? 'ban-pt';
        $this->kriterias = Kriteria::where('lam_type', $this->lamType)->orderBy('kode', 'asc')->get();
        
        // Load users from the same prodi for assignment
        $this->users = \App\Models\User::where('prodi_id', $this->prodiId)->get();

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
        $this->sections = $kriteria->template_narasi ?? $this->getDefaultSections($this->lamType, $kriteria);

        // Load or create Narasi record for this Prodi & Kriteria
        $this->currentNarasiModel = \App\Models\Narasi::firstOrCreate(
            ['prodi_id' => $this->prodiId, 'kriteria_id' => $id],
            ['content' => array_fill_keys(array_keys($this->sections), ''), 'status' => 'todo']
        );

        $this->narasi[$id] = $this->currentNarasiModel->content;
    }

    protected function getDefaultSections($lamType, $kriteria = null)
    {
        // LAMEMBA has specific dimensions per criteria
        if ($lamType === 'lam-emba' && $kriteria) {
            $kode = (string) $kriteria->kode;
            $embaTemplates = [
                '1' => ['a' => 'Misi', 'b' => 'Visi', 'c' => 'Tujuan dan Sasaran', 'd' => 'Strategi'],
                '2' => ['a' => 'Tata Pamong', 'b' => 'Tata Kelola'],
                '3' => ['a' => 'Penerimaan Mahasiswa', 'b' => 'Layanan Akademik', 'c' => 'Kinerja Akademik', 'd' => 'Kesejahteraan', 'e' => 'Karir'],
                '4' => ['a' => 'Kecukupan Dosen', 'b' => 'Pengelolaan Dosen', 'c' => 'Tenaga Kependidikan', 'd' => 'Pengelolaan Tendik'],
                '5' => ['a' => 'Keuangan', 'b' => 'Sarana dan Prasarana'],
                '6' => ['a' => 'Kurikulum', 'b' => 'Jaminan Pembelajaran'],
                '7' => ['a' => 'Penelitian', 'b' => 'Pengabdian Masyarakat'],
            ];
            
            if (isset($embaTemplates[$kode])) {
                return $embaTemplates[$kode];
            }
        }

        $templates = [
            'ban-pt' => [
                'kondisi' => 'Kondisi Saat Ini',
                'evaluasi' => 'Evaluasi Capaian',
                'rencana' => 'Rencana Tindak Lanjut',
            ],
            'lam-infokom' => [
                'penetapan' => '[PENETAPAN] Kebijakan & Standar',
                'pelaksanaan' => '[PELAKSANAAN] Pelaksanaan Standar',
                'evaluasi' => '[EVALUASI] Evaluasi Capaian & Survei',
                'pengendalian' => '[PENGENDALIAN] Tindak Lanjut & Rekomendasi',
                'peningkatan' => '[PENINGKATAN] Peningkatan Standar Mutu',
            ],
            'lam-emba' => [
                'dimensi_a' => 'Elemen / Dimensi A',
                'dimensi_b' => 'Elemen / Dimensi B',
                'dimensi_c' => 'Elemen / Dimensi C',
                'dimensi_d' => 'Elemen / Dimensi D',
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
        
        $oldStatus = $this->currentNarasiModel->status;
        $this->currentNarasiModel->update([
            'content' => $this->editData,
            'status' => ($oldStatus == 'todo') ? 'in_progress' : $oldStatus
        ]);

        // Record work session in workflow if progress changed
        if ($oldStatus == 'todo') {
            $this->logWorkflow('status_change', 'todo', 'in_progress', 'Narrative editing started');
        }

        $this->isEditing = false;
        $this->dispatch('notify', message: 'Narasi berhasil disimpan!', type: 'success');
        $this->dispatch('narasi-saved');
    }

    public function updateStatus($newStatus)
    {
        if (!array_key_exists($newStatus, $this->statusOptions)) return;

        $oldStatus = $this->currentNarasiModel->status;
        $this->currentNarasiModel->update(['status' => $newStatus]);

        $this->logWorkflow('status_change', $oldStatus, $newStatus, "Status changed to {$this->statusOptions[$newStatus]}");
        
        $this->dispatch('notify', message: 'Status kriteria diperbarui!', type: 'success');
    }

    public function updateAssignee($userId)
    {
        $oldAssignee = $this->currentNarasiModel->assignee_id;
        $this->currentNarasiModel->update(['assignee_id' => $userId]);

        $userName = $userId ? \App\Models\User::find($userId)->name : 'None';
        $this->logWorkflow('assignment_change', $oldAssignee, $userId, "Assigned to {$userName}");

        $this->dispatch('notify', message: 'Penanggung jawab diperbarui!', type: 'success');
    }

    protected function logWorkflow($action, $old, $new, $comment)
    {
        $this->currentNarasiModel->workflows()->create([
            'from_status' => $old ?? 'none',
            'to_status' => $new ?? 'none',
            'user_id' => auth()->id(),
            'action' => $action,
            'old_value' => $old,
            'new_value' => $new,
            'comment' => $comment
        ]);
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

    public function auditCompliance()
    {
        $this->isAuditing = true;
        
        $kriteria = Kriteria::find($this->activeKriteria);
        $narrative = $this->isEditing ? $this->editData : $this->narasi[$this->activeKriteria];
        
        // If queue is configured, run in background
        if (config('queue.default') !== 'sync') {
            \App\Jobs\AuditNarrativeJob::dispatch(
                $this->activeKriteria,
                $this->prodiId,
                session('selected_prodi_name'),
                $narrative
            );
            $this->dispatch('notify', message: 'Audit sedang diproses di background. Hasil akan muncul otomatis.', type: 'info');
            $this->isAuditing = false;
            return;
        }

        // Fallback for sync processing
        $aiService = new \App\Services\AccreditationAiService();
        $documents = \App\Models\Dokumen::where('kriteria_id', $this->activeKriteria)
            ->where('prodi_id', $this->prodiId)
            ->whereIn('status', ['submitted', 'approved'])
            ->get();
            
        $docContext = $documents->map(fn($d) => ['id' => $d->id, 'nama' => $d->nama_file])->toArray();
        $lkpsRows = \App\Models\LkpsData::where('prodi_id', $this->prodiId)->get();
        $lkpsContext = $lkpsRows->map(fn($r) => $r->data_values)->toArray();

        $context = [
            'prodi' => session('selected_prodi_name'),
            'kriteria' => $kriteria->nama,
            'kode_kriteria' => $kriteria->kode,
            'dokumen_tersedia' => $docContext,
            'data_lkps' => $lkpsContext
        ];

        try {
            $result = $aiService->auditNarrative($kriteria, $narrative, $context);
            $this->auditResults[$this->activeKriteria] = $result;
            
            if ($this->currentNarasiModel) {
                $meta = $this->currentNarasiModel->metadata ?? [];
                $meta['last_audit'] = array_merge($result, ['timestamp' => now()->toDateTimeString()]);
                $this->currentNarasiModel->update(['metadata' => $meta]);
            }

            $this->logWorkflow('ai_audit', null, null, 'Automated AI Compliance Audit performed. Score: ' . ($result['predicted_score'] ?? 'N/A'));
            $this->dispatch('notify', message: 'Audit Compliance selesai!', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Gagal melakukan audit: ' . $e->getMessage(), type: 'error');
        }

        $this->isAuditing = false;
    }

    public function checkConsistency()
    {
        $this->isCheckingConsistency = true;
        
        $kriteria = Kriteria::find($this->activeKriteria);
        $narrative = $this->isEditing ? $this->editData : $this->narasi[$this->activeKriteria];
        
        $lkpsRows = \App\Models\LkpsData::where('prodi_id', $this->prodiId)->get();
        $lkpsContext = $lkpsRows->map(fn($r) => [
            'tabel' => $r->lam_table_id,
            'data' => $r->data_values
        ])->toArray();

        $aiService = new \App\Services\AccreditationAiService();

        try {
            $result = $aiService->checkDataConsistency($kriteria, (array)$narrative, $lkpsContext);
            $this->consistencyResults[$this->activeKriteria] = $result;
            
            if ($this->currentNarasiModel) {
                $meta = $this->currentNarasiModel->metadata ?? [];
                $meta['last_consistency_check'] = array_merge($result, ['timestamp' => now()->toDateTimeString()]);
                $this->currentNarasiModel->update(['metadata' => $meta]);
            }

            $this->dispatch('notify', message: 'Audit Linieritas selesai!', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Gagal cek linieritas: ' . $e->getMessage(), type: 'error');
        }

        $this->isCheckingConsistency = false;
    }

    public function findEvidence()
    {
        $this->isFindingEvidence = true;
        
        $kriteria = Kriteria::find($this->activeKriteria);
        $narrative = $this->isEditing ? $this->editData : $this->narasi[$this->activeKriteria];
        
        // Get all relevant documents for this prodi & kriteria
        $documents = \App\Models\Dokumen::where('prodi_id', $this->prodiId)
            ->where('kriteria_id', $this->activeKriteria)
            ->get();
            
        $docList = $documents->map(fn($d) => [
            'id' => $d->id,
            'name' => $d->nama_file
        ])->toArray();

        $aiService = new \App\Services\AccreditationAiService();

        try {
            $result = $aiService->suggestCitations((array)$narrative, $docList);
            $this->evidenceSuggestions[$this->activeKriteria] = $result['suggestions'] ?? [];
            
            $this->dispatch('notify', message: 'Saran bukti fisik ditemukan!', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Gagal mencari bukti: ' . $e->getMessage(), type: 'error');
        }

        $this->isFindingEvidence = false;
    }

    public function render()
    {
        return view('livewire.led')->layout('layouts.app');
    }
}
