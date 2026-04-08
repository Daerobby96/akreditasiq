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
        'peringkat_saat_ini',
        'tanggal_kadaluarsa',
        'target_submit',
        'status_akreditasi',
        'target_peringkat',
    ];

    public function dokumens()
    {
        return $this->hasMany(Dokumen::class);
    }
}
