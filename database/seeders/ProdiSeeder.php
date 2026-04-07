<?php

namespace Database\Seeders;

use App\Models\Prodi;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProdiSeeder extends Seeder
{
    public function run(): void
    {
        $prodis = [
            ['nama' => 'Teknik Informatika', 'kode' => 'TI-S1', 'jenjang' => 'S1', 'lam_type' => 'lam-infokom'],
            ['nama' => 'Manajemen Bisnis', 'kode' => 'MB-S1', 'jenjang' => 'S1', 'lam_type' => 'lam-emba'],
            ['nama' => 'Kesehatan Masyarakat', 'kode' => 'KM-S1', 'jenjang' => 'S1', 'lam_type' => 'lam-ptkes'],
            ['nama' => 'Sistem Informasi', 'kode' => 'SI-S1', 'jenjang' => 'S1', 'lam_type' => 'lam-infokom'],
        ];

        foreach ($prodis as $p) {
            Prodi::create($p);
        }
    }
}
