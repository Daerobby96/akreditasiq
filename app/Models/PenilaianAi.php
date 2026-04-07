<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianAi extends Model
{
    protected $fillable = [
        'dokumen_id',
        'skor',
        'analisis_teks',
        'gap_analysis',
        'rekomendasi',
        'engine',
        'raw_response'
    ];

    protected $casts = [
        'raw_response' => 'array'
    ];

    public function dokumen()
    {
        return $this->belongsTo(Dokumen::class);
    }
}
