<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LamTable;
use App\Models\LamTableColumn;

class LemdikSeeder extends Seeder
{
    public function run(): void
    {
        $lamType = 'lamdik';

        // Clear existing for this LAM to avoid duplicates
        $existingTables = LamTable::where('lam_type', $lamType)->get();
        foreach($existingTables as $et) {
            $et->columns()->delete();
            $et->delete();
        }

        // Helper functions
        $createTable = function($slug, $label) use ($lamType) {
            return LamTable::create(['slug' => $slug, 'lam_type' => $lamType, 'label' => $label]);
        };

        $createCol = function($table, $header, $field, $type = 'text', $parentId = null, $order = 0) {
            return LamTableColumn::create([
                'lam_table_id' => $table->id, 'header_name' => $header, 'field_name' => $field,
                'data_type' => $type, 'parent_id' => $parentId, 'sort_order' => $order
            ]);
        };

        // --- CRITERION LK-1: Kurikulum dan Pembelajaran Kependidikan ---

        // Tabel 1.A.1 Struktur Kurikulum Kependidikan
        $t1a1 = $createTable('tabel_1_a_1_kurikulum_pendidikan', 'Tabel 1.A.1 Struktur Kurikulum Kependidikan');
        $createCol($t1a1, 'Kode MK', 'kode_mk');
        $createCol($t1a1, 'Nama Mata Kuliah', 'nama_mk');
        $createCol($t1a1, 'SKS', 'sks', 'number');
        $createCol($t1a1, 'Semester', 'semester', 'number');
        $createCol($t1a1, 'Kompetensi Pedagogik', 'kompetensi_pedagogik');
        $createCol($t1a1, 'Kompetensi Profesional', 'kompetensi_profesional');

        // Tabel 1.A.2 Capaian Pembelajaran Lulusan (CPL) Kependidikan
        $t1a2 = $createTable('tabel_1_a_2_cpl_pendidikan', 'Tabel 1.A.2 Capaian Pembelajaran Lulusan (CPL) Kependidikan');
        $createCol($t1a2, 'Kode CPL', 'kode_cpl');
        $createCol($t1a2, 'Deskripsi CPL', 'deskripsi_cpl');
        $createCol($t1a2, 'Domain Kompetensi (Pedagogik/Profesional/Sosial/Kepribadian)', 'domain');
        $createCol($t1a2, 'Level KKNI', 'level_kkni', 'number');

        // Tabel 1.B.1 Mata Kuliah Pengembangan Kepribadian
        $t1b1 = $createTable('tabel_1_b_1_mk_kepribadian', 'Tabel 1.B.1 Mata Kuliah Pengembangan Kepribadian');
        $createCol($t1b1, 'Nama Mata Kuliah', 'nama_mk');
        $createCol($t1b1, 'SKS', 'sks', 'number');
        $createCol($t1b1, 'Semester', 'semester', 'number');
        $createCol($t1b1, 'Tujuan Pembelajaran', 'tujuan');
        $createCol($t1b1, 'Metode Pembelajaran', 'metode');

        // --- CRITERION LK-2: Praktikum dan Pengalaman Lapangan ---

        // Tabel 2.A.1 Praktikum Kependidikan
        $t2a1 = $createTable('tabel_2_a_1_praktikum_pendidikan', 'Tabel 2.A.1 Praktikum Kependidikan');
        $createCol($t2a1, 'Nama Praktikum', 'nama_praktikum');
        $createCol($t2a1, 'SKS', 'sks', 'number');
        $createCol($t2a1, 'Semester', 'semester', 'number');
        $createCol($t2a1, 'Lokasi Praktikum', 'lokasi');
        $createCol($t2a1, 'Jumlah Mahasiswa per Kelas', 'jml_mhs', 'number');
        $createCol($t2a1, 'Durasi (Minggu)', 'durasi', 'number');

        // Tabel 2.A.2 PPL (Praktik Pengalaman Lapangan)
        $t2a2 = $createTable('tabel_2_a_2_ppl', 'Tabel 2.A.2 PPL (Praktik Pengalaman Lapangan)');
        $createCol($t2a2, 'Tahun Akademik', 'tahun');
        $createCol($t2a2, 'Jumlah Mahasiswa PPL', 'jml_mhs_ppl', 'number');
        $createCol($t2a2, 'Jumlah Sekolah Partner', 'jml_sekolah', 'number');
        $createCol($t2a2, 'Rata-rata Durasi PPL (Minggu)', 'avg_durasi', 'number');
        $createCol($t2a2, 'Persentase Mahasiswa Lulus PPL', 'persentase_lulus', 'number');

        // Tabel 2.B.1 Fasilitas Praktikum Kependidikan
        $t2b1 = $createTable('tabel_2_b_1_fasilitas_praktikum', 'Tabel 2.B.1 Fasilitas Praktikum Kependidikan');
        $createCol($t2b1, 'Nama Fasilitas', 'nama_fasilitas');
        $createCol($t2b1, 'Jenis (Lab/Microteaching/Ruang Simulasi)', 'jenis');
        $createCol($t2b1, 'Luas (m2)', 'luas', 'number');
        $createCol($t2b1, 'Kapasitas', 'kapasitas', 'number');
        $createCol($t2b1, 'Status Kepemilikan', 'status');
        $createCol($t2b1, 'Perangkat Utama', 'perangkat');

        // --- CRITERION LK-3: Kompetensi Pedagogik Calon Guru ---

        // Tabel 3.A.1 Pengembangan Kompetensi Pedagogik
        $t3a1 = $createTable('tabel_3_a_1_kompetensi_pedagogik', 'Tabel 3.A.1 Pengembangan Kompetensi Pedagogik');
        $createCol($t3a1, 'Aspek Kompetensi Pedagogik', 'aspek');
        $createCol($t3a1, 'Mata Kuliah Pengembang', 'mk_pengembang');
        $createCol($t3a1, 'Metode Pengembangan', 'metode');
        $createCol($t3a1, 'Indikator Pencapaian', 'indikator');
        $createCol($t3a1, 'SKS', 'sks', 'number');

        // Tabel 3.A.2 Sertifikasi Pedagogik Mahasiswa
        $t3a2 = $createTable('tabel_3_a_2_sertifikasi_pedagogik', 'Tabel 3.A.2 Sertifikasi Pedagogik Mahasiswa');
        $createCol($t3a2, 'Jenis Sertifikasi', 'jenis_sertifikasi');
        $createCol($t3a2, 'Lembaga Pemberi', 'lembaga');
        $createCol($t3a2, 'Jumlah Mahasiswa Lulus', 'jml_lulus', 'number');
        $createCol($t3a2, 'Tahun', 'tahun');
        $createCol($t3a2, 'Persentase Kelulusan', 'persentase', 'number');

        // Tabel 3.B.1 Prestasi Mahasiswa Bidang Kependidikan
        $t3b1 = $createTable('tabel_3_b_1_prestasi_mhs', 'Tabel 3.B.1 Prestasi Mahasiswa Bidang Kependidikan');
        $createCol($t3b1, 'Nama Kegiatan', 'nama_kegiatan');
        $createCol($t3b1, 'Tingkat (Internasional/Nasional/Lokal)', 'tingkat');
        $createCol($t3b1, 'Jenis Prestasi', 'jenis_prestasi');
        $createCol($t3b1, 'Tahun', 'tahun');
        $createCol($t3b1, 'Jumlah Mahasiswa Terlibat', 'jml_mhs', 'number');
        $createCol($t3b1, 'Link Bukti', 'bukti');

        // Tabel 3.C.1 Kerjasama dengan Sekolah/Madrasah
        $t3c1 = $createTable('tabel_3_c_1_kerjasama_sekolah', 'Tabel 3.C.1 Kerjasama dengan Sekolah/Madrasah');
        $createCol($t3c1, 'Nama Sekolah/Madrasah', 'nama_sekolah');
        $createCol($t3c1, 'Jenis Kerjasama', 'jenis_kerjasama');
        $createCol($t3c1, 'Durasi (Tahun)', 'durasi', 'number');
        $createCol($t3c1, 'Manfaat untuk Prodi', 'manfaat');
        $createCol($t3c1, 'Jumlah Mahasiswa Terlibat', 'jml_mhs', 'number');
        $createCol($t3c1, 'Link Dokumen', 'link_dokumen');
    }
}