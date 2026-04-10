<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /** @use HasFactory<\Database\Factories\SettingFactory> */
    use HasFactory;

    protected $fillable = [
        'nama_institusi',
        'alamat',
        'kota',
        'website',
        'email',
        'logo_path',
        'rektor_nama',
        'rektor_nip'
    ];
}
