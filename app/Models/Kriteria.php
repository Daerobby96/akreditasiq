<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    protected $fillable = ['kode', 'nama', 'deskripsi', 'template_narasi', 'bobot', 'lam_type'];

    protected $casts = [
        'template_narasi' => 'array'
    ];

    public function dokumens()
    {
        return $this->hasMany(Dokumen::class);
    }
}
