<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\Dokumen;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
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
            'lamType' => ($lamType === 'lam-emba' ? 'LAMEMBA' : 'LAM-INFOKOM 2.1'),
            'settings' => \App\Models\Setting::first(),
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
            'lamType' => ($lamType === 'lam-emba' ? 'LAMEMBA' : 'LAM-INFOKOM 2.1'),
            'settings' => \App\Models\Setting::first(),
        ]);

        $filename = ($lamType === 'lam-emba' ? 'DED_' : 'LED_') . strtoupper($prodi->nama) . '_' . now()->format('Ymd') . '.pdf';
        return $pdf->download($filename);
    }

    public function previewLkps()
    {
        $prodiId = session('selected_prodi_id');
        $prodi = \App\Models\Prodi::find($prodiId) ?? auth()->user()->prodi ?? \App\Models\Prodi::first();
        $lamType = $prodi->lam_type ?? 'ban-pt';
        
        $reportNames = [
            'lam-teknik' => [
                'title' => 'LAPORAN KINERJA PROGRAM STUDI (LKPS)',
                'subtitle' => 'PENDIDIKAN TEKNIK',
                'doc_code' => 'LKPS'
            ],
            'lam-infokom' => [
                'title' => 'DAFTAR ISIAN KUANTITATIF (DIK)',
                'subtitle' => 'BIDANG INFORMATIKA DAN KOMPUTER',
                'doc_code' => 'DIK'
            ],
            'lam-emba' => [
                'title' => 'DOKUMEN EVALUASI KINERJA (DEK)',
                'subtitle' => 'BIDANG EKONOMI, MANAJEMEN, BISNIS, DAN AKUNTANSI',
                'doc_code' => 'DEK'
            ],
            'ban-pt' => [
                'title' => 'LAPORAN KINERJA PROGRAM STUDI (LKPS)',
                'subtitle' => 'INSTRUMEN AKREDITASI 9 KRITERIA',
                'doc_code' => 'LKPS'
            ]
        ];

        $config = $reportNames[$lamType] ?? $reportNames['ban-pt'];

        $tables = \App\Models\LamTable::where('lam_type', $lamType)
            ->orderBy('id')
            ->get();

        // Determine specific view or fallback to generic
        $viewName = 'reports.lkps_' . str_replace('-', '_', $lamType);
        if (!view()->exists($viewName)) {
            $viewName = 'reports.lkps';
        }

        $pdf = Pdf::loadView($viewName, [
            'prodi' => $prodi,
            'tables' => $tables,
            'lamType' => $lamType,
            'reportConfig' => $config,
            'date' => now()->format('d F Y'),
            'settings' => \App\Models\Setting::first(),
        ])->setPaper('a4', 'landscape');

        return $pdf->stream();
    }

    public function downloadLkps()
    {
        $prodiId = session('selected_prodi_id');
        $prodi = \App\Models\Prodi::find($prodiId) ?? auth()->user()->prodi ?? \App\Models\Prodi::first();
        $lamType = $prodi->lam_type ?? 'ban-pt';

        $reportNames = [
            'lam-teknik' => ['title' => 'LKPS', 'doc_code' => 'LKPS'],
            'lam-infokom' => ['title' => 'DIK', 'doc_code' => 'DIK'],
            'lam-emba' => ['title' => 'DEK', 'doc_code' => 'DEK'],
            'ban-pt' => ['title' => 'LKPS', 'doc_code' => 'LKPS'],
        ];
        
        $config = $reportNames[$lamType] ?? $reportNames['ban-pt'];

        $tables = \App\Models\LamTable::where('lam_type', $lamType)
            ->orderBy('id')
            ->get();

        // Determine specific view or fallback to generic
        $viewName = 'reports.lkps_' . str_replace('-', '_', $lamType);
        if (!view()->exists($viewName)) {
            $viewName = 'reports.lkps';
        }

        $pdf = Pdf::loadView($viewName, [
            'prodi' => $prodi,
            'tables' => $tables,
            'lamType' => $lamType,
            'reportConfig' => (isset($reportNames[$lamType]) ? array_merge($reportNames[$lamType], ['subtitle' => '']) : $reportNames['ban-pt']),
            'date' => now()->format('d F Y'),
            'settings' => \App\Models\Setting::first(),
        ])->setPaper('a4', 'landscape');

        $filename = $config['doc_code'] . '_' . strtoupper($prodi->nama) . '_' . now()->format('Ymd') . '.pdf';
        return $pdf->download($filename);
    }

    public function downloadLkpsDocx()
    {
        $prodiId = session('selected_prodi_id');
        $prodi = \App\Models\Prodi::find($prodiId) ?? auth()->user()->prodi ?? \App\Models\Prodi::first();
        $lamType = $prodi->lam_type ?? 'ban-pt';

        $tables = \App\Models\LamTable::where('lam_type', $lamType)
            ->orderBy('id')
            ->get();

        $viewName = 'reports.lkps_' . str_replace('-', '_', $lamType);
        if (!view()->exists($viewName)) {
            $viewName = 'reports.lkps';
        }

        $reportNames = [
            'lam-teknik' => ['title' => 'LKPS', 'doc_code' => 'LKPS'],
            'lam-infokom' => ['title' => 'DIK', 'doc_code' => 'DIK'],
            'lam-emba' => ['title' => 'DEK', 'doc_code' => 'DEK'],
            'ban-pt' => ['title' => 'LKPS', 'doc_code' => 'LKPS'],
        ];
        $config = $reportNames[$lamType] ?? $reportNames['ban-pt'];

        $settings = \App\Models\Setting::first();

        $html = view($viewName, [
            'prodi' => $prodi,
            'tables' => $tables,
            'lamType' => $lamType,
            'reportConfig' => (isset($reportNames[$lamType]) ? array_merge($reportNames[$lamType], ['subtitle' => '']) : $reportNames['ban-pt']),
            'date' => now()->format('d F Y'),
            'isDocx' => true,
            'settings' => $settings
        ])->render();

        // Sanitize for Word XML compatibility
        $html = str_replace(['&nbsp;', '&nbsp'], ' ', $html);
        $html = preg_replace('/<!--(.|\s)*?-->/', '', $html); 
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'); // Encode all special chars
        $html = trim($html);

        $filename = 'LKPS_' . strtoupper($prodi->nama) . '_' . now()->format('Ymd') . '.doc'; // Word can open .doc HTML files perfectly
        
        return response($html)
            ->header('Content-Type', 'application/msword')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function downloadLedDocx()
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
        
        $settings = \App\Models\Setting::first();

        $html = view('reports.led', [
            'prodi' => $prodi,
            'kriterias' => $kriterias,
            'date' => now()->format('d F Y'),
            'lamType' => ($lamType === 'lam-emba' ? 'LAMEMBA' : 'LAM-INFOKOM 2.1'),
            'isDocx' => true,
            'settings' => $settings
        ])->render();

        // Sanitize for Word XML compatibility
        $html = str_replace(['&nbsp;', '&nbsp'], ' ', $html);
        $html = preg_replace('/<!--(.|\s)*?-->/', '', $html); 
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'); // Encode all special chars
        $html = trim($html);

        $filename = ($lamType === 'lam-emba' ? 'DED_' : 'LED_') . strtoupper($prodi->nama) . '_' . now()->format('Ymd') . '.doc';
        
        return response($html)
            ->header('Content-Type', 'application/msword')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
