<?php

namespace App\Livewire;

use App\Models\Dokumen;
use App\Models\Kriteria;
use App\Models\PenilaianAi;
use App\Services\AccreditationAiService;
use Livewire\Component;

class AiAudit extends Component
{
    public $selectedKriteria = '';
    public $isAnalyzing = false;
    public $analysisResult = null;
    public $prodi;

    public function mount()
    {
        $prodiId = session('selected_prodi_id');
        $this->prodi = \App\Models\Prodi::find($prodiId) ?? \App\Models\Prodi::first();
    }

    public function runAudit()
    {
        if (!$this->selectedKriteria) return;

        $this->isAnalyzing = true;

        $dokumens = Dokumen::where('kriteria_id', $this->selectedKriteria)
            ->where('prodi_id', $this->prodi->id)
            ->whereIn('status', ['submitted', 'approved'])
            ->get();

        if ($dokumens->isEmpty()) {
            $this->analysisResult = [
                'status' => 'empty',
                'message' => 'Tidak ada dokumen yang ditemukan untuk kriteria ini.',
            ];
            $this->isAnalyzing = false;
            return;
        }

        $aiService = new AccreditationAiService();
        $results = [];

        foreach ($dokumens as $dokumen) {
            // Cek apakah sudah pernah dianalisis
            $existing = PenilaianAi::where('dokumen_id', $dokumen->id)->first();
            if (!$existing) {
                $existing = $aiService->analyzeDocument($dokumen);
            }
            if ($existing) {
                $results[] = $existing;
            }
        }

        $avgScore = collect($results)->avg('skor');
        $kriteria = Kriteria::find($this->selectedKriteria);

        $this->analysisResult = [
            'status' => 'success',
            'kriteria' => $kriteria->kode . ': ' . $kriteria->nama,
            'total_docs' => $dokumens->count(),
            'avg_score' => number_format($avgScore, 2),
            'results' => collect($results)->map(fn($r) => [
                'skor' => number_format($r->skor, 2),
                'analisis' => $r->analisis_teks,
                'gap' => $r->gap_analysis,
                'rekomendasi' => $r->rekomendasi,
                'engine' => $r->engine,
            ])->toArray(),
        ];

        $this->isAnalyzing = false;
    }

    public function runFullAudit()
    {
        $this->isAnalyzing = true;
        $kriterias = Kriteria::where('lam_type', $this->prodi->lam_type)->with(['dokumens' => function ($q) {
            $q->where('prodi_id', $this->prodi->id)
              ->whereIn('status', ['submitted', 'approved']);
        }])->get();

        $aiService = new AccreditationAiService();
        $summary = [];

        foreach ($kriterias as $k) {
            $scores = [];
            foreach ($k->dokumens as $dok) {
                $existing = PenilaianAi::where('dokumen_id', $dok->id)->first();
                if (!$existing) {
                    $existing = $aiService->analyzeDocument($dok);
                }
                if ($existing) {
                    $scores[] = $existing->skor;
                }
            }
            $summary[] = [
                'kode' => $k->kode,
                'nama' => $k->nama,
                'docs' => $k->dokumens->count(),
                'avg' => count($scores) > 0 ? number_format(array_sum($scores) / count($scores), 2) : '-',
            ];
        }

        $this->analysisResult = [
            'status' => 'full',
            'summary' => $summary,
        ];

        $this->isAnalyzing = false;
    }

    public function resetResult()
    {
        $this->analysisResult = null;
        $this->selectedKriteria = '';
    }

    public function render()
    {
        return view('livewire.ai-audit', [
            'kriterias' => Kriteria::where('lam_type', $this->prodi->lam_type)->orderBy('kode', 'asc')->get(),
            'analyzing' => $this->isAnalyzing,
            'lamLabel' => $this->getLamLabel()
        ])->layout('layouts.app');
    }

    protected function getLamLabel()
    {
        $lams = [
            'ban-pt' => 'BAN-PT (9 Kriteria)',
            'lam-emba' => 'LAMEMBA (Akreditasi Bisnis)',
            'lam-infokom' => 'LAM-INFOKOM (Komputasi)',
            'lam-ptkes' => 'LAM-PTKes (Health)'
        ];

        return $lams[$this->prodi->lam_type] ?? strtoupper($this->prodi->lam_type);
    }
}
