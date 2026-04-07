<?php

namespace Database\Seeders;

use App\Models\Kriteria;
use Illuminate\Database\Seeder;

class InfokomLedSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cleanup old criteria for Infokom
        Kriteria::where('lam_type', 'lam-infokom')->delete();

        // 2. Define PPEPP Template (Common for C.1 - C.6)
        $ppeppTemplate = [
            'penetapan' => '1. [PENETAPAN] Daftar dan penjelasan dokumen kebijakan, standar, dan indikator yang ditetapkan.',
            'pelaksanaan' => '2. [PELAKSANAAN] Penjelasan tentang pelaksanaan kebijakan, standar, dan indikator (merujuk pada LKPS).',
            'evaluasi' => '3. [EVALUASI] Penjelasan tentang evaluasi berkala, faktor pendukung, akar masalah, dan hasil survei kepuasan.',
            'pengendalian' => '4. [PENGENDALIAN] Penjelasan tentang tindak lanjut (revisi dan rekomendasi) terhadap akar masalah.',
            'peningkatan' => '5. [PENINGKATAN] Penjelasan tentang peningkatan/optimalisasi standar baik yang sudah terpenuhi maupun belum.'
        ];

        $infokomKriterias = [
            [
                'kode' => 'A.',
                'nama' => 'Kondisi Eksternal',
                'lam_type' => 'lam-infokom',
                'bobot' => 0,
                'template_narasi' => [
                    'lingkungan_makro' => 'Analisis Lingkungan Makro (Kebijakan, Ekonomi, Sosial, Budaya, IPTEK)',
                    'lingkungan_mikro' => 'Analisis Lingkungan Mikro (Pesaing, Pengguna, Sumber Calon Maba/Dosen, Pendanaan, Kemitraan)',
                    'peluang_ancaman' => 'Peluang (Opportunities) dan Ancaman (Threats)'
                ]
            ],
            [
                'kode' => 'B.',
                'nama' => 'Profil Unit Pengelola Program Studi',
                'lam_type' => 'lam-infokom',
                'bobot' => 0,
                'template_narasi' => [
                    'sejarah' => '1. Sejarah Unit Pengelola Program Studi',
                    'vmts' => '2. Visi, Misi, Tujuan, Strategi, dan Tata Nilai',
                    'otk' => '3. Organisasi dan Tata Kerja',
                    'mahasiswa' => '4. Mahasiswa dan Lulusan',
                    'sdm' => '5. Dosen dan Tenaga Kependidikan',
                    'keuangan' => '6. Keuangan, Sarana, dan Prasarana',
                    'spm' => '7. Sistem Penjaminan Mutu',
                    'kinerja' => '8. Kinerja UPPS dan Program Studi'
                ]
            ],
            [
                'kode' => 'C.1',
                'nama' => 'Budaya Mutu',
                'lam_type' => 'lam-infokom',
                'bobot' => 15,
                'template_narasi' => $ppeppTemplate
            ],
            [
                'kode' => 'C.2',
                'nama' => 'Relevansi Pendidikan',
                'lam_type' => 'lam-infokom',
                'bobot' => 30,
                'template_narasi' => $ppeppTemplate
            ],
            [
                'kode' => 'C.3',
                'nama' => 'Relevansi Penelitian',
                'lam_type' => 'lam-infokom',
                'bobot' => 15,
                'template_narasi' => $ppeppTemplate
            ],
            [
                'kode' => 'C.4',
                'nama' => 'Relevansi Pengabdian kepada Masyarakat',
                'lam_type' => 'lam-infokom',
                'bobot' => 10,
                'template_narasi' => $ppeppTemplate
            ],
            [
                'kode' => 'C.5',
                'nama' => 'Akuntabilitas',
                'lam_type' => 'lam-infokom',
                'bobot' => 15,
                'template_narasi' => $ppeppTemplate
            ],
            [
                'kode' => 'C.6',
                'nama' => 'Diferensiasi Misi',
                'lam_type' => 'lam-infokom',
                'bobot' => 15,
                'template_narasi' => $ppeppTemplate
            ],
            [
                'kode' => 'D.',
                'nama' => 'Suplemen Program Studi',
                'lam_type' => 'lam-infokom',
                'bobot' => 0,
                'template_narasi' => [
                    'kurikulum' => 'Muatan Kurikulum yang dimiliki Program Studi',
                    'proyek' => 'Integrasi Teori dan Praktik / Capstone Project'
                ]
            ]
        ];

        foreach ($infokomKriterias as $k) {
            Kriteria::create($k);
        }
    }
}
