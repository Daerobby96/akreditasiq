<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Evaluasi Diri (LED) - {{ $prodi->nama }}</title>
    <style>
        @page {
            margin: 2.5cm 2.5cm 3cm 2.5cm;
        }
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.5;
            color: #000;
            font-size: 11pt;
            text-align: justify;
        }
        .header-text {
            @if(!isset($isDocx))
            position: fixed;
            top: -1.5cm;
            @endif
            left: 0;
            right: 0;
            font-size: 9pt;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            font-style: italic;
            margin-bottom: 20px;
        }
        .cover {
            text-align: center;
            margin-top: 2cm;
        }
        .cover h1 {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 0.5cm;
        }
        .cover h2 {
            font-size: 14pt;
            margin-bottom: 1cm;
        }
        .logo-placeholder {
            width: 150px;
            height: 150px;
            border: 1px dashed #ccc;
            margin: 2cm auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10pt;
            color: #999;
        }
        .page-break {
            page-break-after: always;
            clear: both;
        }
        .bab-title {
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            margin-top: 50px;
            margin-bottom: 30px;
            text-transform: uppercase;
        }
        .kriteria-title {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 15pt;
            margin-bottom: 10pt;
        }
        .section-label {
            font-weight: bold;
            margin-top: 10pt;
            margin-bottom: 5pt;
        }
        @if(!isset($isDocx))
        .footer {
            position: fixed;
            bottom: -1.5cm;
            left: 0;
            right: 0;
            font-size: 10pt;
            text-align: right;
        }
        @endif
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
        }
        th {
            background-color: #f3f4f6;
        }
    </style>
</head>
<body>
    <div class="header-text">
        @if($lamType === 'LAM-EMBA')
            Dokumen Evaluasi Diri (DED) {{ $settings->nama_institusi ?? 'LAMEMBA' }} - {{ date('Y') }}
        @else
            Laporan Evaluasi Diri (LED) {{ $settings->nama_institusi ?? $lamType }} - {{ date('Y') }}
        @endif
    </div>

    <div class="cover">
        @if($settings && $settings->logo_path)
            <img src="{{ public_path('storage/' . $settings->logo_path) }}" style="height: 120px; margin-bottom: 30px;">
        @else
            <div class="logo-placeholder">LOGO PERGURUAN TINGGI</div>
        @endif

        @if($lamType === 'LAM-EMBA')
            <h1>DOKUMEN EVALUASI DIRI</h1>
            <h1>PENGAJUAN UNTUK STATUS TERAKREDITASI UNGGUL</h1>
        @else
            <h1>LAPORAN EVALUASI DIRI</h1>
            <h1>AKREDITASI PROGRAM STUDI</h1>
        @endif
        
        <h2 style="margin-top: 1cm">PROGRAM STUDI {{ strtoupper($prodi->nama) }}</h2>
        <h2>{{ strtoupper($prodi->fakultas ?? 'FAKULTAS') }}</h2>
        <h2>{{ strtoupper($settings->nama_institusi ?? 'UNIVERSITAS') }}</h2>
        <h2 style="margin-top: 2cm">{{ strtoupper($settings->kota ?? 'KOTA') }}, TAHUN {{ date('Y') }}</h2>
    </div>

    <div class="page-break"></div>

    @if(!isset($isDocx))
    <div class="footer">
        <span class="pagenum"></span>
    </div>
    @endif

    <div class="bab-title">BAGIAN KEDUA<br>
        @if($lamType === 'LAM-EMBA')
            DOKUMEN EVALUASI DIRI
        @else
            STRUKTUR LAPORAN EVALUASI DIRI
        @endif
    </div>
    
    <div class="kriteria-title">
        @if($lamType === 'LAM-EMBA')
            II. DOKUMEN EVALUASI DIRI
        @else
            II. LAPORAN EVALUASI DIRI
        @endif
    </div>
    
    <div class="kriteria-title" style="margin-left: 20pt;">
        @if($lamType === 'LAM-EMBA')
            B. EVALUASI DIRI 7 (TUJUH) KRITERIA
        @else
            C. KRITERIA
        @endif
    </div>

    @foreach($kriterias as $index => $k)
        <div style="margin-left: 20pt;">
            <div class="kriteria-title">
                @if($lamType === 'LAM-EMBA')
                    B.{{ $index + 1 }} {{ strtoupper($k->nama) }}
                @else
                    C.{{ $index + 1 }} {{ strtoupper($k->nama) }}
                @endif
            </div>
            
            @php 
                $narasi = $k->narasis->first();
                $content = $narasi->content ?? [];
                $sections = $k->template_narasi;
                if (!$sections) {
                    if ($lamType === 'LAM-EMBA') {
                        $kode = (string)$k->kode;
                        $embaTemplates = [
                            '1' => ['a' => 'Misi', 'b' => 'Visi', 'c' => 'Tujuan dan Sasaran', 'd' => 'Strategi'],
                            '2' => ['a' => 'Tata Pamong', 'b' => 'Tata Kelola'],
                            '3' => ['a' => 'Penerimaan Mahasiswa', 'b' => 'Layanan Akademik', 'c' => 'Kinerja Akademik', 'd' => 'Kesejahteraan', 'e' => 'Karir'],
                            '4' => ['a' => 'Kecukupan Dosen', 'b' => 'Pengelolaan Dosen', 'c' => 'Tenaga Kependidikan', 'd' => 'Pengelolaan Tendik'],
                            '5' => ['a' => 'Keuangan', 'b' => 'Sarana dan Prasarana'],
                            '6' => ['a' => 'Kurikulum', 'b' => 'Jaminan Pembelajaran'],
                            '7' => ['a' => 'Penelitian', 'b' => 'Pengabdian Masyarakat'],
                        ];
                        $sections = $embaTemplates[$kode] ?? [];
                    } else {
                        $sections = [
                            'penetapan' => '[PENETAPAN]',
                            'pelaksanaan' => '[PELAKSANAAN]',
                            'evaluasi' => '[EVALUASI]',
                            'pengendalian' => '[PENGENDALIAN]',
                            'peningkatan' => '[PENINGKATAN]'
                        ];
                    }
                }
            @endphp

            @foreach($sections as $sKey => $sLabel)
                <div class="section-label">
                    @if($lamType === 'LAM-EMBA')
                        {{ $sKey }}. {{ $sLabel }}
                    @else
                        {{ ($index + 1) . '.' . ($loop->index + 1) }} {{ $sLabel }}
                    @endif
                </div>
                <div class="content">
                    {!! $content[$sKey] ?? '<p><i>(Belum ada narasi)</i></p>' !!}
                </div>
            @endforeach
        </div>
        
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    @if(!isset($isDocx))
    <script type="text/php">
        if (isset($pdf)) {
            $text = "{PAGE_NUM} / {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("Arial");
            $width = $fontMetrics->get_text_width($text, $font, $size);
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
    @endif
</body>
</html>
