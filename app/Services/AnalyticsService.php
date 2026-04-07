<?php

namespace App\Services;

use App\Models\Dokumen;
use App\Models\Kriteria;
use App\Models\PenilaianAi;
use App\Models\Workflow;
use App\Models\Prodi;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Get score trend data for the last 6 months
     */
    public function getScoreTrendData(Prodi $prodi): array
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $avgScore = PenilaianAi::whereHas('dokumen', function($q) use ($prodi) {
                $q->where('prodi_id', $prodi->id);
            })
            ->whereBetween('created_at', [
                $date->startOfMonth(),
                $date->endOfMonth()
            ])
            ->avg('skor') ?? 0;

            $data[] = [
                'month' => $date->format('M Y'),
                'score' => round($avgScore, 2),
                'date' => $date->format('Y-m')
            ];
        }

        return $data;
    }

    /**
     * Get document status distribution
     */
    public function getStatusDistributionData(Prodi $prodi): array
    {
        $statuses = Dokumen::where('prodi_id', $prodi->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->map(function($item) {
                return [
                    'status' => $this->getStatusLabel($item->status),
                    'count' => $item->count,
                    'color' => $this->getStatusColor($item->status),
                    'percentage' => 0 // Will be calculated later
                ];
            });

        // Calculate percentages
        $total = $statuses->sum('count');
        $statuses = $statuses->map(function($item) use ($total) {
            $item['percentage'] = $total > 0 ? round(($item['count'] / $total) * 100, 1) : 0;
            return $item;
        });

        return $statuses->toArray();
    }

    /**
     * Get criteria progress data
     */
    public function getCriteriaProgressData(Prodi $prodi): array
    {
        $kriteria = Kriteria::where('lam_type', $prodi->lam_type ?? 'sarjana')->get();

        $data = $kriteria->map(function($kriterium) use ($prodi) {
            $totalDocs = Dokumen::where('prodi_id', $prodi->id)
                ->where('kriteria_id', $kriterium->id)
                ->count();

            $approvedDocs = Dokumen::where('prodi_id', $prodi->id)
                ->where('kriteria_id', $kriterium->id)
                ->where('status', 'approved')
                ->count();

            $avgScore = PenilaianAi::whereHas('dokumen', function($q) use ($prodi, $kriterium) {
                $q->where('prodi_id', $prodi->id)
                  ->where('kriteria_id', $kriterium->id);
            })->avg('skor') ?? 0;

            $progress = $totalDocs > 0 ? round(($approvedDocs / $totalDocs) * 100) : 0;

            return [
                'kriteria' => $kriterium->kode,
                'nama' => $kriterium->nama,
                'progress' => $progress,
                'total_docs' => $totalDocs,
                'approved_docs' => $approvedDocs,
                'avg_score' => round($avgScore, 2),
                'color' => $this->getProgressColor($progress)
            ];
        });

        return $data->toArray();
    }

    /**
     * Get workflow activity data for the last 7 days
     */
    public function getWorkflowActivityData(Prodi $prodi): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);

            $activities = Workflow::whereHas('trackable', function($q) use ($prodi) {
                $q->where('prodi_id', $prodi->id);
            })
            ->whereDate('created_at', $date)
            ->count();

            $data[] = [
                'date' => $date->format('D'),
                'full_date' => $date->format('Y-m-d'),
                'activities' => $activities,
                'formatted_date' => $date->format('j M')
            ];
        }

        return $data;
    }

    /**
     * Get AI analysis score distribution (heatmap style)
     */
    public function getAiAnalysisHeatmap(Prodi $prodi): array
    {
        $scores = PenilaianAi::whereHas('dokumen', function($q) use ($prodi) {
            $q->where('prodi_id', $prodi->id);
        })->pluck('skor');

        $distribution = [
            '1.0-1.9' => 0,
            '2.0-2.9' => 0,
            '3.0-3.9' => 0,
            '4.0' => 0
        ];

        foreach ($scores as $score) {
            if ($score >= 4.0) {
                $distribution['4.0']++;
            } elseif ($score >= 3.0) {
                $distribution['3.0-3.9']++;
            } elseif ($score >= 2.0) {
                $distribution['2.0-2.9']++;
            } else {
                $distribution['1.0-1.9']++;
            }
        }

        return collect($distribution)->map(function($count, $range) use ($distribution) {
            return [
                'range' => $range,
                'count' => $count,
                'color' => $this->getScoreColor($range),
                'percentage' => $this->calculateScorePercentage($range, $distribution)
            ];
        })->values()->toArray();
    }

    /**
     * Get accreditation insights and recommendations
     */
    public function getAccreditationInsights(Prodi $prodi): array
    {
        $insights = [];

        // Average score insight
        $avgScore = PenilaianAi::whereHas('dokumen', function($q) use ($prodi) {
            $q->where('prodi_id', $prodi->id);
        })->avg('skor') ?? 0;

        if ($avgScore >= 3.5) {
            $insights[] = [
                'type' => 'success',
                'message' => "Skor rata-rata AI sangat baik ({$avgScore}/4.0). Kualitas dokumen sudah memenuhi standar tinggi."
            ];
        } elseif ($avgScore >= 3.0) {
            $insights[] = [
                'type' => 'warning',
                'message' => "Skor rata-rata AI cukup baik ({$avgScore}/4.0). Perlu perbaikan pada beberapa aspek."
            ];
        } else {
            $insights[] = [
                'type' => 'danger',
                'message' => "Skor rata-rata AI perlu ditingkatkan ({$avgScore}/4.0). Fokus pada perbaikan dokumen."
            ];
        }

        // Progress insight
        $totalCriteria = Kriteria::where('lam_type', $prodi->lam_type ?? 'sarjana')->count();
        $completedCriteria = Dokumen::where('prodi_id', $prodi->id)
            ->where('status', 'approved')
            ->distinct('kriteria_id')
            ->count('kriteria_id');

        $progressPercent = $totalCriteria > 0 ? round(($completedCriteria / $totalCriteria) * 100) : 0;

        if ($progressPercent >= 80) {
            $insights[] = [
                'type' => 'success',
                'message' => "Progress akreditasi sangat baik ({$progressPercent}%). Hampir semua kriteria terpenuhi."
            ];
        } elseif ($progressPercent >= 60) {
            $insights[] = [
                'type' => 'info',
                'message' => "Progress akreditasi cukup baik ({$progressPercent}%). Terus lengkapi dokumen yang tersisa."
            ];
        } else {
            $insights[] = [
                'type' => 'warning',
                'message' => "Progress akreditasi perlu ditingkatkan ({$progressPercent}%). Fokus pada pengumpulan dokumen."
            ];
        }

        // Low-scoring criteria insight
        $lowScoringCriteria = $this->getCriteriaProgressData($prodi);
        $lowScoring = collect($lowScoringCriteria)->filter(function($item) {
            return $item['avg_score'] < 3.0 && $item['total_docs'] > 0;
        })->take(3);

        if ($lowScoring->isNotEmpty()) {
            $criteriaNames = $lowScoring->pluck('nama')->join(', ');
            $insights[] = [
                'type' => 'warning',
                'message' => "Perhatian khusus pada kriteria: {$criteriaNames}. Skor AI masih perlu ditingkatkan."
            ];
        }

        return $insights;
    }

    // Helper methods
    protected function getStatusLabel($status): string
    {
        return match($status) {
            'draft' => 'Draft',
            'submitted' => 'Diajukan',
            'under_review' => 'Dalam Review',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => ucfirst($status)
        };
    }

    protected function getStatusColor($status): string
    {
        return match($status) {
            'draft' => '#6B7280',
            'submitted' => '#3B82F6',
            'under_review' => '#F59E0B',
            'approved' => '#10B981',
            'rejected' => '#EF4444',
            default => '#6B7280'
        };
    }

    protected function getProgressColor($progress): string
    {
        if ($progress >= 80) return '#10B981';
        if ($progress >= 60) return '#F59E0B';
        return '#EF4444';
    }

    protected function getScoreColor($range): string
    {
        return match($range) {
            '4.0' => '#10B981',
            '3.0-3.9' => '#84CC16',
            '2.0-2.9' => '#F59E0B',
            '1.0-1.9' => '#EF4444',
            default => '#6B7280'
        };
    }

    protected function calculateScorePercentage($range, $distribution): float
    {
        $total = array_sum($distribution);
        return $total > 0 ? round(($distribution[$range] / $total) * 100, 1) : 0;
    }
}