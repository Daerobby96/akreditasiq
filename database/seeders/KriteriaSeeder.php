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

            // LAM-TEKNIK (Specifics)
            ['kode' => 'TK-1', 'nama' => 'Kurikulum Teknik dan Industri 4.0', 'lam_type' => 'lam-teknik', 'bobot' => 25],
            ['kode' => 'TK-2', 'nama' => 'Laboratorium & Fasilitas Teknik', 'lam_type' => 'lam-teknik', 'bobot' => 20],
            ['kode' => 'TK-3', 'nama' => 'Kerjasama Industri Teknik', 'lam_type' => 'lam-teknik', 'bobot' => 15],

            // LAMDIK (Lem-DIk) - Education Programs
            ['kode' => 'LK-1', 'nama' => 'Kurikulum dan Pembelajaran Kependidikan', 'lam_type' => 'lamdik', 'bobot' => 25],
            ['kode' => 'LK-2', 'nama' => 'Praktikum dan Pengalaman Lapangan', 'lam_type' => 'lamdik', 'bobot' => 20],
            ['kode' => 'LK-3', 'nama' => 'Kompetensi Pedagogik Calon Guru', 'lam_type' => 'lamdik', 'bobot' => 15],
        ];

        foreach ($kriterias as $k) {
            Kriteria::updateOrCreate(['kode' => $k['kode'], 'lam_type' => $k['lam_type']], $k);
        }
    }
}
