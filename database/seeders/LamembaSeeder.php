<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LamembaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lamType = 'lam-emba';

        // Clear existing for this LAM to avoid duplicates
        $existingTables = \App\Models\LamTable::where('lam_type', $lamType)->get();
        foreach($existingTables as $et) {
            $et->columns()->delete();
            $et->delete();
        }

        // Helper to create table
        $createTable = function($slug, $label) use ($lamType) {
            return \App\Models\LamTable::create([
                'slug' => $slug, 
                'lam_type' => $lamType,
                'label' => $label
            ]);
        };

        // Helper to create column
        $createCol = function($table, $header, $field, $type = 'text', $parentId = null, $order = 0) {
            return \App\Models\LamTableColumn::create([
                'lam_table_id' => $table->id,
                'header_name' => $header,
                'field_name' => $field,
                'data_type' => $type,
                'parent_id' => $parentId,
                'sort_order' => $order
            ]);
        };

        // --- TABEL 1: Profil Dosen Status ---
        $t1 = $createTable('tabel_1_dosen_status', 'Tabel 1. Profil Dosen Status Kepegawaian');
        $createCol($t1, 'No', 'no', 'number');
        $createCol($t1, 'Nama Dosen', 'nama_dosen');
        $createCol($t1, 'Status (Tetap/Tidak)', 'status');
        $createCol($t1, 'NIDN/NIDK', 'nidn');
        $createCol($t1, 'Jabatan Akademik', 'jabatan');
        $createCol($t1, 'Akademisi/Praktisi', 'jenis_dosen');
        $createCol($t1, 'Perusahaan/Industri*', 'perusahaan');

        // --- TABEL 2: Profil Dosen Keahlian ---
        $t2 = $createTable('tabel_2_dosen_keahlian', 'Tabel 2. Profil Dosen Latar Belakang Keahlian');
        $createCol($t2, 'No', 'no', 'number');
        $createCol($t2, 'Nama Dosen', 'nama_dosen');
        $pnd = $createCol($t2, 'Pendidikan Pasca Sarjana', 'pendidikan_ps');
        $createCol($t2, 'Magister/S2', 's2', 'text', $pnd->id);
        $createCol($t2, 'Doktor/S3', 's3', 'text', $pnd->id);
        $createCol($t2, 'Bidang Keahlian', 'bidang');
        $createCol($t2, 'Sertifikat Pendidik', 'serdos');
        $createCol($t2, 'Sertifikat Kompetensi', 'sertifikasi');

        // --- TABEL 3: Kontribusi Intelektual ---
        $t3 = $createTable('tabel_3_dosen_intelektual', 'Tabel 3. Profil Dosen Kontribusi Intelektual');
        $createCol($t3, 'No', 'no', 'number');
        $createCol($t3, 'Nama Dosen', 'nama_dosen');
        $dikjar = $createCol($t3, 'Pendidikan dan Pengajaran', 'dikjar_group');
        $createCol($t3, 'MK Diampu pada PS Akre', 'mk_ps_akre', 'text', $dikjar->id);
        $createCol($t3, 'MK Diampu pada PS Lain', 'mk_ps_lain', 'text', $dikjar->id);
        $createCol($t3, 'Judul Bahan Ajar', 'bahan_ajar', 'text', $dikjar->id);
        
        $mhsAkre = $createCol($t3, 'Jml Mhs Dibimbing pada PS Akred', 'mhs_akre_group', 'text', $dikjar->id);
        $createCol($t3, 'TS-2', 'akre_ts2', 'number', $mhsAkre->id);
        $createCol($t3, 'TS-1', 'akre_ts1', 'number', $mhsAkre->id);
        $createCol($t3, 'TS', 'akre_ts', 'number', $mhsAkre->id);
        $createCol($t3, 'Rata-rata', 'akre_rata', 'number', $mhsAkre->id);

        $mhsLain = $createCol($t3, 'Jml Mhs Dibimbing pada PS Lain', 'mhs_lain_group', 'text', $dikjar->id);
        $createCol($t3, 'TS-2', 'lain_ts2', 'number', $mhsLain->id);
        $createCol($t3, 'TS-1', 'lain_ts1', 'number', $mhsLain->id);
        $createCol($t3, 'TS', 'lain_ts', 'number', $mhsLain->id);
        $createCol($t3, 'Rata-rata', 'lain_rata', 'number', $mhsLain->id);

        $createCol($t3, 'Rata-rata Jml Bimbingan', 'rata_bimbingan', 'number', $dikjar->id);
        $createCol($t3, 'Rekognisi Bidang', 'rekognisi', 'text', $dikjar->id);

        // --- TABEL 4: EWMP ---
        $t4 = $createTable('tabel_4_ewmp', 'Tabel 4. Ekuivalen Waktu Mengajar Penuh (EWMP)');
        $createCol($t4, 'No', 'no', 'number');
        $createCol($t4, 'Nama Dosen', 'nama_dosen');
        $ewmpGroup = $createCol($t4, 'EWMP (sks)', 'ewmp_group');
        $ajarGroup = $createCol($t4, 'Pembelajaran & Bimbingan', 'ajar_group', 'text', $ewmpGroup->id);
        $createCol($t4, 'PS Diakreditasi', 'sks_akre', 'number', $ajarGroup->id);
        $createCol($t4, 'PS Lain di PT', 'sks_pt_lain', 'number', $ajarGroup->id);
        $createCol($t4, 'PS Lain luar PT', 'sks_pt_luar', 'number', $ajarGroup->id);
        $createCol($t4, 'Penelitian', 'sks_riset', 'number', $ewmpGroup->id);
        $createCol($t4, 'PkM', 'sks_pkm', 'number', $ewmpGroup->id);
        $createCol($t4, 'Tugas Tambahan', 'sks_tugas', 'number', $ewmpGroup->id);
        $createCol($t4, 'Jumlah (sks)', 'sks_total', 'number');
        $createCol($t4, 'Rata-rata per Semester', 'sks_rata', 'number');

        // --- TABEL 5: Tendik ---
        $t5 = $createTable('tabel_5_tendik', 'Tabel 5. Profil Tenaga Kependidikan');
        $createCol($t5, 'No', 'no', 'number');
        $createCol($t5, 'Nama Tendik', 'nama_tendik');
        $createCol($t5, 'Status', 'status');
        $createCol($t5, 'Jabatan', 'jabatan');
        $pndTk = $createCol($t5, 'Pendidikan', 'pnd_group');
        $createCol($t5, 'Diploma', 'pnd_d3', 'text', $pndTk->id);
        $createCol($t5, 'Sarjana', 'pnd_s1', 'text', $pndTk->id);
        $createCol($t5, 'Magister', 'pnd_s2', 'text', $pndTk->id);
        $createCol($t5, 'Sertifikat Kompetensi', 'sertifikasi');

        // --- TABEL 6: Keuangan ---
        $t6 = $createTable('tabel_6_keuangan', 'Tabel 6. Profil Keuangan Program Studi');
        $createCol($t6, 'No', 'no', 'number');
        $createCol($t6, 'Jenis Sumber/Penggunaan', 'jenis');
        $uppsGroup = $createCol($t6, 'Unit Pengelola (UPPS) Juta Rp', 'upps_group');
        $createCol($t6, 'TS-2', 'upps_ts2', 'currency', $uppsGroup->id);
        $createCol($t6, 'TS-1', 'upps_ts1', 'currency', $uppsGroup->id);
        $createCol($t6, 'TS', 'upps_ts', 'currency', $uppsGroup->id);
        $createCol($t6, 'Rata-rata', 'upps_rata', 'currency', $uppsGroup->id);

        // --- TABEL 7: Prestasi ---
        $t7 = $createTable('tabel_7_prestasi', 'Tabel 7. Prestasi Akademik & Non-Akademik');
        $createCol($t7, 'No', 'no', 'number');
        $createCol($t7, 'Nama Kegiatan', 'nama');
        $createCol($t7, 'Akademik/Non', 'jenis');
        $createCol($t7, 'Tahun', 'tahun');
        $tkGroup = $createCol($t7, 'Tingkat', 'tk_group');
        $createCol($t7, 'Lokal', 'tk_lokal', 'text', $tkGroup->id);
        $createCol($t7, 'Nasional', 'tk_nasional', 'text', $tkGroup->id);
        $createCol($t7, 'Internasional', 'tk_inter', 'text', $tkGroup->id);
        $createCol($t7, 'Prestasi Dicapai', 'prestasi');

        // --- TABEL 8: Masa Studi D3 ---
        $t8 = $createTable('tabel_8_masa_studi_d3', 'Tabel 8. Masa Studi Lulusan (D3)');
        $createCol($t8, 'No', 'no', 'number');
        $createCol($t8, 'Tahun Masuk', 'tahun_masuk');
        $createCol($t8, 'Jml Mhs Diterima', 'jml_mhs', 'number');
        $lulusGroup3 = $createCol($t8, 'Jumlah Lulusan pada Akhir', 'lulus_group');
        $createCol($t8, 'TS-2', 'ts2', 'number', $lulusGroup3->id);
        $createCol($t8, 'TS-1', 'ts1', 'number', $lulusGroup3->id);
        $createCol($t8, 'TS', 'ts', 'number', $lulusGroup3->id);
        $createCol($t8, 'Jml Lulusan s.d. TS', 'total_lulus', 'number');
        $createCol($t8, 'Rata-rata Masa Studi', 'rata_masa_studi', 'number');

        // --- TABEL 9: Masa Studi S1 ---
        $t9 = $createTable('tabel_9_masa_studi_s1', 'Tabel 9. Masa Studi Lulusan (S1)');
        $createCol($t9, 'No', 'no', 'number');
        $createCol($t9, 'Tahun Masuk', 'tahun_masuk');
        $createCol($t9, 'Jml Mhs Diterima', 'jml_mhs', 'number');
        $lulusGroup1 = $createCol($t9, 'Jumlah Lulusan pada Akhir', 'lulus_group');
        $createCol($t9, 'TS-6', 'ts6', 'number', $lulusGroup1->id);
        $createCol($t9, 'TS-5', 'ts5', 'number', $lulusGroup1->id);
        $createCol($t9, 'TS-4', 'ts4', 'number', $lulusGroup1->id);
        $createCol($t9, 'TS-3', 'ts3', 'number', $lulusGroup1->id);
        $createCol($t9, 'TS-2', 'ts2', 'number', $lulusGroup1->id);
        $createCol($t9, 'TS-1', 'ts1', 'number', $lulusGroup1->id);
        $createCol($t9, 'TS', 'ts', 'number', $lulusGroup1->id);
        $createCol($t9, 'Jml Lulusan s.d. TS', 'total_lulus', 'number');
        $createCol($t9, 'Rata-rata Masa Studi', 'rata_masa_studi', 'number');

        // --- TABEL 10: Masa Studi S2 ---
        $t10 = $createTable('tabel_10_masa_studi_s2', 'Tabel 10. Masa Studi Lulusan (S2)');
        $createCol($t10, 'No', 'no', 'number');
        $createCol($t10, 'Tahun Masuk', 'tahun_masuk');
        $createCol($t10, 'Jml Mhs Diterima', 'jml_mhs', 'number');
        $lulusGroup2 = $createCol($t10, 'Jumlah Lulusan pada Akhir', 'lulus_group');
        $createCol($t10, 'TS-3', 'ts3', 'number', $lulusGroup2->id);
        $createCol($t10, 'TS-2', 'ts2', 'number', $lulusGroup2->id);
        $createCol($t10, 'TS-1', 'ts1', 'number', $lulusGroup2->id);
        $createCol($t10, 'TS', 'ts', 'number', $lulusGroup2->id);
        $createCol($t10, 'Jml Lulusan s.d. TS', 'total_lulus', 'number');
        $createCol($t10, 'Rata-rata Masa Studi', 'rata_masa_studi', 'number');

        // --- TABEL 12: Waktu Tunggu Lulusan ---
        $t12 = $createTable('tabel_12_waktu_tunggu', 'Tabel 12. Waktu Tunggu Lulusan (D3/S1)');
        $createCol($t12, 'No', 'no', 'number');
        $createCol($t12, 'Tahun Lulus', 'tahun_lulus');
        $createCol($t12, 'Jumlah Lulusan', 'jml_lulusan', 'number');
        $createCol($t12, 'Jml Lulusan yang Ditelusuri', 'jml_lacak', 'number');
        $wtGroup = $createCol($t12, 'Jml Lulusan Bekerja dengan Waktu Tunggu', 'wt_group');
        $createCol($t12, 'WT < 3 bulan', 'wt_3', 'number', $wtGroup->id);
        $createCol($t12, '3 <= WT <= 6 bulan', 'wt_3_6', 'number', $wtGroup->id);
        $createCol($t12, 'WT > 6 bulan', 'wt_6', 'number', $wtGroup->id);

        // --- TABEL 15: Kesesuaian Bidang Kerja ---
        $t15 = $createTable('tabel_15_kesesuaian_kerja', 'Tabel 15. Kesesuaian Bidang Kerja Lulusan');
        $createCol($t15, 'No', 'no', 'number');
        $createCol($t15, 'Tahun Lulus', 'tahun_lulus');
        $createCol($t15, 'Jumlah Lulusan', 'jml_lulusan', 'number');
        $createCol($t15, 'Jml Lulusan yang Ditelusuri', 'jml_lacak', 'number');
        $ksesuaianGroup = $createCol($t15, 'Tingkat Kesesuaian Bidang Kerja', 'ksesuaian_group');
        $createCol($t15, 'Rendah', 'fit_low', 'number', $ksesuaianGroup->id);
        $createCol($t15, 'Sedang', 'fit_mid', 'number', $ksesuaianGroup->id);
        $createCol($t15, 'Tinggi', 'fit_high', 'number', $ksesuaianGroup->id);

        // --- TABEL 17: Kepuasan Pengguna ---
        $t17 = $createTable('tabel_17_kepuasan_pengguna', 'Tabel 17. Kepuasan Pengguna Lulusan');
        $createCol($t17, 'No', 'no', 'number');
        $createCol($t17, 'Aspek Penilaian', 'aspek');
        $puGroup = $createCol($t17, 'Tingkat Kepuasan Pengguna (%)', 'pu_group');
        $createCol($t17, 'Sangat Baik', 'pu_sangat_baik', 'number', $puGroup->id);
        $createCol($t17, 'Baik', 'pu_baik', 'number', $puGroup->id);
        $createCol($t17, 'Cukup', 'pu_cukup', 'number', $puGroup->id);
        $createCol($t17, 'Kurang', 'pu_kurang', 'number', $puGroup->id);
        $createCol($t17, 'Rencana Tindak Lanjut', 'rtl');

        // --- TABEL 18: Publikasi Mahasiswa ---
        $t18 = $createTable('tabel_18_publikasi_mhs', 'Tabel 18. Publikasi Ilmiah Mahasiswa');
        $createCol($t18, 'No', 'no', 'number');
        $createCol($t18, 'Jenis Publikasi', 'jenis');
        $jmlGroup18 = $createCol($t18, 'Jumlah Publikasi', 'jml_group');
        $createCol($t18, 'TS-2', 'ts2', 'number', $jmlGroup18->id);
        $createCol($t18, 'TS-1', 'ts1', 'number', $jmlGroup18->id);
        $createCol($t18, 'TS', 'ts', 'number', $jmlGroup18->id);
        $createCol($t18, 'Jumlah', 'total', 'number');

        // --- TABEL 19: Sitasi Mahasiswa ---
        $t19 = $createTable('tabel_19_sitasi_mhs', 'Tabel 19. Karya Ilmiah Mahasiswa yang Disitasi');
        $createCol($t19, 'No', 'no', 'number');
        $createCol($t19, 'Nama Mahasiswa', 'nama');
        $createCol($t19, 'Judul Artikel', 'judul');
        $createCol($t19, 'Jumlah Sitasi', 'jml_sitasi', 'number');

        // --- TABEL 21: HKI Mahasiswa ---
        $t21 = $createTable('tabel_21_hki_mhs', 'Tabel 21. Produk/HKI/Teknologi Luaran Mahasiswa');
        $createCol($t21, 'No', 'no', 'number');
        $createCol($t21, 'Nama Luaran', 'nama');
        $createCol($t21, 'Tahun', 'tahun', 'number');
        $createCol($t21, 'Keterangan (Paten/HKI/Produk)', 'keterangan');

        // --- TABEL 22: Luaran Riset Lainnya (Mhs) ---
        $t22 = $createTable('tabel_22_luaran_riset_mhs', 'Tabel 22. Luaran Penelitian/PkM Mahasiswa Lainnya');
        $createCol($t22, 'No', 'no', 'number');
        $createCol($t22, 'Jenis Luaran', 'jenis');
        $createCol($t22, 'Judul Luaran', 'judul');
        $createCol($t22, 'Tahun', 'tahun', 'number');

        // --- TABEL 23: Luaran Riset Dosen ---
        $t23 = $createTable('tabel_23_luaran_riset_dosen', 'Tabel 23. Luaran Penelitian/PkM Dosen');
        $createCol($t23, 'No', 'no', 'number');
        $createCol($t23, 'Nama Dosen', 'nama_dosen');
        $createCol($t23, 'Jenis Luaran (Hki/Paten/Buku)', 'jenis');
        $createCol($t23, 'Judul Luaran', 'judul');
        $createCol($t23, 'Tahun', 'tahun', 'number');
        $createCol($t23, 'Keterangan', 'keterangan');
    }
}
