<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LamTable;
use App\Models\LamTableColumn;

class LamembaSeeder extends Seeder
{
    public function run(): void
    {
        $lamType = 'lam-emba';

        // Clear existing for this LAM to avoid duplicates
        $existingTables = LamTable::where('lam_type', $lamType)->get();
        foreach($existingTables as $et) {
            /** @var \App\Models\LamTable $et */
            $et->columns()->delete();
            $et->delete();
        }

        // Helper to create table
        $createTable = function($slug, $label) use ($lamType) {
            return LamTable::create([
                'slug' => $slug, 
                'lam_type' => $lamType,
                'label' => $label
            ]);
        };

        // Helper to create column
        $createCol = function($table, $header, $field, $type = 'text', $parentId = null, $order = 0) {
            return LamTableColumn::create([
                'lam_table_id' => $table->id,
                'header_name' => $header,
                'field_name' => $field,
                'data_type' => $type,
                'parent_id' => $parentId,
                'sort_order' => $order
            ]);
        };

        // --- KRITERIA A: Sumber Daya Manusia ---

        // TABEL 1: Profil Dosen Berdasarkan Status Kepegawaian
        $t1 = $createTable('tabel_1_dosen_status', 'Tabel 1. Profil Dosen Berdasarkan Status Kepegawaian');
        $createCol($t1, 'No.', 'no', 'number');
        $createCol($t1, 'Nama Dosen', 'nama');
        $createCol($t1, 'Status (Tetap/Tidak)', 'status');
        $createCol($t1, 'NIDN/NIDK', 'nidn');
        $createCol($t1, 'Jabatan Akademik', 'jabatan');
        $createCol($t1, 'Akademisi/Praktisi', 'jenis_dosen');
        $createCol($t1, 'Perusahaan/Industri*', 'perusahaan');

        // TABEL 2: Profil Dosen Berdasarkan Latar Belakang Keahlian
        $t2 = $createTable('tabel_2_dosen_keahlian', 'Tabel 2. Profil Dosen Berdasarkan Latar Belakang Keahlian');
        $createCol($t2, 'No.', 'no', 'number');
        $createCol($t2, 'Nama Dosen', 'nama');
        $pnd = $createCol($t2, 'Pendidikan Pasca Sarjana', 'pnd_group');
        $createCol($t2, 'Magister/S2', 's2', 'text', $pnd->id);
        $createCol($t2, 'Doktor/S3', 's3', 'text', $pnd->id);
        $createCol($t2, 'Bidang Keahlian', 'bidang');
        $createCol($t2, 'Sertifikat Pendidik Profesional', 'serdos');
        $createCol($t2, 'Sertifikat Kompetensi/ Profesi/ Industri', 'sertkom');

        // TABEL 3: Profil Dosen Berdasarkan Kontribusi Intelektual
        $t3 = $createTable('tabel_3_dosen_intelektual', 'Tabel 3. Profil Dosen Berdasarkan Kontribusi Intelektual');
        $createCol($t3, 'No.', 'no', 'number');
        $createCol($t3, 'Nama Dosen', 'nama');
        $pndjar = $createCol($t3, 'Pendidikan dan Pengajaran', 'pndjar_group');
        $createCol($t3, 'Mata Kuliah pada PS Akreditasi', 'mk_akre', 'text', $pndjar->id);
        $createCol($t3, 'Mata Kuliah pada PS Lain', 'mk_lain', 'text', $pndjar->id);
        $createCol($t3, 'Judul Bahan Ajar', 'bahan_ajar', 'text', $pndjar->id);
        
        $mhsAkre = $createCol($t3, 'Jumlah Mahasiswa Dibimbing pada PS Akred', 'mhs_akre_group', 'text', $pndjar->id);
        $createCol($t3, 'TS-2', 'akre_ts2', 'number', $mhsAkre->id);
        $createCol($t3, 'TS-1', 'akre_ts1', 'number', $mhsAkre->id);
        $createCol($t3, 'TS', 'akre_ts', 'number', $mhsAkre->id);
        $createCol($t3, 'Rata-rata', 'akre_rata', 'number', $mhsAkre->id);

        $mhsLain = $createCol($t3, 'Jumlah Mahasiswa Dibimbing pada PS Lain', 'mhs_lain_group', 'text', $pndjar->id);
        $createCol($t3, 'TS-2', 'lain_ts2', 'number', $mhsLain->id);
        $createCol($t3, 'TS-1', 'lain_ts1', 'number', $mhsLain->id);
        $createCol($t3, 'TS', 'lain_ts', 'number', $mhsLain->id);
        $createCol($t3, 'Rata-rata', 'lain_rata', 'number', $mhsLain->id);

        $createCol($t3, 'Rata-rata Jumlah Bimbingan', 'rata_bimbingan', 'number', $pndjar->id);
        $createCol($t3, 'Rekognisi Bidang Pnd & Pengajaran', 'rekognisi_pnd', 'text', $pndjar->id);

        $praktik = $createCol($t3, 'Praktik dan Profesional', 'praktik_group');
        $createCol($t3, 'Nama Produk/Jasa', 'praktik_nama', 'text', $praktik->id);
        $createCol($t3, 'Deskripsi', 'praktik_desc', 'text', $praktik->id);
        $createCol($t3, 'Keterlibatan Org', 'praktik_org', 'text', $praktik->id);
        $createCol($t3, 'Rekognisi Praktik', 'rekognisi_praktik', 'text', $praktik->id);

        $riset = $createCol($t3, 'Penelitian', 'riset_group');
        $createCol($t3, 'Judul Artikel Sitasi', 'riset_judul', 'text', $riset->id);
        $createCol($t3, 'Jml Sitasi', 'riset_sitasi', 'number', $riset->id);
        $createCol($t3, 'GS ID', 'gs_id', 'text', $riset->id);
        $createCol($t3, 'Rekognisi Riset', 'rekognisi_riset', 'text', $riset->id);

        $pkm = $createCol($t3, 'Kontribusi Sosial Masyarakat', 'pkm_group');
        $createCol($t3, 'Kegiatan PkM Mandiri', 'pkm_nama', 'text', $pkm->id);
        $createCol($t3, 'Organisasi diluar PS', 'pkm_org', 'text', $pkm->id);
        $createCol($t3, 'Rekognisi PkM', 'rekognisi_pkm', 'text', $pkm->id);

        // TABEL 4: Ekuivalen Waktu Mengajar Penuh (EWMP)
        $t4 = $createTable('tabel_4_ewmp', 'Tabel 4. Ekuivalen Waktu Mengajar Penuh (EWMP)');
        $createCol($t4, 'No.', 'no', 'number');
        $createCol($t4, 'Nama Dosen', 'nama');
        $ewmp = $createCol($t4, 'EWMP (sks) pada TS', 'ewmp_group');
        $pndjar4 = $createCol($t4, 'Pendidikan: Pembelajaran dan Pembimbingan', 'ajar_group', 'text', $ewmp->id);
        $createCol($t4, 'PS diakreditasi', 'sks_akre', 'number', $pndjar4->id);
        $createCol($t4, 'PS Lain di PT', 'sks_lain_pt', 'number', $pndjar4->id);
        $createCol($t4, 'PS Lain luar PT', 'sks_luar_pt', 'number', $pndjar4->id);
        $createCol($t4, 'Penelitian', 'sks_riset', 'number', $ewmp->id);
        $createCol($t4, 'PkM', 'sks_pkm', 'number', $ewmp->id);
        $createCol($t4, 'Tugas Tambahan/Penunjang', 'sks_tugas', 'number', $ewmp->id);
        $createCol($t4, 'Jumlah (sks)', 'total', 'number');
        $createCol($t4, 'Rata-rata per Semester', 'avg_sem', 'number');

        // TABEL 5: Profil Tenaga Kependidikan
        $t5 = $createTable('tabel_5_tendik', 'Tabel 5. Profil Tenaga Kependidikan');
        $createCol($t5, 'No.', 'no', 'number');
        $createCol($t5, 'Nama DTPR (sic. Tendik)', 'nama');
        $createCol($t5, 'Status (Tetap/Kontrak)', 'status');
        $createCol($t5, 'Jabatan', 'jabatan');
        $pnd5 = $createCol($t5, 'Pendidikan', 'pnd_group');
        $createCol($t5, 'Diploma', 'dip', 'text', $pnd5->id);
        $createCol($t5, 'Sarjana', 'sar', 'text', $pnd5->id);
        $createCol($t5, 'Magister', 'mag', 'text', $pnd5->id);
        $createCol($t5, 'Sertifikat Kompetensi/Profesi/Industri', 'sertkom');

        // --- KRITERIA B: Keuangan ---

        // TABEL 6: Profil Keuangan Program Studi yang Diakreditasi
        $t6 = $createTable('tabel_6_keuangan', 'Tabel 6. Profil Keuangan Program Studi yang Diakreditasi');
        $createCol($t6, 'No.', 'no', 'number');
        $createCol($t6, 'Jenis Sumber/Penggunaan', 'jenis');
        $upps = $createCol($t6, 'Unit Pengelola Program Studi (Juta Rp)', 'upps_group');
        $createCol($t6, 'TS-2', 'upps_ts2', 'currency', $upps->id);
        $createCol($t6, 'TS-1', 'upps_ts1', 'currency', $upps->id);
        $createCol($t6, 'TS', 'upps_ts', 'currency', $upps->id);
        $createCol($t6, 'Rata-rata', 'upps_rata', 'currency', $upps->id);

        $ps = $createCol($t6, 'Program Studi yang di Akreditasi (Juta Rp)', 'ps_group');
        $createCol($t6, 'TS-2', 'ps_ts2', 'currency', $ps->id);
        $createCol($t6, 'TS-1', 'ps_ts1', 'currency', $ps->id);
        $createCol($t6, 'TS', 'ps_ts', 'currency', $ps->id);
        $createCol($t6, 'Rata-rata', 'ps_rata', 'currency', $ps->id);

        $perc = $createCol($t6, 'Program Studi yang di Akreditasi (%)', 'perc_group');
        $createCol($t6, 'TS-2', 'perc_ts2', 'text', $perc->id);
        $createCol($t6, 'TS-1', 'perc_ts1', 'text', $perc->id);
        $createCol($t6, 'TS', 'perc_ts', 'text', $perc->id);

        $other = $createCol($t6, 'Program Studi X (PS Lain) (%)', 'other_group');
        $createCol($t6, 'TS-2', 'other_ts2', 'text', $other->id);
        $createCol($t6, 'TS-1', 'other_ts1', 'text', $other->id);
        $createCol($t6, 'TS', 'other_ts', 'text', $other->id);

        // --- KRITERIA C: Luaran dan Capaian Tridharma ---

        // TABEL 7: Prestasi Akademik dan Non-Akademik Mahasiswa
        $t7 = $createTable('tabel_7_prestasi', 'Tabel 7. Prestasi Akademik dan Non-Akademik Mahasiswa');
        $createCol($t7, 'No.', 'no', 'number');
        $createCol($t7, 'Nama Kegiatan', 'nama');
        $createCol($t7, 'Akademik/Non', 'mhw_jenis');
        $createCol($t7, 'Tahun Perolehan', 'tahun', 'number');
        $tk7 = $createCol($t7, 'Tingkat', 'tk_group');
        $createCol($t7, 'Lokal', 'tk_lokal', 'text', $tk7->id);
        $createCol($t7, 'Nasional', 'tk_nasional', 'text', $tk7->id);
        $createCol($t7, 'Internasional', 'tk_inter', 'text', $tk7->id);
        $createCol($t7, 'Prestasi yang Dicapai', 'pencapaian');
        $createCol($t7, 'Standar DIKTI PT', 'standar');

        // Masa Studi (Unified Structure Template)
        $addMasaStudi = function($num, $slug, $label, $levels) use ($createTable, $createCol) {
            $t = $createTable($slug, $label);
            $createCol($t, 'No.', 'no', 'number');
            $createCol($t, 'Tahun Masuk', 'tahun');
            $createCol($t, 'Jumlah Mahasiswa Diterima', 'diterima', 'number');
            $lg = $createCol($t, 'Jumlah Mahasiswa yang Lulus pada', 'lulus_group');
            foreach ($levels as $l) {
                $createCol($t, 'Akhir '.$l, strtolower(str_replace([' ', '-'], '_', $l)), 'number', $lg->id);
            }
            $createCol($t, 'Jumlah Lulusan s.d. Akhir TS', 'total_lulus', 'number');
            $createCol($t, 'Rata-rata Masa Studi', 'avg_studi', 'number');
            $createCol($t, 'Standar DIKTI PT', 'standar');
        };

        $addMasaStudi(8, 'tabel_8_masa_studi_d3', 'Tabel 8. Masa Studi Lulusan (D3)', ['TS-4', 'TS-3', 'TS-2', 'TS-1', 'TS']);
        $addMasaStudi(9, 'tabel_9_masa_studi_s1', 'Tabel 9. Masa Studi Lulusan (S1/D4)', ['TS-6', 'TS-5', 'TS-4', 'TS-3', 'TS-2', 'TS-1', 'TS']);
        $addMasaStudi(10, 'tabel_10_masa_studi_s2', 'Tabel 10. Masa Studi Lulusan (S2)', ['TS-3', 'TS-2', 'TS-1', 'TS']);
        $addMasaStudi(11, 'tabel_11_masa_studi_s3', 'Tabel 11. Masa Studi Lulusan (S3)', ['TS-6', 'TS-5', 'TS-4', 'TS-3', 'TS-2', 'TS-1', 'TS']);

        // Waktu Tunggu
        $t12 = $createTable('tabel_12_waktu_tunggu_d3', 'Tabel 12. Waktu Tunggu Lulusan (D3)');
        $createCol($t12, 'Tahun Lulus', 'tahun');
        $createCol($t12, 'Jml Lulusan', 'lulus', 'number');
        $createCol($t12, 'Terlacak', 'lacak', 'number');
        $createCol($t12, 'Dipesan Sebelum Lulus', 'pesan', 'number');
        $wt12 = $createCol($t12, 'Waktu Tunggu', 'wt_group');
        $createCol($t12, 'WT < 3 bln', 'wt_3', 'number', $wt12->id);
        $createCol($t12, '3 <= WT <= 6 bln', 'wt_3_6', 'number', $wt12->id);
        $createCol($t12, 'WT > 6 bln', 'wt_6', 'number', $wt12->id);
        $createCol($t12, 'Standar DIKTI PT', 'standar');

        $t13 = $createTable('tabel_13_waktu_tunggu_s1', 'Tabel 13. Waktu Tunggu Lulusan (S1)');
        $createCol($t13, 'Tahun Lulus', 'tahun');
        $createCol($t13, 'Jml Lulusan', 'lulus', 'number');
        $createCol($t13, 'Terlacak', 'lacak', 'number');
        $wt13 = $createCol($t13, 'Waktu Tunggu', 'wt_group');
        $createCol($t13, 'WT < 6 bln', 'wt_6', 'number', $wt13->id);
        $createCol($t13, '6 <= WT <= 18 bln', 'wt_6_18', 'number', $wt13->id);
        $createCol($t13, 'WT > 18 bln', 'wt_18', 'number', $wt13->id);
        $createCol($t13, 'Standar DIKTI PT', 'standar');

        $t14 = $createTable('tabel_14_waktu_tunggu_d4', 'Tabel 14. Waktu Tunggu Lulusan (D4)');
        $createCol($t14, 'Tahun Lulus', 'tahun');
        $createCol($t14, 'Jml Lulusan', 'lulus', 'number');
        $createCol($t14, 'Terlacak', 'lacak', 'number');
        $wt14 = $createCol($t14, 'Waktu Tunggu', 'wt_group');
        $createCol($t14, 'WT < 3 bln', 'wt_3', 'number', $wt14->id);
        $createCol($t14, '3 <= WT <= 6 bln', 'wt_3_6', 'number', $wt14->id);
        $createCol($t14, 'WT > 6 bln', 'wt_6', 'number', $wt14->id);
        $createCol($t14, 'Standar DIKTI PT', 'standar');

        // TABEL 15: Kesesuaian Bidang Kerja Lulusan
        $t15 = $createTable('tabel_15_kesesuaian_kerja', 'Tabel 15. Kesesuaian Bidang Kerja Lulusan');
        $createCol($t15, 'Tahun Lulus', 'tahun');
        $createCol($t15, 'Jml Lulusan', 'lulus', 'number');
        $createCol($t15, 'Terlacak', 'lacak', 'number');
        $tk15 = $createCol($t15, 'Tingkat Kesesuaian', 'tk_group');
        $createCol($t15, 'Tidak Sesuai', 'non', 'number', $tk15->id);
        $createCol($t15, 'Sesuai', 'fit', 'number', $tk15->id);
        $createCol($t15, 'Kesesuaian Target Profil', 'target');

        // TABEL 16: Jangkauan Operasi Kerja Lulusan
        $t16 = $createTable('tabel_16_jangkauan_kerja', 'Tabel 16. Jangkauan Operasi Kerja Lulusan');
        $createCol($t16, 'Tahun Lulus', 'tahun');
        $createCol($t16, 'Jml Lulusan', 'lulus', 'number');
        $createCol($t16, 'Terlacak', 'lacak', 'number');
        $jk16 = $createCol($t16, 'Jangkauan/Ukuran', 'jk_group');
        $createCol($t16, 'Lokal/Wilayah/Wirausaha non-izin', 'lok', 'number', $jk16->id);
        $createCol($t16, 'Nasional/Wirausaha berizin', 'nas', 'number', $jk16->id);
        $createCol($t16, 'Multinasional/Internasional', 'inter', 'number', $jk16->id);
        $createCol($t16, 'Kesesuaian Target Profil', 'target');

        // TABEL 17: Kepuasan Pengguna
        $t17 = $createTable('tabel_17_kepuasan_pengguna', 'Tabel 17. Kepuasan Pengguna');
        $createCol($t17, 'No.', 'no', 'number');
        $createCol($t17, 'Jenis Kemampuan', 'jenis');
        $kp17 = $createCol($t17, 'Tingkat Kepuasan (%)', 'kp_group');
        $createCol($t17, 'Sangat Baik', 'sb', 'number', $kp17->id);
        $createCol($t17, 'Baik', 'b', 'number', $kp17->id);
        $createCol($t17, 'Cukup', 'c', 'number', $kp17->id);
        $createCol($t17, 'Kurang', 'k', 'number', $kp17->id);
        $createCol($t17, 'Rencana Tindak Lanjut', 'rtl');
        $createCol($t17, 'Kesesuaian Target Profil', 'target');

        // Pubs Tables
        $addPubTable = function($num, $slug, $label) use ($createTable, $createCol) {
            $t = $createTable($slug, $label);
            $createCol($t, 'No.', 'no', 'number');
            $createCol($t, 'Media Publikasi / Jenis', 'media');
            $jml = $createCol($t, 'Jumlah Judul', 'jml_group');
            $createCol($t, 'TS-2', 'ts2', 'number', $jml->id);
            $createCol($t, 'TS-1', 'ts1', 'number', $jml->id);
            $createCol($t, 'TS', 'ts', 'number', $jml->id);
            $createCol($t, 'Jumlah', 'total', 'number');
            $createCol($t, 'Standar DIKTI PT', 'standar');
        };

        $addPubTable(18, 'tabel_18_publikasi_mhs', 'Tabel 18. Publikasi Ilmiah Mahasiswa (S2/S3)');
        $addPubTable(19, 'tabel_19_pagelaran_mhs', 'Tabel 19. Pagelaran/Pameran/Publikasi Ilmiah Mahasiswa');

        // TABEL 20: Karya Ilmiah Mahasiswa yang Disitasi
        $t20 = $createTable('tabel_20_sitasi_mhs', 'Tabel 20. Karya Ilmiah Mahasiswa yang Disitasi');
        $createCol($t20, 'No.', 'no', 'number');
        $createCol($t20, 'Nama Mahasiswa', 'nama');
        $createCol($t20, 'Judul Artikel (Jurnal, Vol, No)', 'judul');
        $createCol($t20, 'Jumlah Sitasi', 'sitasi', 'number');
        $createCol($t20, 'Standar DIKTI PT', 'standar');

        // TABEL 21: Produk/Jasa yang Dihasilkan Mahasiswa Diadopsi Industri
        $t21 = $createTable('tabel_21_produk_mhs', 'Tabel 21. Produk/Jasa Mahasiswa Diadopsi Industri');
        $createCol($t21, 'No.', 'no', 'number');
        $createCol($t21, 'Nama Mahasiswa', 'nama');
        $createCol($t21, 'Nama Produk/Jasa', 'produk');
        $createCol($t21, 'Deskripsi', 'desc');
        $createCol($t21, 'Bukti', 'bukti');
        $createCol($t21, 'Standar DIKTI PT', 'standar');

        // TABEL 22: Luaran Penelitian/PkM Mahasiswa
        $t22 = $createTable('tabel_22_luaran_mhs', 'Tabel 22. Luaran Penelitian/PkM Mahasiswa');
        $createCol($t22, 'No.', 'no', 'number');
        $createCol($t22, 'Judul Luaran', 'judul');
        $createCol($t22, 'Tahun', 'tahun', 'number');
        $createCol($t22, 'Keterangan', 'ket');
        $createCol($t22, 'Standar DIKTI PT', 'standar');

        // TABEL 23: Luaran Penelitian/PkM yang Dihasilkan oleh Dosen
        $t23 = $createTable('tabel_23_luaran_dosen', 'Tabel 23. Luaran Penelitian/PkM Dosen');
        $createCol($t23, 'No.', 'no', 'number');
        $createCol($t23, 'Luaran Pen & PkM', 'judul');
        $createCol($t23, 'Tahun', 'tahun', 'number');
        $createCol($t23, 'Keterangan', 'ket');
        $src = $createCol($t23, 'Sumber Pembiayaan', 'src_group');
        $createCol($t23, 'PT/Mandiri', 'src_pt', 'number', $src->id);
        $createCol($t23, 'Lembaga DN', 'src_dn', 'number', $src->id);
        $createCol($t23, 'Lembaga LN', 'src_ln', 'number', $src->id);
        $pub = $createCol($t23, 'Jenis Publikasi (Checkbox Columns)', 'pub_group');
        for($i=7; $i<=19; $i++) {
            $createCol($t23, 'Media '.$i, 'pub_'.$i, 'text', $pub->id);
        }
    }
}
