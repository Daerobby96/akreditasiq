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
                $rootColumns = $table->columns()->whereNull('parent_id')->with('children')->get();
                $leafColumns = $table->columns()->whereDoesntHave('children')->get();
                $hasChildren = $rootColumns->some(fn($c) => $c->children->isNotEmpty());
                $data = \App\Models\LkpsData::where('prodi_id', $prodi->id)->where('lam_table_id', $table->id)->get();
            @endphp

            <table class="data-table" @if(isset($isDocx)) border="1" @endif>
                <thead>
                    <tr>
                        @foreach($rootColumns as $column)
                            <th rowspan="{{ $column->children->isEmpty() && $hasChildren ? 2 : 1 }}" 
                                colspan="{{ $column->colspan ?: 1 }}">
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
                    <tr class="bg-gray-100 italic">
                        @foreach($leafColumns as $index => $col)
                            <th style="font-size: 8pt; background: #fafafa">({{ $index + 1 }})</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                        <tr>
                            @foreach($leafColumns as $col)
                                @php $val = $row->data_values[$col->field_name] ?? ''; @endphp
                                <td class="{{ in_array($col->data_type, ['number', 'currency']) ? 'text-right' : '' }}">
                                    @if($col->data_type === 'currency')
                                        {{ number_format((float)$val, 0, ',', '.') }}
                                    @elseif($col->data_type === 'number')
                                        {{ $val }}
                                    @else
                                        {{ $val ?: '-' }}
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
