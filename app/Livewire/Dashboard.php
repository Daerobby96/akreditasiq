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

    // Chart data properties
    public $scoreTrendData;
    public $statusDistributionData;
    public $criteriaProgressData;
    public $workflowActivityData;
    public $aiAnalysisHeatmap;

    public function mount()
    {
        $prodiId = session('selected_prodi_id');
        $prodi = \App\Models\Prodi::find($prodiId) ?? auth()->user()->prodi ?? \App\Models\Prodi::first();

        $this->loadBasicStats($prodi);
        $this->loadChartData($prodi);
    }

    protected function loadBasicStats($prodi)
    {
        $this->totalCriteria = Kriteria::where('lam_type', $prodi->lam_type ?? 'sarjana')->count();
        $this->totalDocs = Dokumen::where('prodi_id', $prodi->id)->count();

        $scores = PenilaianAi::whereHas('dokumen', function($q) use ($prodi) {
            $q->where('prodi_id', $prodi->id);
        })->pluck('skor');

        $this->avgScore = $scores->isNotEmpty() ? number_format($scores->avg(), 2) : 0;

        $completedCriteria = Dokumen::where('prodi_id', $prodi->id)
            ->where('status', 'approved')
            ->distinct('kriteria_id')
            ->count('kriteria_id');

        $this->progressPercentage = $this->totalCriteria > 0
            ? round(($completedCriteria / $this->totalCriteria) * 100)
            : 0;
    }

    protected function loadChartData($prodi)
    {
        $analyticsService = new AnalyticsService();

        $this->scoreTrendData = $analyticsService->getScoreTrendData($prodi);
        $this->statusDistributionData = $analyticsService->getStatusDistributionData($prodi);
        $this->criteriaProgressData = $analyticsService->getCriteriaProgressData($prodi);
        $this->workflowActivityData = $analyticsService->getWorkflowActivityData($prodi);
        $this->aiAnalysisHeatmap = $analyticsService->getAiAnalysisHeatmap($prodi);
    }



    public function render()
    {
        return view('livewire.dashboard')->layout('layouts.app');
    }
}
