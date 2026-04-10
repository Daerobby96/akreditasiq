<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LamTable;
use App\Models\LamTableColumn;

class LamInfokomSeeder extends Seeder
{
    public function run(): void
    {
        $lamType = 'lam-infokom';

        // Clear existing for this LAM to avoid duplicates
        $existingTables = LamTable::where('lam_type', $lamType)->get();
        foreach($existingTables as $et) {
            /** @var \App\Models\LamTable $et */
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

        // --- CRITERION 1: Budaya Mutu ---
        
        // Tabel 1.A.1 Tabel Pimpinan dan Tupoksi UPPS dan PS
        $t1a1 = $createTable('tabel_1_a_1_pimpinan', 'Tabel 1.A.1 Tabel Pimpinan dan Tupoksi UPPS dan PS');
        $createCol($t1a1, 'Unit Kerja', 'unit_kerja');
        $createCol($t1a1, 'Nama Ketua', 'nama_ketua');
        $createCol($t1a1, 'Periode Jabatan', 'periode');
        $createCol($t1a1, 'Pendidikan Terakhir', 'pendidikan');
        $createCol($t1a1, 'Jabatan Fungsional', 'jabatan');
        $createCol($t1a1, 'Tugas Pokok dan Fungsi', 'tupoksi');

        // Tabel 1.A.2 Sumber Pendanaan UPPS/PS
        $t1a2 = $createTable('tabel_1_a_2_sumber_dana', 'Tabel 1.A.2 Sumber Pendanaan UPPS/PS');
        $createCol($t1a2, 'Sumber Pendanaan', 'sumber');
        $createCol($t1a2, 'TS-2', 'ts2', 'currency');
        $createCol($t1a2, 'TS-1', 'ts1', 'currency');
        $createCol($t1a2, 'TS', 'ts', 'currency');
        $createCol($t1a2, 'Link Bukti', 'bukti');

        // Tabel 1.A.3 Penggunaan Dana UPPS/PS
        $t1a3 = $createTable('tabel_1_a_3_penggunaan_dana', 'Tabel 1.A.3 Penggunaan Dana UPPS/PS');
        $createCol($t1a3, 'Penggunaan Dana', 'penggunaan');
        $createCol($t1a3, 'TS-2', 'ts2', 'currency');
        $createCol($t1a3, 'TS-1', 'ts1', 'currency');
        $createCol($t1a3, 'TS', 'ts', 'currency');
        $createCol($t1a3, 'Link Bukti', 'bukti');

        // Tabel 1.A.4 Rata-rata Beban DTPR per semester (EWMP) pada TS
        $t1a4 = $createTable('tabel_1_a_4_ewmp', 'Tabel 1.A.4 Rata-rata Beban DTPR per semester (EWMP) pada TS');
        $createCol($t1a4, 'No', 'no', 'number');
        $createCol($t1a4, 'Nama DTPR', 'nama');
        $sksAjar = $createCol($t1a4, 'SKS Pengajaran Pada', 'sks_ajar_group');
        $createCol($t1a4, 'PS Sendiri', 'sks_ajar_ps', 'number', $sksAjar->id);
        $createCol($t1a4, 'PS Lain, PT Sendiri', 'sks_ajar_luar_ps', 'number', $sksAjar->id);
        $createCol($t1a4, 'PT Lain', 'sks_ajar_luar_pt', 'number', $sksAjar->id);
        $createCol($t1a4, 'SKS Penelitian', 'sks_riset', 'number');
        $createCol($t1a4, 'SKS Pengabdian kepada Masyarakat', 'sks_pkm', 'number');
        $sksMan = $createCol($t1a4, 'SKS Manajemen', 'sks_man_group');
        $createCol($t1a4, 'PT Sendiri', 'sks_man_pt', 'number', $sksMan->id);
        $createCol($t1a4, 'PT Lain', 'sks_man_luar_pt', 'number', $sksMan->id);
        $createCol($t1a4, 'Total SKS', 'total', 'number');

        // Tabel 1.A.5 Kualifikasi Tenaga Kependidikan
        $t1a5 = $createTable('tabel_1_a_5_tendik', 'Tabel 1.A.5 Kualifikasi Tenaga Kependidikan');
        $createCol($t1a5, 'No', 'no', 'number');
        $createCol($t1a5, 'Jenis Tenaga Kependidikan', 'jenis');
        $pnd = $createCol($t1a5, 'Jumlah Tenaga Kependidikan dengan Pendidikan Terakhir', 'pnd_group');
        $createCol($t1a5, 'S3', 's3', 'number', $pnd->id);
        $createCol($t1a5, 'S2', 's2', 'number', $pnd->id);
        $createCol($t1a5, 'S1', 's1', 'number', $pnd->id);
        $createCol($t1a5, 'D4', 'd4', 'number', $pnd->id);
        $createCol($t1a5, 'D3', 'd3', 'number', $pnd->id);
        $createCol($t1a5, 'D2', 'd2', 'number', $pnd->id);
        $createCol($t1a5, 'D1', 'd1', 'number', $pnd->id);
        $createCol($t1a5, 'SMA/SMK/MA', 'sma', 'number', $pnd->id);
        $createCol($t1a5, 'Unit Kerja', 'unit_kerja');

        // Tabel 1.B Tabel Unit SPMI dan SDM
        $t1b = $createTable('tabel_1_b_spmi', 'Tabel 1.B Tabel Unit SPMI dan SDM');
        $createCol($t1b, 'Unit SPMI', 'unit');
        $createCol($t1b, 'Nama Unit SPMI', 'nama');
        $createCol($t1b, 'Dokumen SPMI', 'dokumen');
        $auditor = $createCol($t1b, 'Jumlah Auditor Mutu Internal', 'auditor_group');
        $createCol($t1b, 'Certified', 'certified', 'number', $auditor->id);
        $createCol($t1b, 'Non Certified', 'non_certified', 'number', $auditor->id);
        $createCol($t1b, 'Frekuensi audit/monev per tahun', 'frekuensi', 'number');
        $createCol($t1b, 'Bukti Certified Auditor', 'bukti_certified');
        $createCol($t1b, 'Laporan Audit', 'laporan');

        // --- CRITERION 2: Relevansi Pendidikan ---
        
        // Tabel 2.A.1 Data Mahasiswa
        $t2a1 = $createTable('tabel_2_a_1_data_mhs', 'Tabel 2.A.1 Data Mahasiswa');
        $createCol($t2a1, 'TS', 'ts');
        $createCol($t2a1, 'Daya Tampung', 'dt', 'number');
        $calon = $createCol($t2a1, 'Jumlah Calon Mahasiswa', 'calon_group');
        $createCol($t2a1, 'Pendaftar', 'pendaftar', 'number', $calon->id);
        $createCol($t2a1, 'Pendaftar Afirmasi', 'pendaftar_afirmasi', 'number', $calon->id);
        $createCol($t2a1, 'Pendaftar Kebutuhan Khusus', 'pendaftar_khusus', 'number', $calon->id);
        
        $maba = $createCol($t2a1, 'Jumlah Mahasiswa Baru', 'maba_group');
        $regMaba = $createCol($t2a1, 'Reguler', 'reg_maba_group', 'text', $maba->id);
        $createCol($t2a1, 'Diterima', 'reg_maba_diterima', 'number', $regMaba->id);
        $createCol($t2a1, 'Afirmasi', 'reg_maba_afirmasi', 'number', $regMaba->id);
        $createCol($t2a1, 'Kebutuhan Khusus', 'reg_maba_khusus', 'number', $regMaba->id);
        $rplMaba = $createCol($t2a1, 'RPL', 'rpl_maba_group', 'text', $maba->id);
        $createCol($t2a1, 'Diterima', 'rpl_maba_diterima', 'number', $rplMaba->id);
        $createCol($t2a1, 'Afirmasi', 'rpl_maba_afirmasi', 'number', $rplMaba->id);
        $createCol($t2a1, 'Kebutuhan Khusus', 'rpl_maba_khusus', 'number', $rplMaba->id);

        $maktif = $createCol($t2a1, 'Jumlah Mahasiswa Aktif', 'maktif_group');
        $regMaktif = $createCol($t2a1, 'Reguler', 'reg_maktif_group', 'text', $maktif->id);
        $createCol($t2a1, 'Diterima', 'reg_maktif_diterima', 'number', $regMaktif->id);
        $createCol($t2a1, 'Afirmasi', 'reg_maktif_afirmasi', 'number', $regMaktif->id);
        $createCol($t2a1, 'Kebutuhan Khusus', 'reg_maktif_khusus', 'number', $regMaktif->id);
        $rplMaktif = $createCol($t2a1, 'RPL', 'rpl_maktif_group', 'text', $maktif->id);
        $createCol($t2a1, 'Diterima', 'rpl_maktif_diterima', 'number', $rplMaktif->id);
        $createCol($t2a1, 'Afirmasi', 'rpl_maktif_afirmasi', 'number', $rplMaktif->id);
        $createCol($t2a1, 'Kebutuhan Khusus', 'rpl_maktif_khusus', 'number', $rplMaktif->id);

        // Tabel 2.A.2 Keragaman Asal Mahasiswa
        $t2a2 = $createTable('tabel_2_a_2_asal_mhs', 'Tabel 2.A.2 Keragaman Asal Mahasiswa');
        $createCol($t2a2, 'Asal Mahasiswa', 'asal');
        $jmaba = $createCol($t2a2, 'Jumlah Mahasiswa Baru', 'jmaba_group');
        $createCol($t2a2, 'TS-2', 'ts2', 'number', $jmaba->id);
        $createCol($t2a2, 'TS-1', 'ts1', 'number', $jmaba->id);
        $createCol($t2a2, 'TS', 'ts', 'number', $jmaba->id);
        $createCol($t2a2, 'Link Bukti', 'bukti');

        // Tabel 2.A.3 Kondisi Jumlah Mahasiswa
        $t2a3 = $createTable('tabel_2_a_3_kondisi_mhs', 'Tabel 2.A.3 Kondisi Jumlah Mahasiswa');
        $createCol($t2a3, 'Keterangan', 'keterangan');
        $createCol($t2a3, 'TS-2', 'ts2', 'number');
        $createCol($t2a3, 'TS-1', 'ts1', 'number');
        $createCol($t2a3, 'TS', 'ts', 'number');
        $createCol($t2a3, 'Jumlah', 'total', 'number');

        // Tabel 2.B.1 Tabel Isi Pembelajaran
        $t2b1 = $createTable('tabel_2_b_1_pembelajaran', 'Tabel 2.B.1 Tabel Isi Pembelajaran');
        $createCol($t2b1, 'No', 'no', 'number');
        $createCol($t2b1, 'Mata Kuliah', 'mk');
        $createCol($t2b1, 'SKS', 'sks', 'number');
        $createCol($t2b1, 'Semester', 'semester', 'number');
        $pl = $createCol($t2b1, 'Profil Lulusan (PL)', 'pl_group');
        $createCol($t2b1, 'PL 1', 'pl1', 'text', $pl->id);
        $createCol($t2b1, 'PL 2', 'pl2', 'text', $pl->id);
        $createCol($t2b1, '...', 'pl_etc', 'text', $pl->id);
        $createCol($t2b1, 'PL n', 'pln', 'text', $pl->id);

        // Tabel 2.B.2 Pemetaan Capaian Pembelajaran Lulusan dan Profil Lulusan
        $t2b2 = $createTable('tabel_2_b_2_pemetaan_cpl', 'Tabel 2.B.2 Pemetaan Capaian Pembelajaran Lulusan dan Profil Lulusan');
        $createCol($t2b2, 'CPL / PL', 'cpl_pl');
        $createCol($t2b2, 'PL 1', 'pl1');
        $createCol($t2b2, 'PL 2', 'pl2');
        $createCol($t2b2, '...', 'etc');
        $createCol($t2b2, 'PL n', 'pln');

        // Tabel 2.B.3 Peta Pemenuhan CPL
        $t2b3 = $createTable('tabel_2_b_3_peta_cpl', 'Tabel 2.B.3 Peta Pemenuhan CPL');
        $createCol($t2b3, 'CPL', 'cpl');
        $createCol($t2b3, 'CPMK', 'cpmk');
        $createCol($t2b3, 'Semester 1', 's1');
        $createCol($t2b3, 'Semester 2', 's2');
        $createCol($t2b3, 'Semester 3', 's3');
        $createCol($t2b3, 'Semester 4', 's4');
        $createCol($t2b3, 'Semester 5', 's5');
        $createCol($t2b3, 'Semester 6', 's6');
        $createCol($t2b3, '...', 'etc');

        // Tabel 2.B.4 Rata-rata Masa Tunggu Lulusan untuk Bekerja Pertama Kali
        $t2b4 = $createTable('tabel_2_b_4_masa_tunggu', 'Tabel 2.B.4 Rata-rata Masa Tunggu Lulusan untuk Bekerja Pertama Kali');
        $createCol($t2b4, 'Tahun Lulus', 'tahun');
        $createCol($t2b4, 'Jumlah Lulusan', 'jml_lulus', 'number');
        $createCol($t2b4, 'Jumlah Lulusan yang Terlacak', 'lacak', 'number');
        $createCol($t2b4, 'Rata-rata Waktu Tunggu (Bulan)', 'avg_wt', 'number');

        // Tabel 2.B.5 Kesesuaian Bidang Kerja Lulusan
        $t2b5 = $createTable('tabel_2_b_5_kesesuaian_kerja', 'Tabel 2.B.5 Kesesuaian Bidang Kerja Lulusan');
        $createCol($t2b5, 'Tahun Lulus', 'tahun');
        $createCol($t2b5, 'Jumlah Lulusan', 'jml_lulus', 'number');
        $createCol($t2b5, 'Jumlah Lulusan yang Terlacak', 'lacak', 'number');
        $createCol($t2b5, 'Profesi Kerja Bidang Infokom', 'profesi_infokom', 'number');
        $createCol($t2b5, 'Profesi Kerja Bidang NON Infokom', 'profesi_non_infokom', 'number');
        $lingkup = $createCol($t2b5, 'Lingkup Tempat Kerja', 'lingkup_group');
        $createCol($t2b5, 'Multinasional/ Internasional', 'multi_inter', 'number', $lingkup->id);
        $createCol($t2b5, 'Nasional', 'nasional', 'number', $lingkup->id);
        $createCol($t2b5, 'Wirausaha', 'wirausaha', 'number', $lingkup->id);

        // Tabel 2.B.6 Kepuasan Pengguna Lulusan
        $t2b6 = $createTable('tabel_2_b_6_kepuasan_pengguna', 'Tabel 2.B.6 Kepuasan Pengguna Lulusan');
        $createCol($t2b6, 'No', 'no', 'number');
        $createCol($t2b6, 'Jenis Kemampuan', 'jenis');
        $tkp = $createCol($t2b6, 'Tingkat Kepuasan Pengguna (%)', 'tkp_group');
        $createCol($t2b6, 'Sangat Baik', 'sb', 'number', $tkp->id);
        $createCol($t2b6, 'Baik', 'b', 'number', $tkp->id);
        $createCol($t2b6, 'Cukup', 'c', 'number', $tkp->id);
        $createCol($t2b6, 'Kurang', 'k', 'number', $tkp->id);
        $createCol($t2b6, 'Rencana Tindak Lanjut oleh UPPS/PS', 'rtl');

        // Tabel 2.C Fleksibilitas Dalam Proses Pembelajaran
        $t2c = $createTable('tabel_2_c_fleksibilitas', 'Tabel 2.C Fleksibilitas Dalam Proses Pembelajaran');
        $createCol($t2c, 'Tahun Akademik', 'tahun');
        $createCol($t2c, 'Jumlah Mahasiswa Aktif', 'maktif', 'number');
        $bentuk = $createCol($t2c, 'Bentuk Pembelajaran', 'bentuk_group');
        $createCol($t2c, 'Micro-credensial', 'micro', 'number', $bentuk->id);
        $createCol($t2c, 'RPL tipe A-2', 'rpl', 'number', $bentuk->id);
        $createCol($t2c, 'Pembelajaran di PS lain', 'ps_lain', 'number', $bentuk->id);
        $createCol($t2c, 'Pembelajaran di PT lain', 'pt_lain', 'number', $bentuk->id);
        $createCol($t2c, 'CBL/ PBL', 'cbl_pbl', 'number', $bentuk->id);
        $createCol($t2c, '...', 'etc', 'number', $bentuk->id);
        $createCol($t2c, 'Link Bukti', 'bukti');

        // Tabel 2.D Rekognisi dan Apresiasi Kompetensi Lulusan
        $t2d = $createTable('tabel_2_d_rekognisi_lulusan', 'Tabel 2.D Rekognisi dan Apresiasi Kompetensi Lulusan');
        $createCol($t2d, 'Sumber Rekognisi', 'sumber');
        $createCol($t2d, 'Jenis Pengakuan Lulusan (Rekognisi)', 'jenis');
        $tahun = $createCol($t2d, 'Tahun Akademik', 'tahun_group');
        $createCol($t2d, 'TS-2', 'ts2', 'number', $tahun->id);
        $createCol($t2d, 'TS-1', 'ts1', 'number', $tahun->id);
        $createCol($t2d, 'TS', 'ts', 'number', $tahun->id);
        $createCol($t2d, 'Link Bukti', 'bukti');

        // --- CRITERION 3: Relevansi Penelitian ---

        // Tabel 3.A.1 Sarana dan Prasarana Penelitian
        $t3a1 = $createTable('tabel_3_a_1_sarpras_riset', 'Tabel 3.A.1 Sarana dan Prasarana Penelitian');
        $createCol($t3a1, 'Nama Prasarana', 'nama');
        $createCol($t3a1, 'Daya Tampung', 'dt', 'number');
        $createCol($t3a1, 'Luas Ruang (m2)', 'luas', 'number');
        $createCol($t3a1, 'Milik sendiri (M)/Sewa (W)', 'milik');
        $createCol($t3a1, 'Berlisensi (L)/ Public Domain (P)/Tidak Berlisensi (T)', 'lisensi');
        $createCol($t3a1, 'Perangkat .....', 'perangkat');
        $createCol($t3a1, 'Link Bukti', 'bukti');

        // Tabel 3.A.2 Penelitian DTPR, Hibah dan Pembiayaan Penelitian
        $t3a2 = $createTable('tabel_3_a_2_hibah_riset', 'Tabel 3.A.2 Penelitian DTPR, Hibah dan Pembiayaan Penelitian');
        $createCol($t3a2, 'No', 'no', 'number');
        $createCol($t3a2, 'Nama DTPR (Ketua)', 'nama');
        $createCol($t3a2, 'Judul Penelitian', 'judul');
        $createCol($t3a2, 'Jumlah Mahasiswa yang Terlibat', 'mhs_terlibat', 'number');
        $createCol($t3a2, 'Jenis Hibah Penelitian', 'jenis');
        $createCol($t3a2, 'Sumber L/N/I', 'sumber');
        $createCol($t3a2, 'Durasi (tahun)', 'durasi', 'number');
        $dana = $createCol($t3a2, 'Pendanaan (Rp juta)', 'dana_group');
        $createCol($t3a2, 'TS-2', 'ts2', 'currency', $dana->id);
        $createCol($t3a2, 'TS-1', 'ts1', 'currency', $dana->id);
        $createCol($t3a2, 'TS', 'ts', 'currency', $dana->id);
        $createCol($t3a2, 'Link Bukti', 'bukti');

        // Tabel 3.A.3 Pengembangan DTPR di Bidang Penelitian
        $t3a3 = $createTable('tabel_3_a_3_pengembangan_riset', 'Tabel 3.A.3 Pengembangan DTPR di Bidang Penelitian');
        $createCol($t3a3, 'Nama DTPR', 'nama');
        $createCol($t3a3, 'Jenis Pengembangan DTPR', 'jenis');
        $tahun = $createCol($t3a3, 'Tahun Akademik', 'tahun_group');
        $createCol($t3a3, 'TS-2', 'ts2', 'number', $tahun->id);
        $createCol($t3a3, 'TS-1', 'ts1', 'number', $tahun->id);
        $createCol($t3a3, 'TS', 'ts', 'number', $tahun->id);
        $createCol($t3a3, 'Jumlah', 'jumlah', 'number');
        $createCol($t3a3, 'Link Bukti', 'bukti');

        // Tabel 3.C.1 Kerjasama Penelitian
        $t3c1 = $createTable('tabel_3_c_1_kerjasama_riset', 'Tabel 3.C.1 Kerjasama Penelitian');
        $createCol($t3c1, 'No', 'no', 'number');
        $createCol($t3c1, 'Judul Kerjasama', 'judul');
        $createCol($t3c1, 'Mitra Kerja Sama', 'mitra');
        $createCol($t3c1, 'Sumber L/N/I', 'sumber');
        $createCol($t3c1, 'Durasi (Tahun)', 'durasi', 'number');
        $dana = $createCol($t3c1, 'Pendanaan (Rp Juta)', 'dana_group');
        $createCol($t3c1, 'TS-2', 'ts2', 'currency', $dana->id);
        $createCol($t3c1, 'TS-1', 'ts1', 'currency', $dana->id);
        $createCol($t3c1, 'TS', 'ts', 'currency', $dana->id);
        $createCol($t3c1, 'Link Bukti', 'bukti');

        // Tabel 3.C.2 Publikasi Penelitian
        $t3c2 = $createTable('tabel_3_c_2_publikasi_riset', 'Tabel 3.C.2 Publikasi Penelitian');
        $createCol($t3c2, 'No', 'no', 'number');
        $createCol($t3c2, 'Nama DTPR', 'nama');
        $createCol($t3c2, 'Judul Publikasi', 'judul');
        $createCol($t3c2, 'Jenis Publikasi (IB/I/S1,S2,S3,S4,T)', 'jenis');
        $tahun = $createCol($t3c2, 'Tahun Terbit (beri tanda √)', 'tahun_group');
        $createCol($t3c2, 'TS-2', 'ts2', 'text', $tahun->id);
        $createCol($t3c2, 'TS-1', 'ts1', 'text', $tahun->id);
        $createCol($t3c2, 'TS', 'ts', 'text', $tahun->id);
        $createCol($t3c2, 'Link Bukti', 'bukti');

        // Tabel 3.C.3 Perolehan HKI (Granted)
        $t3c3 = $createTable('tabel_3_c_3_hki_riset', 'Tabel 3.C.3 Perolehan HKI (Granted)');
        $createCol($t3c3, 'No', 'no', 'number');
        $createCol($t3c3, 'Judul', 'judul');
        $createCol($t3c3, 'Jenis HKI', 'jenis');
        $createCol($t3c3, 'Nama DTPR', 'nama');
        $tahun = $createCol($t3c3, 'Tahun Perolehan (Beri Tanda √)', 'tahun_group');
        $createCol($t3c3, 'TS-2', 'ts2', 'text', $tahun->id);
        $createCol($t3c3, 'TS-1', 'ts1', 'text', $tahun->id);
        $createCol($t3c3, 'TS', 'ts', 'text', $tahun->id);
        $createCol($t3c3, 'Link Bukti', 'bukti');

        // --- CRITERION 4: Relevansi PkM ---

        // Tabel 4.A.1 Sarana dan Prasarana PkM
        $t4a1 = $createTable('tabel_4_a_1_sarpras_pkm', 'Tabel 4.A.1 Sarana dan Prasarana PkM');
        $createCol($t4a1, 'Nama Prasarana', 'nama');
        $createCol($t4a1, 'Daya Tampung', 'dt', 'number');
        $createCol($t4a1, 'Luas Ruang (m2)', 'luas', 'number');
        $createCol($t4a1, 'Milik Sendiri (M)/ Sewa (W)', 'milik');
        $createCol($t4a1, 'Berlisensi (L)/ Public Domain (P)/Tidak Berlisensi (T)', 'lisensi');
        $createCol($t4a1, 'Perangkat .....', 'perangkat');
        $createCol($t4a1, 'Link Bukti', 'bukti');

        // Tabel 4.A.2 PkM DTPR, Hibah dan Pembiayaan PkM
        $t4a2 = $createTable('tabel_4_a_2_hibah_pkm', 'Tabel 4.A.2 PkM DTPR, Hibah dan Pembiayaan PkM');
        $createCol($t4a2, 'No', 'no', 'number');
        $createCol($t4a2, 'Nama DTPR (Sebagai Ketua PkM)', 'nama');
        $createCol($t4a2, 'Judul PkM', 'judul');
        $createCol($t4a2, 'Jumlah Mahasiswa yang Terlibat', 'mhs_terlibat', 'number');
        $createCol($t4a2, 'Jenis Hibah PkM', 'jenis');
        $createCol($t4a2, 'Sumber Dana L/N/I', 'sumber');
        $createCol($t4a2, 'Durasi (tahun)', 'durasi', 'number');
        $dana = $createCol($t4a2, 'Pendanaan (Rp Juta)', 'dana_group');
        $createCol($t4a2, 'TS-2', 'ts2', 'currency', $dana->id);
        $createCol($t4a2, 'TS-1', 'ts1', 'currency', $dana->id);
        $createCol($t4a2, 'TS', 'ts', 'currency', $dana->id);
        $createCol($t4a2, 'Link Bukti', 'bukti');

        // Tabel 4.C.1 Kerjasama PkM
        $t4c1 = $createTable('tabel_4_c_1_kerjasama_pkm', 'Tabel 4.C.1 Kerjasama PkM');
        $createCol($t4c1, 'No', 'no', 'number');
        $createCol($t4c1, 'Judul Kerjasama', 'judul');
        $createCol($t4c1, 'Mitra kerja sama', 'mitra');
        $createCol($t4c1, 'Sumber L/N/I', 'sumber');
        $createCol($t4c1, 'Durasi (tahun)', 'durasi', 'number');
        $dana = $createCol($t4c1, 'Pendanaan (Rp Juta)', 'dana_group');
        $createCol($t4c1, 'TS-2', 'ts2', 'currency', $dana->id);
        $createCol($t4c1, 'TS-1', 'ts1', 'currency', $dana->id);
        $createCol($t4c1, 'TS', 'ts', 'currency', $dana->id);
        $createCol($t4c1, 'Link Bukti', 'bukti');

        // Tabel 4.C.2 Diseminasi Hasil PkM
        $t4c2 = $createTable('tabel_4_c_2_diseminasi_pkm', 'Tabel 4.C.2 Diseminasi Hasil PkM');
        $createCol($t4c2, 'No', 'no', 'number');
        $createCol($t4c2, 'Nama DTPR (Ketua)', 'nama');
        $createCol($t4c2, 'Judul', 'judul');
        $createCol($t4c2, 'Diseminasi Hasil PkM (L/N/I)', 'diseminasi');
        $createCol($t4c2, 'TS-2', 'ts2', 'text');
        $createCol($t4c2, 'TS-1', 'ts1', 'text');
        $createCol($t4c2, 'TS', 'ts', 'text');
        $createCol($t4c2, 'Link Bukti', 'bukti');

        // Tabel 4.C.3 Perolehan HKI PkM
        $t4c3 = $createTable('tabel_4_c_3_hki_pkm', 'Tabel 4.C.3 Perolehan HKI PkM');
        $createCol($t4c3, 'No', 'no', 'number');
        $createCol($t4c3, 'Judul', 'judul');
        $createCol($t4c3, 'Jenis HKI', 'jenis');
        $createCol($t4c3, 'Nama DTPR', 'nama');
        $tahun = $createCol($t4c3, 'Tahun Perolehan (beri tanda √)', 'tahun_group');
        $createCol($t4c3, 'TS-2', 'ts2', 'text', $tahun->id);
        $createCol($t4c3, 'TS-1', 'ts1', 'text', $tahun->id);
        $createCol($t4c3, 'TS', 'ts', 'text', $tahun->id);
        $createCol($t4c3, 'Link Bukti', 'bukti');

        // --- CRITERION 5: Akuntabilitas ---

        // Tabel 5.1. Sistem Tata Kelola
        $t51 = $createTable('tabel_5_1_tata_kelola', 'Tabel 5.1. Sistem Tata Kelola');
        $createCol($t51, 'No', 'no', 'number');
        $createCol($t51, 'Jenis Tata Kelola', 'jenis');
        $createCol($t51, 'Nama Sistem Informasi', 'nama');
        $createCol($t51, 'Akses (Lokal/Internet)', 'akses');
        $createCol($t51, 'Unit Kerja/SDM Pengelola', 'unit');
        $createCol($t51, 'Link Bukti', 'bukti');

        // Tabel 5.2 Sarana dan Prasarana Pendidikan
        $t52 = $createTable('tabel_5_2_sarpras_pnd', 'Tabel 5.2 Sarana dan Prasarana Pendidikan');
        $createCol($t52, 'Nama Prasarana', 'nama');
        $createCol($t52, 'Daya Tampung', 'dt', 'number');
        $createCol($t52, 'Luas Ruang (m2)', 'luas', 'number');
        $createCol($t52, 'Milik sendiri (M)/Sewa (W)', 'milik');
        $createCol($t52, 'Berlisensi (L)/ Public Domain (P)/Tidak berlisensi (T)', 'lisensi');
        $createCol($t52, 'Perangkat …', 'perangkat');
        $createCol($t52, 'Link Bukti', 'bukti');

        // --- CRITERION 6: Diferensiasi Misi ---

        // Tabel 6. Kesesuaian Visi, Misi
        $t6 = $createTable('tabel_6_visi_misi', 'Tabel 6. Kesesuaian Visi, Misi');
        $createCol($t6, 'Visi PT', 'visi_pt');
        $createCol($t6, 'Visi UPPS', 'visi_upps');
        $createCol($t6, 'Visi Keilmuan PS', 'visi_ps');
        $createCol($t6, 'Misi PT', 'misi_pt');
        $createCol($t6, 'Misi UPPS', 'misi_upps');
        $createCol($t6, 'Misi PS', 'misi_ps');
    }
}
