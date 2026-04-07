<?php

namespace Database\Seeders;

use App\Models\Kriteria;
use Illuminate\Database\Seeder;

class KriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kriterias = [
            // BAN-PT Standard
            ['kode' => 'C1', 'nama' => 'Visi, Misi, Tujuan, dan Strategi', 'lam_type' => 'ban-pt', 'bobot' => 5],
            ['kode' => 'C2', 'nama' => 'Tata Pamong, Tata Kelola dan Kerjasama', 'lam_type' => 'ban-pt', 'bobot' => 10],
            ['kode' => 'C3', 'nama' => 'Mahasiswa', 'lam_type' => 'ban-pt', 'bobot' => 15],
            ['kode' => 'C4', 'nama' => 'Sumber Daya Manusia', 'lam_type' => 'ban-pt', 'bobot' => 20],
            ['kode' => 'C5', 'nama' => 'Keuangan, Sarana dan Prasarana', 'lam_type' => 'ban-pt', 'bobot' => 10],

            // LAM-INFOKOM (Specifics)
            ['kode' => 'IK-1', 'nama' => 'Kualitas Kurikulum Komputasi', 'lam_type' => 'lam-infokom', 'bobot' => 25],
            ['kode' => 'IK-2', 'nama' => 'Kerjasama Industri Teknologi', 'lam_type' => 'lam-infokom', 'bobot' => 15],
            ['kode' => 'IK-3', 'nama' => 'Laboratorium & Bandwidth', 'lam_type' => 'lam-infokom', 'bobot' => 10],

            // LAMEMBA (Specifics)
            ['kode' => 'EB-1', 'nama' => 'Kontribusi Intelektual Dosen Akuntansi', 'lam_type' => 'lam-emba', 'bobot' => 30],
            ['kode' => 'EB-2', 'nama' => 'Efikasi Pendanaan Mandiri', 'lam_type' => 'lam-emba', 'bobot' => 20],
        ];

        foreach ($kriterias as $k) {
            Kriteria::updateOrCreate(['kode' => $k['kode'], 'lam_type' => $k['lam_type']], $k);
        }
    }
}
