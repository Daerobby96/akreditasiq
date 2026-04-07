<!DOCTYPE html>
<html>
<head>
    <title>Laporan Akreditasi Cerdas</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.5; font-size: 12px; }
        .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 20px; margin-bottom: 30px; }
        .title { font-size: 24px; font-weight: bold; color: #4f46e5; margin: 0; }
        .subtitle { font-size: 14px; color: #666; }
        .stats { margin-bottom: 30px; padding: 20px; background: #f8fafc; border-radius: 10px; }
        .score-box { text-align: center; }
        .score-val { font-size: 36px; font-weight: bold; color: #4f46e5; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table th, .table td { padding: 12px; border: 1px solid #e2e8f0; text-align: left; }
        .table th { background: #f1f5f9; color: #475569; font-weight: bold; }
        .kriteria-row { background: #f8fafc; font-weight: bold; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; }
        .badge-success { background: #dcfce7; color: #166534; }
        .ai-section { margin-top: 10px; font-size: 11px; padding: 10px; background: #fdfdfd; border-left: 3px solid #6366f1; }
        .footer { text-align: center; font-size: 10px; color: #94a3b8; position: fixed; bottom: 0; width: 100%; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">LAPORAN AKREDITASI CERDAS</h1>
        <p class="subtitle">Analisis Kinerja dan Pemenuhan Standar Institusi</p>
        <p class="subtitle">Tanggal Laporan: {{ $date }}</p>
    </div>

    <div class="stats">
        <div class="score-box">
            <div style="font-size: 12px; color: #64748b; text-transform: uppercase;">Skor Rata-rata Penilaian AI</div>
            <div class="score-val">{{ number_format($avgScore, 2) }} / 4.00</div>
        </div>
    </div>

    <h3 style="color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">Ringkasan per Kriteria</h3>
    <table class="table">
        <thead>
            <tr>
                <th width="10%">Kode</th>
                <th width="40%">Kriteria</th>
                <th width="20%">Status</th>
                <th width="15%">Dokumen</th>
                <th width="15%">AI Score</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kriterias as $k)
                <tr class="kriteria-row">
                    <td align="center">{{ $k->kode }}</td>
                    <td>{{ $k->nama }}</td>
                    <td>
                        @if($k->dokumens->count() > 0)
                            <span class="badge badge-success">TERDATA</span>
                        @else
                            <span style="color: #94a3b8">BELUM ADA DATA</span>
                        @endif
                    </td>
                    <td align="center">{{ $k->dokumens->count() }}</td>
                    <td align="center">
                        @php $s = $k->dokumens->flatMap->penilaian_ai->avg('skor'); @endphp
                        {{ $s ? number_format($s, 2) : '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <h3 style="color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">Detail Analisis & Gap Analysis</h3>
    @foreach($kriterias as $k)
        @foreach($k->dokumens as $d)
            @if($d->penilaian_ai->isNotEmpty())
                <div style="margin-bottom: 25px; padding: 15px; border: 1px solid #f1f5f9; border-radius: 8px;">
                    <div style="font-weight: bold; font-size: 13px; color: #4f46e5;">{{ $k->kode }}: {{ $k->nama }}</div>
                    <div style="font-size: 11px; color: #64748b; margin-bottom: 10px;">File: {{ $d->nama_file }} | Skor: {{ number_format($d->penilaian_ai->first()->skor, 2) }}</div>
                    
                    <div class="ai-section">
                        <strong>Analisis:</strong> {{ $d->penilaian_ai->first()->analisis_teks }}
                        <br><br>
                        <strong>Gap Analysis:</strong> {{ $d->penilaian_ai->first()->gap_analysis }}
                        <br><br>
                        <strong>Rekomendasi:</strong> {{ $d->penilaian_ai->first()->rekomendasi }}
                    </div>
                </div>
            @endif
        @endforeach
    @endforeach

    <div class="footer">
        © {{ date('Y') }} Sistem Akreditasi Cerdas - Didukung oleh Teknologi LLM (OpenAI/Claude)
    </div>
</body>
</html>
