<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiGuidance extends Model
{
    protected $fillable = [
        'prodi_id',
        'kriteria_id',
        'guidance',
        'last_generated_at'
    ];

    protected $casts = [
        'guidance' => 'array',
        'last_generated_at' => 'datetime'
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }
}
