<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Setting::create([
            'nama_institusi' => 'Universitas Akreditasi Indonesia',
            'alamat' => 'Jl. Pendidikan No. 123',
            'kota' => 'Jakarta Kota',
            'website' => 'https://akreditasi.ac.id',
            'email' => 'info@akreditasi.ac.id',
            'rektor_nama' => 'Prof. Dr. Akreditasi, M.Kom.',
        ]);
    }
}
