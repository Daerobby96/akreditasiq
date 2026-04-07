<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    protected $fillable = [
        'nama',
        'kode',
        'jenjang',
        'lam_type',
    ];

    public function dokumens()
    {
        return $this->hasMany(Dokumen::class);
    }
}
