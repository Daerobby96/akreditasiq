<?php

namespace Database\Seeders;

use App\Models\Kriteria;
use Illuminate\Database\Seeder;

class LamembaLedSeeder extends Seeder
{
    public function run(): void
    {
        $lamembaKriterias = [
            [
                'kode' => 'A.',
                'nama' => 'Profil Unit Pengelola Program Studi',
                'lam_type' => 'lam-emba',
                'bobot' => 0,
                'template_narasi' => [
                    'sejarah' => '1. Sejarah Unit Pengelola Program Studi dan Program Studi',
                    'visi_misi' => '2. Visi, Misi, Tujuan, Strategi, dan Tata Nilai',
                    'organisasi' => '3. Organisasi dan Tata Kerja',
                    'mahasiswa' => '4. Mahasiswa dan Lulusan',
                    'dosen' => '5. Dosen dan Tenaga Kependidikan',
                    'keuangan' => '6. Keuangan, Sarana, dan Prasarana',
                    'spmi' => '7. Sistem Penjaminan Mutu Internal',
                    'kinerja' => '8. Kinerja Unit Pengelola Program Studi dan Program Studi'
                ]
            ],
            [
                'kode' => 'B.1',
                'nama' => 'Orientasi Strategis',
                'lam_type' => 'lam-emba',
                'bobot' => 10,
                'template_narasi' => [
                    'misi' => 'a. Misi (Deskripsi landasan filosofis, keterlibatan pemangku kepentingan, dan bukti pencapaian misi sesuai indikator 1-4)',
                    'visi' => 'b. Visi (Deskripsi aspirasi masa depan, keselarasan dengan institusi, dan hasil evaluasi relevansi visi)',
                    'tujuan' => 'c. Tujuan dan Sasaran (Deskripsi rumusan tujuan yang spesifik, terukur, dan berjangka waktu)',
                    'strategi' => 'd. Strategi (Deskripsi upaya pencapaian tujuan dengan integrasi manajemen risiko)'
                ]
            ],
            [
                'kode' => 'B.2',
                'nama' => 'Tata Pamong dan Tata Kelola',
                'lam_type' => 'lam-emba',
                'bobot' => 15,
                'template_narasi' => [
                    'tata_pamong' => 'a. Tata Pamong (Deskripsi struktur, proses pengawasan, dan pembentukan sinergi)',
                    'tata_kelola' => 'b. Tata Kelola (Deskripsi perencanaan, akuntabilitas, transparansi, dan SPMI)',
                ]
            ],
            [
                'kode' => 'B.3',
                'nama' => 'Pengelolaan Mahasiswa',
                'lam_type' => 'lam-emba',
                'bobot' => 10,
                'template_narasi' => [
                    'penerimaan' => 'a. Penerimaan Mahasiswa (Analisis kebijakan pendaftaran, seleksi, dan inklusivitas)',
                    'layanan' => 'b. Layanan Akademik (Deskripsi pedoman pedagogi, penggunaan teknologi, dan AI)',
                    'kinerja' => 'c. Kinerja Akademik (Analisis IPK, masa studi, dan hasil keterlibatan mahasiswa)',
                    'kesejahteraan' => 'd. Kesejahteraan Mahasiswa (Layanan kesehatan mental, fisik, dan keamanan kampus)',
                    'karir' => 'e. Pengembangan Karir (Program pembekalan dunia kerja dan penyaluran lulusan)'
                ]
            ],
            [
                'kode' => 'B.4',
                'nama' => 'Pengelolaan Dosen dan Tenaga Kependidikan',
                'lam_type' => 'lam-emba',
                'bobot' => 20,
                'template_narasi' => [
                    'kecukupan' => 'a. Kecukupan dan Kualifikasi Dosen (Analisis jumlah, kualifikasi, dan beban kerja)',
                    'pengelolaan_dosen' => 'b. Pengelolaan Dosen (Rencana rekrutmen, pengembangan kompetensi, dan fasilitas EMBA)',
                    'kecukupan_tendik' => 'c. Kecukupan Tenaga Kependidikan (Kualifikasi pendukung teknis dan administrasi)',
                    'pengelolaan_tendik' => 'd. Pengelolaan Tenaga Kependidikan (Rekrutmen dan pengembangan karir tendik)'
                ]
            ],
            [
                'kode' => 'B.5',
                'nama' => 'Keuangan dan Sarana Prasarana',
                'lam_type' => 'lam-emba',
                'bobot' => 15,
                'template_narasi' => [
                    'keuangan' => 'a. Keuangan (Perencanaan penerimaan, pengeluaran, dan keberlanjutan daya keuangan)',
                    'sarana' => 'b. Sarana dan Prasarana (Ketersediaan fasilitas belajar, keamanan, dan ramah difabel)'
                ]
            ],
            [
                'kode' => 'B.6',
                'nama' => 'Pendidikan dan Pengajaran',
                'lam_type' => 'lam-emba',
                'bobot' => 20,
                'template_narasi' => [
                    'kurikulum' => 'a. Kurikulum (Struktur mata kuliah, peta kurikulum, dan integrasi kebutuhan EMBA)',
                    'jaminan' => 'b. Jaminan Pembelajaran (Pengukuran langsung/tidak langsung CPL dan intervensi perbaikan)'
                ]
            ],
            [
                'kode' => 'B.7',
                'nama' => 'Penelitian dan Pengabdian Kepada Masyarakat',
                'lam_type' => 'lam-emba',
                'bobot' => 10,
                'template_narasi' => [
                    'penelitian' => 'a. Penelitian (Kontribusi kemajuan ilmu, evaluasi kinerja, dan rekognisi mitra)',
                    'pkm' => 'b. Pengabdian Masyarakat (Kegiatan, hasil, dan kontribusi PKM bagi pemangku kepentingan)'
                ]
            ],
            [
                'kode' => 'C.',
                'nama' => 'Analisis, Strategi Pengembangan dan Keberlanjutan',
                'lam_type' => 'lam-emba',
                'bobot' => 0,
                'template_narasi' => [
                    'analisis_swot' => '1. Analisis Capaian Kinerja (SWOT)',
                    'strategi' => '2. Strategi Pengembangan dan Keberlanjutan UPPS/PS'
                ]
            ]
        ];

        foreach ($lamembaKriterias as $k) {
            Kriteria::updateOrCreate(
                ['kode' => $k['kode'], 'lam_type' => 'lam-emba'],
                $k
            );
        }
    }
}
