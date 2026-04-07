<?php

namespace App\Livewire;

use App\Models\Dokumen;
use App\Models\Kriteria;
use App\Models\Workflow;
use Livewire\Component;

class Monitoring extends Component
{
    public function render()
    {
        $prodiId = session('selected_prodi_id');
        $prodi = \App\Models\Prodi::find($prodiId) ?? \App\Models\Prodi::first();
        $lamType = $prodi->lam_type ?? 'ban-pt';

        $workflows = Workflow::with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        $kriterias = Kriteria::where('lam_type', $lamType)->withCount([
            'dokumens',
            'dokumens as draft_count' => fn($q) => $q->where('status', 'draft'),
            'dokumens as submitted_count' => fn($q) => $q->where('status', 'submitted'),
            'dokumens as review_count' => fn($q) => $q->where('status', 'review'),
            'dokumens as approved_count' => fn($q) => $q->where('status', 'approved'),
            'dokumens as revision_count' => fn($q) => $q->where('status', 'revision'),
        ])->orderBy('kode', 'asc')->get();

        $statusSummary = [
            'draft' => Dokumen::where('status', 'draft')->count(),
            'submitted' => Dokumen::where('status', 'submitted')->count(),
            'review' => Dokumen::where('status', 'review')->count(),
            'approved' => Dokumen::where('status', 'approved')->count(),
            'revision' => Dokumen::where('status', 'revision')->count(),
        ];
        $totalDocs = array_sum($statusSummary);

        $notifications = \App\Models\Notification::latest()->limit(5)->get();

        return view('livewire.monitoring', [
            'workflows' => $workflows,
            'kriterias' => $kriterias,
            'statusSummary' => $statusSummary,
            'totalDocs' => $totalDocs,
            'notifications' => $notifications,
            'prodis' => \App\Models\Prodi::withCount('dokumens')->get()
        ])->layout('layouts.app');
    }
}
