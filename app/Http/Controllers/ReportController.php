<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\Dokumen;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function previewLed()
    {
        $prodiId = session('selected_prodi_id');
        $prodi = \App\Models\Prodi::find($prodiId) ?? auth()->user()->prodi ?? \App\Models\Prodi::first();
        
        $lamType = $prodi->lam_type ?? 'ban-pt';
        $kriterias = Kriteria::where('lam_type', $lamType)
            ->with(['narasis' => function($q) use ($prodiId) {
                $q->where('prodi_id', $prodiId);
            }])
            ->orderBy('kode', 'asc')
            ->get();
        
        $pdf = Pdf::loadView('reports.led', [
            'prodi' => $prodi,
            'kriterias' => $kriterias,
            'date' => now()->format('d F Y'),
            'lamType' => ($lamType === 'lam-emba' ? 'LAMEMBA' : 'LAM-INFOKOM 2.1')
        ]);

        return $pdf->stream();
    }

    public function downloadLed()
    {
        $prodiId = session('selected_prodi_id');
        $prodi = \App\Models\Prodi::find($prodiId) ?? auth()->user()->prodi ?? \App\Models\Prodi::first();
        
        $lamType = $prodi->lam_type ?? 'ban-pt';
        $kriterias = Kriteria::where('lam_type', $lamType)
            ->with(['narasis' => function($q) use ($prodiId) {
                $q->where('prodi_id', $prodiId);
            }])
            ->orderBy('kode', 'asc')
            ->get();
        
        $pdf = Pdf::loadView('reports.led', [
            'prodi' => $prodi,
            'kriterias' => $kriterias,
            'date' => now()->format('d F Y'),
            'lamType' => ($lamType === 'lam-emba' ? 'LAMEMBA' : 'LAM-INFOKOM 2.1')
        ]);

        $filename = ($lamType === 'lam-emba' ? 'DED_' : 'LED_') . strtoupper($prodi->nama) . '_' . now()->format('Ymd') . '.pdf';
        return $pdf->download($filename);
    }
}
