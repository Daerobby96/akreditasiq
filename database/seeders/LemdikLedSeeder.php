<?php

namespace Database\Seeders;

use App\Models\Kriteria;
use Illuminate\Database\Seeder;

class LemdikLedSeeder extends Seeder
{
    public function run(): void
    {
        $lamdikKriterias = [
            [
                'kode' => 'LK-1',
                'nama' => 'Kurikulum dan Pembelajaran Kependidikan',
                'lam_type' => 'lamdik',
                'bobot' => 25,
                'template_narasi' => [
                    'struktur_kurikulum' => 'a. Struktur Kurikulum Kependidikan (Deskripsi kesesuaian kurikulum dengan standar pendidikan guru)',
                    'capaian_pembelajaran' => 'b. Capaian Pembelajaran Lulusan (Deskripsi CPL yang mencakup kompetensi pedagogik dan profesional)',
                    'pengembangan_kepribadian' => 'c. Mata Kuliah Pengembangan Kepribadian (Deskripsi MK yang mengembangkan karakter dan etika guru)',
                    'evaluasi_kurikulum' => 'd. Evaluasi dan Pengembangan Kurikulum (Deskripsi proses review kurikulum berdasarkan perkembangan pendidikan)'
                ]
            ],
            [
                'kode' => 'LK-2',
                'nama' => 'Praktikum dan Pengalaman Lapangan',
                'lam_type' => 'lamdik',
                'bobot' => 20,
                'template_narasi' => [
                    'praktikum_pendidikan' => 'a. Praktikum Kependidikan (Deskripsi kegiatan praktikum microteaching dan simulasi pembelajaran)',
                    'ppl' => 'b. Praktik Pengalaman Lapangan (PPL) (Deskripsi program PPL dan kualitas pengalaman mahasiswa)',
                    'fasilitas_praktikum' => 'c. Fasilitas Praktikum Kependidikan (Deskripsi ketersediaan lab microteaching dan ruang simulasi)',
                    'evaluasi_praktikum' => 'd. Evaluasi Program Praktikum (Deskripsi hasil evaluasi dan improvement program praktikum)'
                ]
            ],
            [
                'kode' => 'LK-3',
                'nama' => 'Kompetensi Pedagogik Calon Guru',
                'lam_type' => 'lamdik',
                'bobot' => 15,
                'template_narasi' => [
                    'kompetensi_pedagogik' => 'a. Pengembangan Kompetensi Pedagogik (Deskripsi mata kuliah dan metode pengembangan kompetensi mengajar)',
                    'sertifikasi_pedagogik' => 'b. Sertifikasi Pedagogik Mahasiswa (Deskripsi program sertifikasi dan pencapaian mahasiswa)',
                    'prestasi_mahasiswa' => 'c. Prestasi Mahasiswa Bidang Kependidikan (Deskripsi prestasi akademik dan non-akademik)',
                    'kerjasama_sekolah' => 'd. Kerjasama dengan Sekolah/Madrasah (Deskripsi partnership untuk pengembangan kompetensi mahasiswa)'
                ]
            ]
        ];

        foreach ($lamdikKriterias as $kriteria) {
            Kriteria::updateOrCreate(
                ['kode' => $kriteria['kode'], 'lam_type' => $kriteria['lam_type']],
                $kriteria
            );
        }
    }
}