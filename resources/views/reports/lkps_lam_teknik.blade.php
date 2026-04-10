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
        .info-table td { padding: 4px 0; vertical-align: top; }
        .info-table td.label { width: 200px; font-weight: bold; }
        .table-block { margin-bottom: 35px; page-break-inside: avoid; }
        .table-label { font-weight: bold; margin-bottom: 8px; font-size: 11pt; text-align: left; }
        
        table.data-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 8px 6px; font-size: 9pt; }
        table.data-table th { background-color: #f3f4f6; font-weight: bold; text-align: center; vertical-align: middle; }
        
        .keterangan-box { margin-top: 15px; font-size: 9pt; text-align: justify; border-top: 1px solid #eee; padding-top: 10px; }
        .instruction-text { font-size: 10pt; font-style: italic; margin-bottom: 8px; color: #444; }
        
        @if(!isset($isDocx))
        .footer { position: fixed; bottom: -1cm; left: 0; right: 0; height: 1cm; text-align: right; font-size: 9pt; border-top: 0.5px solid #ccc; padding-top: 5px; }
        @endif
    </style>
</head>
<body>
    <div class="header">
        <div class="title">LAPORAN KINERJA PROGRAM STUDI (LKPS)</div>
        <div class="subtitle">LAM TEKNIK - {{ $prodi->jenjang }}</div>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr><td class="label">Perguruan Tinggi</td><td>: {{ $settings->nama_institusi ?? 'Universitas Akreditasi Indonesia' }}</td></tr>
            <tr><td class="label">Unit Pengelola</td><td>: {{ $prodi->fakultas ?? 'Fakultas' }}</td></tr>
            <tr><td class="label">Program Studi</td><td>: {{ $prodi->nama }}</td></tr>
        </table>
    </div>

    @foreach($tables as $table)
        <div class="table-block">
            {{-- Instruksi & Keterangan Berbasis Slug Tabel --}}
            @if($table->slug == 'tabel_1_vmts')
                <div class="instruction-text">Tuliskan pernyataan Visi, Misi, Tujuan dan Strategi (VMTS) Perguruan Tinggi (PT) dan Unit Pengelola Program Studi (UPPS) serta visi keilmuan program studi (PS) dengan mengikuti format Tabel 1 berikut ini</div>
            @endif

            <div class="table-label">{{ $table->label }}</div>

            @php
                $rootColumns = $table->columns()->whereNull('parent_id')->with('children')->get();
                $leafColumns = $table->columns()->whereDoesntHave('children')->get();
                $hasChildren = $rootColumns->some(fn($c) => $c->children->isNotEmpty());
                $data = \App\Models\LkpsData::where('prodi_id', $prodi->id)->where('lam_table_id', $table->id)->get();
            @endphp
            
            <table class="data-table" @if(isset($isDocx)) border="1" @endif>
                <thead>
                    <tr>
                        @foreach($rootColumns as $column)
                            <th rowspan="{{ $column->children->isEmpty() && $hasChildren ? 2 : 1 }}" colspan="{{ $column->colspan ?: 1 }}">
                                {{ $column->header_name }}
                            </th>
                        @endforeach
                    </tr>
                    @if($hasChildren)
                        <tr>
                            @foreach($rootColumns as $column)
                                @foreach($column->children as $child)
                                    <th>{{ $child->header_name }}</th>
                                @endforeach
                            @endforeach
                        </tr>
                    @endif
                </thead>
                <tbody>
                    @forelse($data as $row)
                        <tr>
                            @foreach($leafColumns as $col)
                                @php $val = $row->data_values[$col->field_name] ?? ''; @endphp
                                <td class="{{ in_array($col->data_type, ['number', 'currency']) ? 'text-right' : '' }}">
                                    {{ in_array($col->data_type, ['currency']) ? number_format((float)$val, 0, ',', '.') : ($val ?: '-') }}
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="{{ $leafColumns->count() }}" style="text-align: center; font-style: italic;">Data belum diisi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endforeach

@if(!isset($isDocx))
    <div class="footer">
        Halaman {{ '{PAGE_NUM}' }} dari {{ '{PAGE_COUNT}' }}
    </div>
@endif
</body>
</html>
