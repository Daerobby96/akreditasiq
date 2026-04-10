@if(!isset($isDocx))
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>LKPS Preview - {{ $prodi->nama }}</title>
    <style>
        @page { margin: 2cm; }
        body { font-family: "Times New Roman", Times, serif; font-size: 11pt; color: #000; line-height: 1.5; }
        
        .header { text-align: center; margin-bottom: 30px; }
        .header .title { font-size: 14pt; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; }
        .header .subtitle { font-size: 12pt; font-weight: bold; }
        
        .info-section { margin-bottom: 25px; }
        .info-table { width: 100%; border: none; }
        .info-table td { padding: 2px 0; vertical-align: top; }
        .info-table td.label { width: 200px; font-weight: bold; }
        
        .table-block { margin-bottom: 35px; }
        .table-label { font-weight: bold; margin-bottom: 8px; font-size: 11pt; text-align: left; }
        .table-desc { font-size: 10pt; margin-bottom: 10px; text-align: justify; }
        
        table.data-table { width: 100%; border-collapse: collapse; table-layout: fixed; word-wrap: break-word; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 6px 4px; font-size: 9pt; }
        table.data-table th { background-color: #eee; font-weight: bold; text-align: center; vertical-align: middle; }
        table.data-table td { vertical-align: top; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .footer { position: fixed; bottom: -1cm; left: 0; right: 0; height: 1cm; text-align: right; font-size: 9pt; border-top: 0.5px solid #ccc; padding-top: 5px; }
    </style>
</head>
<body>
@endif
    <div class="header">
        <div class="title">{{ $reportConfig['title'] }}</div>
        <div class="subtitle">{{ $reportConfig['subtitle'] }}</div>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="label">Perguruan Tinggi</td>
                <td>: {{ $settings->nama_institusi ?? 'Universitas Akreditasi Indonesia' }}</td>
            </tr>
            <tr>
                <td class="label">Unit Pengelola</td>
                <td>: {{ $prodi->fakultas ?? 'Fakultas Teknik' }}</td>
            </tr>
            <tr>
                <td class="label">Program Studi</td>
                <td>: {{ $prodi->nama }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Akreditasi</td>
                <td>: {{ strtoupper($lamType) }}</td>
            </tr>
        </table>
    </div>

    @foreach($tables as $table)
        <div class="table-block">
            @if($table->instruction)
                <div class="table-desc" style="margin-bottom: 5px; color: #333;">{{ $table->instruction }}</div>
            @endif
            
            <div class="table-label">{{ $table->label }}</div>

            @php
                $allCols = $table->columns;
                $rootColumns = $allCols->whereNull('parent_id')->sortBy('sort_order');
                $data = \App\Models\LkpsData::where('prodi_id', $prodi->id)->where('lam_table_id', $table->id)->orderBy('sort_order')->get();

                // Recursive helper for leaf columns (identical to web)
                $getLeafs = function($cols) use (&$getLeafs, $allCols) {
                    $leafs = collect();
                    foreach($cols as $col) {
                        $children = $allCols->where('parent_id', $col->id)->sortBy('sort_order');
                        if ($children->isEmpty()) {
                            $leafs->push($col);
                        } else {
                            $leafs = $leafs->concat($getLeafs($children));
                        }
                    }
                    return $leafs;
                };
                $leafColumns = $getLeafs($rootColumns);

                // Pre-calculate depth (identical to web)
                $maxDepth = 0;
                foreach($allCols as $col) {
                    $d = 1;
                    $temp = $col;
                    while($temp && $temp->parent_id) { 
                        $d++; 
                        $temp = $allCols->where('id', $temp->parent_id)->first(); 
                    }
                    $col->depth = $d;
                    $maxDepth = max($maxDepth, $d);
                }
            @endphp

            <table class="data-table" @if(isset($isDocx)) border="1" @endif>
                <thead>
                    @for($step = 1; $step <= $maxDepth; $step++)
                        <tr style="background-color: #f3f4f6; color: #000000;">
                            @foreach($allCols->where('depth', $step)->sortBy('sort_order') as $column)
                                @php
                                    $childrenCnt = $allCols->where('parent_id', $column->id)->count();
                                    $rowspan = ($childrenCnt === 0) ? ($maxDepth - $step + 1) : 1;
                                    $colspan = $column->colspan;
                                @endphp
                                <th rowspan="{{ $rowspan }}" 
                                    colspan="{{ $colspan }}"
                                    style="padding: 10px; border: 1px solid #9ca3af; text-align: center; font-size: 10px; font-weight: bold; text-transform: uppercase; color: #000000;">
                                    {{ $column->label ?: $column->header_name }}
                                </th>
                            @endforeach
                        </tr>
                    @endfor
                    <tr class="bg-gray-100 italic">
                        @foreach($leafColumns as $index => $col)
                            <th style="font-size: 8pt; background: #fafafa; border: 1px solid #9ca3af; text-align: center;">({{ $index + 1 }})</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                        @php
                            $isTotalRow = false;
                            foreach($row->data_values as $val) {
                                if (is_string($val) && (stripos($val, 'jumlah') !== false || stripos($val, 'total') !== false)) {
                                    $isTotalRow = true; break;
                                }
                            }
                        @endphp
                        <tr style="{{ $isTotalRow ? 'font-weight: 900; background-color: #f1f5f9; color: #1e1b4b;' : '' }}">
                            @foreach($leafColumns as $col)
                                @php $val = $row->data_values[$col->field_name] ?? ''; @endphp
                                <td class="{{ in_array($col->data_type, ['number', 'currency']) ? 'text-right' : '' }}">
                                    @if($table->slug === 'tabel_1_a_4_ewmp' && $col->field_name === 'total')
                                        @php
                                            $val = ((float)($row->data_values['sks_ajar_ps'] ?? 0) + (float)($row->data_values['sks_ajar_luar_ps'] ?? 0) + (float)($row->data_values['sks_ajar_luar_pt'] ?? 0) + (float)($row->data_values['sks_riset'] ?? 0) + (float)($row->data_values['sks_pkm'] ?? 0) + (float)($row->data_values['sks_man_pt'] ?? 0) + (float)($row->data_values['sks_man_luar_pt'] ?? 0)) / 2;
                                        @endphp
                                        <strong>{{ str_replace(',00', '', number_format($val, 2, ',', '.')) }}</strong>
                                    @elseif(($val && $val != '0') && ($col->data_type === 'boolean' || (str_starts_with($col->field_name, 'pl') && in_array($table->slug, ['tabel_2_b_1_pembelajaran', 'tabel_2_b_2_pemetaan_cpl'])) || (in_array($col->field_name, ['ts', 'ts1', 'ts2']) && in_array($table->slug, ['tabel_3_c_2_publikasi_riset', 'tabel_3_c_3_hki_riset', 'tabel_4_c_3_hki_pkm']))))
                                        <div style="text-align: center;">v</div>
                                    @elseif($col->data_type === 'currency')
                                        {{ ($val && $val != '0') ? number_format((float)$val, 0, ',', '.') : '' }}
                                    @elseif($col->data_type === 'number')
                                        {{ is_numeric($val) && $val !== '' ? str_replace(',00', '', number_format((float)$val, 2, ',', '.')) : $val }}
                                    @else
                                        {{ $val }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $leafColumns->count() }}" class="text-center" style="color: #666; font-style: italic; padding: 20px;">
                                Data belum diisi.
                            </td>
                        </tr>
                    @endforelse

                    @if($data->isNotEmpty() && $table->slug !== 'tabel_2_a_3_kondisi_mhs')
                        @php
                            $sumableTypes = ['number', 'currency'];
                            $skipFields = ['no', 'tahun', 'semester', 'periode'];
                            $labelFields = ['jenis', 'sumber', 'penggunaan', 'keterangan', 'urutan', 'unit', 'unit_kerja', 'kategori', 'nama'];
                            $hasSumable = $leafColumns->some(fn($c) => in_array($c->data_type, $sumableTypes) && !in_array($c->field_name, $skipFields));
                            $totalLabelShown = false;
                        @endphp

                        @if($hasSumable)
                            <tr style="background-color: #f3f4f6; font-weight: bold;">
                                @foreach($leafColumns as $col)
                                    <td class="{{ in_array($col->data_type, ['number', 'currency']) ? 'text-right' : '' }}">
                                        @if(in_array($col->field_name, $labelFields) && !$totalLabelShown)
                                            <strong>{{ $table->slug === 'tabel_1_a_4_ewmp' ? 'Jumlah *' : 'Total' }}</strong>
                                            @php $totalLabelShown = true; @endphp
                                        @elseif(in_array($col->data_type, $sumableTypes) && !in_array($col->field_name, $skipFields))
                                            @php
                                                $sum = $data->sum(fn($row) => 
                                                    ($table->slug === 'tabel_1_a_4_ewmp' && $col->field_name === 'total')
                                                    ? (((float)($row->data_values['sks_ajar_ps'] ?? 0) + (float)($row->data_values['sks_ajar_luar_ps'] ?? 0) + (float)($row->data_values['sks_ajar_luar_pt'] ?? 0) + (float)($row->data_values['sks_riset'] ?? 0) + (float)($row->data_values['sks_pkm'] ?? 0) + (float)($row->data_values['sks_man_pt'] ?? 0) + (float)($row->data_values['sks_man_luar_pt'] ?? 0)) / 2)
                                                    : (float)($row->data_values[$col->field_name] ?? 0)
                                                );
                                            @endphp
                                            {{ str_replace(',00', '', number_format($sum, 2, ',', '.')) }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            @if($table->slug === 'tabel_1_a_4_ewmp')
                                @php $totalLabelShown = false; @endphp
                                <tr style="background-color: #f9fafb; font-style: italic;">
                                    @foreach($leafColumns as $col)
                                        <td class="{{ in_array($col->data_type, ['number', 'currency']) ? 'text-right' : '' }}">
                                            @if(in_array($col->field_name, $labelFields) && !$totalLabelShown)
                                                <strong>Rata-rata **</strong>
                                                @php $totalLabelShown = true; @endphp
                                            @elseif(in_array($col->data_type, $sumableTypes) && !in_array($col->field_name, $skipFields))
                                                @php
                                                    $avg = $data->avg(fn($row) => 
                                                        ($col->field_name === 'total')
                                                        ? (((float)($row->data_values['sks_ajar_ps'] ?? 0) + (float)($row->data_values['sks_ajar_luar_ps'] ?? 0) + (float)($row->data_values['sks_ajar_luar_pt'] ?? 0) + (float)($row->data_values['sks_riset'] ?? 0) + (float)($row->data_values['sks_pkm'] ?? 0) + (float)($row->data_values['sks_man_pt'] ?? 0) + (float)($row->data_values['sks_man_luar_pt'] ?? 0)) / 2)
                                                        : (float)($row->data_values[$col->field_name] ?? 0)
                                                    );
                                                @endphp
                                                {{ str_replace(',00', '', number_format($avg, 2, ',', '.')) }}
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endif
                        @endif
                    @endif
                </tbody>
            </table>

            @if($table->keterangan)
                <div class="table-desc" style="margin-top: 10px; font-size: 9pt;">
                    <strong>Keterangan:</strong><br/>
                    {!! nl2br(e($table->keterangan)) !!}
                </div>
            @endif
        </div>
    @endforeach

@if(!isset($isDocx))
    <div class="footer">
        Halaman {{ '{PAGE_NUM}' }} dari {{ '{PAGE_COUNT}' }} | AKRE System - {{ date('d/m/Y') }}
    </div>
</body>
</html>
@endif
