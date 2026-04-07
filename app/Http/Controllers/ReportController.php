<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\Dokumen;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function download()
    {
        $prodiId = session('selected_prodi_id');
        $prodi = \App\Models\Prodi::find($prodiId) ?? auth()->user()->prodi ?? \App\Models\Prodi::first();
        
        $lamType = $prodi->lam_type ?? 'ban-pt';
        $kriterias = Kriteria::where('lam_type', $lamType)->with(['dokumens.penilaian_ai'])->get();
        
        $totalScore = 0;
        $count = 0;
        
        foreach($kriterias as $k) {
            foreach($k->dokumens as $d) {
                if($d->penilaian_ai->isNotEmpty()) {
                    $totalScore += $d->penilaian_ai->first()->skor;
                    $count++;
                }
            }
        }
        
        $avgScore = $count > 0 ? $totalScore / $count : 0;

        $pdf = Pdf::loadView('reports.accreditation', [
            'kriterias' => $kriterias,
            'avgScore' => $avgScore,
            'date' => now()->format('d F Y')
        ]);

        return $pdf->download('Laporan_Akreditasi_Cerdas_' . now()->format('Ymd') . '.pdf');
    }
}
