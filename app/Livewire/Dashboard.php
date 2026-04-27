<?php

namespace App\Livewire;

use App\Models\Kriteria;
use App\Models\Dokumen;
use App\Models\PenilaianAi;
use App\Services\AnalyticsService;
use Livewire\Component;

class Dashboard extends Component
{
    public $totalCriteria;
    public $totalDocs;
    public $avgScore;
    public $progressPercentage;
    public $predictionRank = 'MENUNGGU DATA';
    public $predictionConfidence = 0;
    public $prodi;

    // Chart data properties
    public $scoreTrendData;
    public $statusDistributionData;
    public $criteriaProgressData;
    public $workflowActivityData;
    public $aiAnalysisHeatmap;
    public $insights = [];
    public $smartResponse = '';
    public $isAsking = false;

    public $totalLkpsTables;
    public $filledLkpsTables;
    public $lkpsProgressPercentage;

    public function mount()
    {
        $prodiId = session('selected_prodi_id');
        $this->prodi = \App\Models\Prodi::find($prodiId) ?? \App\Models\Prodi::first();
        
        $this->loadBasicStats($this->prodi);
        $this->loadChartData($this->prodi);
    }

    protected function loadBasicStats($prodi)
    {
        $this->totalCriteria = Kriteria::where('lam_type', $prodi->lam_type)->count();
        $this->totalDocs = Dokumen::where('prodi_id', $prodi->id)->count();

        // Ambil semua skor AI yang terkait dengan prodi ini
        $scores = PenilaianAi::whereHas('dokumen', function($q) use ($prodi) {
            $q->where('prodi_id', $prodi->id);
        })->pluck('skor');

        $this->avgScore = $scores->isNotEmpty() ? number_format($scores->avg(), 2) : "0.00";

        // Hitler kriteria yang sudah tuntas (Approved)
        $completedCriteria = Dokumen::where('prodi_id', $prodi->id)
            ->where('status', 'approved')
            ->distinct('kriteria_id')
            ->count('kriteria_id');

        $this->progressPercentage = $this->totalCriteria > 0
            ? round(($completedCriteria / $this->totalCriteria) * 100)
            : 0;

        // LKPS Progress Calculation
        $this->totalLkpsTables = \App\Models\LamTable::where('lam_type', $prodi->lam_type)->count();
        $this->filledLkpsTables = \App\Models\LkpsData::where('prodi_id', $prodi->id)
            ->distinct('lam_table_id')
            ->count('lam_table_id');
        
        $this->lkpsProgressPercentage = $this->totalLkpsTables > 0
            ? round(($this->filledLkpsTables / $this->totalLkpsTables) * 100)
            : 0;

        $this->calculatePrediction($this->avgScore, $this->progressPercentage);
            
        // Trigger re-render charts di frontend
        $this->dispatch('contentChanged');
    }

    protected function calculatePrediction($avgScore, $progress)
    {
        if ($progress < 10) {
            $this->predictionRank = 'DATA MINIM';
            $this->predictionConfidence = $progress;
            return;
        }

        $score = (float) $avgScore;

        if ($score >= 3.61) {
            $this->predictionRank = 'UNGGUL';
        } elseif ($score >= 3.01) {
            $this->predictionRank = 'BAIK SEKALI';
        } elseif ($score >= 2.00) {
            $this->predictionRank = 'BAIK';
        } else {
            $this->predictionRank = 'TIDAK TERAKREDITASI';
        }

        // Confidence is a mix of progress and score stability
        $this->predictionConfidence = min(100, round(($progress * 0.7) + (($score / 4) * 30)));
    }

    protected function loadChartData($prodi)
    {
        $analyticsService = new AnalyticsService();

        $this->scoreTrendData = $analyticsService->getScoreTrendData($prodi);
        $this->statusDistributionData = $analyticsService->getStatusDistributionData($prodi);
        $this->criteriaProgressData = $analyticsService->getCriteriaProgressData($prodi);
        $this->workflowActivityData = $analyticsService->getWorkflowActivityData($prodi);
        $this->aiAnalysisHeatmap = $analyticsService->getAiAnalysisHeatmap($prodi);
        $this->insights = $analyticsService->getAccreditationInsights($prodi);
    }

    public function askAssistant(\App\Services\GroqService $groq)
    {
        $this->isAsking = true;
        $this->smartResponse = 'Sedang menganalisis data, mohon tunggu...';

        try {
            $prompt = "Berdasarkan data dashboard saya saat ini (Avg Score: {$this->avgScore}, Progress: {$this->progressPercentage}%), apa prioritas utama yang harus saya kerjakan untuk meningkatkan akreditasi prodi ini? Berikan jawaban singkat, poin-per-poin, dan bahasa yang sangat teknis namun solutif.";

            $messages = [
                ['role' => 'system', 'content' => 'Anda adalah AKRE SMART AI, konsultan akreditasi prodi.'],
                ['role' => 'user', 'content' => $prompt]
            ];

            $response = $groq->chat($messages);
            $this->smartResponse = $response ?: 'Maaf, asisten AI tidak memberikan jawaban. Silakan coba lagi.';
        } catch (\Exception $e) {
            $this->smartResponse = 'Terjadi kesalahan saat menghubungi asisten AI: ' . $e->getMessage();
        }

        $this->isAsking = false;
    }



    public function render()
    {
        return view('livewire.dashboard')->layout('layouts.app');
    }
}
