<?php

namespace Database\Seeders;

use App\Models\Kriteria;
use Illuminate\Database\Seeder;

class LamTeknikLedSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cleanup old criteria for Lam Teknik
        Kriteria::where('lam_type', 'lam-teknik')->delete();

        $lamteknikKriterias = [
            [
                'kode' => 'A.',
                'nama' => 'Struktur Tim Penyusun dan Mekanisme Kerja',
                'lam_type' => 'lam-teknik',
                'bobot' => 0,
                'template_narasi' => [
                    'tim_penyusun' => 'Deskripsi tim penyusun (Ketua, Sekretaris, Anggota) dan peran masing-masing.',
                    'mekanisme_kerja' => 'Mekanisme pengumpulan data, verifikasi, dan penyusunan narasi evaluasi diri.'
                ]
            ],
            [
                'kode' => 'B.',
                'nama' => 'Analisis Lingkungan Eksternal dalam Pengembangan UPPS dan Prodi',
                'lam_type' => 'lam-teknik',
                'bobot' => 0,
                'template_narasi' => [
                    'analisis_makro' => 'Analisis Lingkungan Makro (Kebijakan pemerintah, Ekonomi, Sosial, Budaya, IPTEK).',
                    'analisis_mikro' => 'Analisis Lingkungan Mikro (Dunia industri/kerja, Pesaing, Pengguna lulusan).'
                ]
            ],
            [
                'kode' => 'C1.',
                'nama' => 'Differensiasi Misi',
                'lam_type' => 'lam-teknik',
                'bobot' => 10,
                'template_narasi' => [
                    'vmts' => 'Visi, Misi, Tujuan, dan Strategi (VMTS) UPPS dan Visi Keilmuan Program Studi.',
                    'strategi' => 'Strategi pencapaian VMTS dan keberlanjutan UPPS/PS.'
                ]
            ],
            [
                'kode' => 'C2.',
                'nama' => 'Akuntabilitas',
                'lam_type' => 'lam-teknik',
                'bobot' => 15,
                'template_narasi' => [
                    'kerjasama' => 'a. Kerja sama (Pendidikan, Penelitian, PkM tingkat Internasional, Nasional, Lokal).',
                    'keuangan' => 'b. Keuangan (Perencanaan, alokasi, dan penggunaan dana TS-2 s.d TS).',
                ]
            ],
            [
                'kode' => 'C3.',
                'nama' => 'Relevansi Pendidikan, Penelitian, dan PkM',
                'lam_type' => 'lam-teknik',
                'bobot' => 20,
                'template_narasi' => [
                    'kurikulum' => 'a. Kurikulum dan Rencana Pembelajaran (Pemenuhan KKNI dan Industri 4.0).',
                    'integrasi' => 'b. Integrasi Penelitian dan PkM dalam proses Pembelajaran.',
                    'hibah' => 'c. Penelitian dan PkM DTPS (Capaian hibah dan pendanaan).'
                ]
            ],
            [
                'kode' => 'C4.',
                'nama' => 'Sumber Daya Manusia',
                'lam_type' => 'lam-teknik',
                'bobot' => 20,
                'template_narasi' => [
                    'dosen' => 'a. Profil Dosen Tetap dan Kecukupan DTPS.',
                    'tendik' => 'b. Tenaga Kependidikan (Kualifikasi teknisi, laboran, admin).',
                    'beban_kerja' => 'c. Beban Kerja DTPR (EWMP per semester).',
                    'luaran' => 'd. Luaran Penelitian/PkM Dosen (Publikasi, HKI, Rekognisi).'
                ]
            ],
            [
                'kode' => 'C5.',
                'nama' => 'Sarana, Prasarana, dan K3L',
                'lam_type' => 'lam-teknik',
                'bobot' => 10,
                'template_narasi' => [
                    'fasilitas' => 'Sarana dan Prasarana Utama (Laboratorium, Studio, Bengkel).',
                    'k3l' => 'Keselamatan Kesehatan Kerja dan Lingkungan (K3L) di UPPS.'
                ]
            ],
            [
                'kode' => 'C6.',
                'nama' => 'Mahasiswa dan Luaran Mahasiswa',
                'lam_type' => 'lam-teknik',
                'bobot' => 15,
                'template_narasi' => [
                    'mahasiswa' => 'a. Jumlah Mahasiswa Aktif dan Kualitas Masukan.',
                    'kualitas' => 'b. IPK Lulusan dan Prestasi Mahasiswa (Akademik/Non-akademik).',
                    'masa_studi' => 'c. Masa Studi Lulusan dan Waktu Tunggu Bekerja.',
                    'daya_saing' => 'd. Daya Saing Lulusan dan Kepuasan Pengguna.'
                ]
            ],
            [
                'kode' => 'C7.',
                'nama' => 'Penjaminan Mutu',
                'lam_type' => 'lam-teknik',
                'bobot' => 10,
                'template_narasi' => [
                    'dokumen' => 'a. Ketersediaan Dokumen/Buku SPMI (Kebijakan, Manual, Standar).',
                    'siklus' => 'b. Pelaksanaan Siklus PPEPP dan Hasil Audit Internal.'
                ]
            ]
        ];

        foreach ($lamteknikKriterias as $k) {
            Kriteria::create($k);
        }
    }
}