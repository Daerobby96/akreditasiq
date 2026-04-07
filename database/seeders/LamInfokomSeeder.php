<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LamInfokomSeeder extends Seeder
{
    public function run(): void
    {
        $lamType = 'lam-infokom';

        // Clear existing for this LAM to avoid duplicates
        $existingTables = \App\Models\LamTable::where('lam_type', $lamType)->get();
        foreach($existingTables as $et) {
            $et->columns()->delete();
            $et->delete();
        }

        // Helper functions
        $createTable = function($slug, $label) use ($lamType) {
            return \App\Models\LamTable::create(['slug' => $slug, 'lam_type' => $lamType, 'label' => $label]);
        };

        $createCol = function($table, $header, $field, $type = 'text', $parentId = null, $order = 0) {
            return \App\Models\LamTableColumn::create([
                'lam_table_id' => $table->id, 'header_name' => $header, 'field_name' => $field,
                'data_type' => $type, 'parent_id' => $parentId, 'sort_order' => $order
            ]);
        };

        // --- 1. KERJASAMA ---
        $t1 = $createTable('tabel_1_kerjasama', 'Tabel 1. Kerjasama Tridharma');
        $createCol($t1, 'No', 'no', 'number');
        $createCol($t1, 'Lembaga Mitra', 'mitra');
        $createCol($t1, 'Tingkat (Lokal/Nas/Inter)', 'tingkat');
        $createCol($t1, 'Bentuk Kegiatan', 'bentuk');
        $createCol($t1, 'Manfaat Bagi PS', 'manfaat');
        $createCol($t1, 'Bukti Kerjasama', 'bukti');

        // --- 2. MAHASISWA BARU ---
        $t2 = $createTable('tabel_2_seleksi_mhs', 'Tabel 2. Seleksi Mahasiswa Baru');
        $createCol($t2, 'Tahun Akademik', 'tahun');
        $createCol($t2, 'Daya Tampung', 'dt', 'number');
        $createCol($t2, 'Pendaftar', 'pendaftar', 'number');
        $createCol($t2, 'Lulus Seleksi', 'lulus', 'number');
        $createCol($t2, 'Maba Reguler', 'maba_reg', 'number');
        $createCol($t2, 'Maba Transfer', 'maba_tra', 'number');
        $createCol($t2, 'Total Reguler', 'sb_reg', 'number');
        $createCol($t2, 'Total Transfer', 'sb_tra', 'number');

        // --- 3. MAHASISWA ASING ---
        $t3 = $createTable('tabel_3_mhs_asing', 'Tabel 3. Mahasiswa Asing');
        $createCol($t3, 'No', 'no', 'number');
        $createCol($t3, 'Nama Program Studi', 'prodi');
        $createCol($t3, 'TS-2', 'ts2', 'number');
        $createCol($t3, 'TS-1', 'ts1', 'number');
        $createCol($t3, 'TS', 'ts', 'number');

        // --- 4. DATA DOSEN (DTPS) ---
        $t4 = $createTable('tabel_4_dtps', 'Tabel 4. Profil Dosen Tetap PS');
        $createCol($t4, 'No', 'no', 'number');
        $createCol($t4, 'Nama Dosen', 'nama');
        $createCol($t4, 'NIDN/NIDK', 'nidn');
        $createCol($t4, 'Pendidikan S2', 's2');
        $createCol($t4, 'Pendidikan S3', 's3');
        $createCol($t4, 'Bidang Keahlian', 'bidang');
        $createCol($t4, 'Jabatan Akademik', 'jabatan');
        $createCol($t4, 'Sertifikat Pendidik', 'serdos');
        $createCol($t4, 'Sertifikat Kompetensi IT', 'sertkom');

        // --- 8. EWMP ---
        $t8 = $createTable('tabel_8_ewmp', 'Tabel 8. EWMP Dosen Tetap PT');
        $createCol($t8, 'No', 'no', 'number');
        $createCol($t8, 'Nama Dosen', 'nama');
        $createCol($t8, 'SKS Pendidikan', 'sks_pnd', 'number');
        $createCol($t8, 'SKS Penelitian', 'sks_res', 'number');
        $createCol($t8, 'SKS PkM', 'sks_pkm', 'number');
        $createCol($t8, 'SKS Manajemen', 'sks_man', 'number');
        $createCol($t8, 'Jumlah SKS', 'total', 'number');

        // --- 11. PENELITIAN DTPS ---
        $t11 = $createTable('tabel_11_riset_dtps', 'Tabel 11. Penelitian DTPS');
        $createCol($t11, 'No', 'no', 'number');
        $createCol($t11, 'Sumber Pendanaan', 'sumber');
        $createCol($t11, 'Jumlah Judul TS-2', 'ts2', 'number');
        $createCol($t11, 'Jumlah Judul TS-1', 'ts1', 'number');
        $createCol($t11, 'Jumlah Judul TS', 'ts', 'number');
        $createCol($t11, 'Total', 'total', 'number');

        // --- 13. PUBLIKASI DTPS ---
        $t13 = $createTable('tabel_13_publikasi_dtps', 'Tabel 13. Publikasi Ilmiah DTPS');
        $createCol($t13, 'No', 'no', 'number');
        $createCol($t13, 'Jenis Publikasi', 'jenis');
        $createCol($t13, 'TS-2', 'ts2', 'number');
        $createCol($t13, 'TS-1', 'ts1', 'number');
        $createCol($t13, 'TS', 'ts', 'number');
        $createCol($t13, 'Total', 'total', 'number');

        // --- 19. DANA KEUANGAN ---
        $t19 = $createTable('tabel_19_dana', 'Tabel 19. Penggunaan Dana UPPS & PS');
        $createCol($t19, 'No', 'no', 'number');
        $createCol($t19, 'Jenis Penggunaan', 'jenis');
        $createCol($t19, 'TS-2', 'ts2', 'currency');
        $createCol($t19, 'TS-1', 'ts1', 'currency');
        $createCol($t19, 'TS', 'ts', 'currency');
        $createCol($t19, 'Rata-rata', 'avg', 'currency');

        // --- 24. MASA STUDI S1 ---
        $t24 = $createTable('tabel_24_masa_studi', 'Tabel 24. Masa Studi & Kelulusan S1');
        $createCol($t24, 'No', 'no', 'number');
        $createCol($t24, 'Tahun Masuk', 'tahun');
        $createCol($t24, 'Mhs Diterima', 'jml', 'number');
        $lg24 = $createCol($t24, 'Jumlah Lulusan pada Akhir', 'lg_24');
        $createCol($t24, 'TS-6', 'ts6', 'number', $lg24->id);
        $createCol($t24, 'TS-5', 'ts5', 'number', $lg24->id);
        $createCol($t24, 'TS-4', 'ts4', 'number', $lg24->id);
        $createCol($t24, 'TS-3', 'ts3', 'number', $lg24->id);
        $createCol($t24, 'TS-2', 'ts2', 'number', $lg24->id);
        $createCol($t24, 'TS-1', 'ts1', 'number', $lg24->id);
        $createCol($t24, 'TS', 'ts', 'number', $lg24->id);
        $createCol($t24, 'Rata-rata Masa Studi', 'avg_studi', 'number');

        // --- 25. WAKTU TUNGGU ---
        $t25 = $createTable('tabel_25_waktu_tunggu', 'Tabel 25. Waktu Tunggu Lulusan');
        $createCol($t25, 'No', 'no', 'number');
        $createCol($t25, 'Tahun Lulus', 'tahun');
        $createCol($t25, 'Jml Lulusan', 'jml', 'number');
        $createCol($t25, 'Jml Terlacak', 'lacak', 'number');
        $wtG = $createCol($t25, 'Waktu Tunggu Bekerja', 'wt_g');
        $createCol($t25, '< 3 bln', 'wt3', 'number', $wtG->id);
        $createCol($t25, '3-6 bln', 'wt36', 'number', $wtG->id);
        $createCol($t25, '> 6 bln', 'wt6', 'number', $wtG->id);

        // --- 28. KEPUASAN PENGGUNA ---
        $t28 = $createTable('tabel_28_kepuasan_pengguna', 'Tabel 28. Kepuasan Pengguna Lulusan');
        $createCol($t28, 'No', 'no', 'number');
        $createCol($t28, 'Jenis Kemampuan', 'jenis');
        $kpG = $createCol($t28, 'Tingkat Kepuasan (%)', 'kp_g');
        $createCol($t28, 'Sangat Baik', 'sb', 'number', $kpG->id);
        $createCol($t28, 'Baik', 'b', 'number', $kpG->id);
        $createCol($t28, 'Cukup', 'c', 'number', $kpG->id);
        $createCol($t28, 'Kurang', 'k', 'number', $kpG->id);

        // --- 29. PUBLIKASI MHS ---
        $t29 = $createTable('tabel_29_publikasi_mhs', 'Tabel 29. Publikasi Ilmiah Mahasiswa');
        $createCol($t29, 'No', 'no', 'number');
        $createCol($t29, 'Jenis Publikasi', 'jenis');
        $createCol($t29, 'Jumlah TS-2', 'ts2', 'number');
        $createCol($t29, 'Jumlah TS-1', 'ts1', 'number');
        $createCol($t29, 'Jumlah TS', 'ts', 'number');
        $createCol($t29, 'Total', 'total', 'number');

        // Loop remaining missing tables (placeholders with basic columns for consistency)
        $missingLabels = [
            5 => 'Tabel 5. DTPS Bidang Keahlian Tidak Sesuai',
            6 => 'Tabel 6. Dosen Tetap Perguruan Tinggi',
            7 => 'Tabel 7. Dosen Tidak Tetap',
            9 => 'Tabel 9. Dosen Pembimbing Utama Tugas Akhir',
            10 => 'Tabel 10. Rekognisi/Kepakaran DTPS',
            12 => 'Tabel 12. PkM DTPS',
            14 => 'Tabel 14. Artikel Ilmiah DTPS Disitasi',
            15 => 'Tabel 15. Produk/Jasa DTPS Diadopsi Industri',
            16 => 'Tabel 16. Luaran HKI/Paten DTPS',
            17 => 'Tabel 17. Penelitian DTPS & Mhs',
            18 => 'Tabel 18. PkM DTPS & Mhs',
            20 => 'Tabel 20. Kurikulum & Capaian',
            21 => 'Tabel 21. Kepuasan Mahasiswa',
            22 => 'Tabel 22. Prestasi Akademik Mahasiswa',
            23 => 'Tabel 23. Prestasi Non-akademik',
            26 => 'Tabel 26. Kesesuaian Bidang Kerja',
            27 => 'Tabel 27. Tempat Kerja Lulusan',
            30 => 'Tabel 30. Produk Mahasiswa Diadopsi Industri',
            31 => 'Tabel 31. Luaran HKI/Paten Mahasiswa'
        ];

        foreach ($missingLabels as $num => $label) {
            $t = $createTable("tabel_{$num}_infokom", $label);
            $createCol($t, 'No', 'no', 'number');
            $createCol($t, 'Keterangan/Aspek', 'keterangan');
            $createCol($t, 'Capaian/Jumlah', 'jumlah', 'number');
            $createCol($t, 'Tahun Akademik', 'tahun');
            $createCol($t, 'Bukti Pendukung', 'bukti');
        }
    }
}
