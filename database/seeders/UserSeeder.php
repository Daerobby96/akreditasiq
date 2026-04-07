<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Prodi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ti = Prodi::where('kode', 'TI-S1')->first();
        $mb = Prodi::where('kode', 'MB-S1')->first();
        $km = Prodi::where('kode', 'KM-S1')->first();

        // 1. Super Admin (Full Control)
        User::updateOrCreate(['email' => 'admin@akre.test'], [
            'name' => 'Administrator AKRE',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'prodi_id' => $ti->id,
        ]);

        // 2. Asesor (Review & Scoring)
        User::updateOrCreate(['email' => 'asesor@akre.test'], [
            'name' => 'Prof. Dr. Asesor Utama',
            'password' => Hash::make('password'),
            'role' => 'asesor',
            'prodi_id' => $km->id, // Asesor assigned to KM for example
        ]);

        // 3. User / Dosen (Uploaders)
        User::updateOrCreate(['email' => 'dosen@akre.test'], [
            'name' => 'Dosen Penanggung Jawab',
            'password' => Hash::make('password'),
            'role' => 'user',
            'prodi_id' => $mb->id,
        ]);
        
        // Dynamic users for testing
        User::factory(5)->create();
    }
}
