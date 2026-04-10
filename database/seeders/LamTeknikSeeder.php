<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LamTable;
use App\Models\LamTableColumn;

class LamTeknikSeeder extends Seeder
{
    public function run(): void
    {
        $lamType = 'lam-teknik';

        // Clear existing for this LAM to avoid duplicates
        $existingTables = LamTable::where('lam_type', $lamType)->get();
        foreach ($existingTables as $et) {
            /** @var \App\Models\LamTable $et */
            $et->columns()->delete();
            $et->delete();
        }

        // Helper to create table
        $createTable = function ($slug, $label) use ($lamType) {
            return LamTable::create([
                'slug' => $slug,
                'lam_type' => $lamType,
                'label' => $label
            ]);
        };

        // Helper to create column
        $createCol = function ($table, $header, $field, $type = 'text', $parentId = null, $order = 0) {
            return LamTableColumn::create([
                'lam_table_id' => $table->id,
                'header_name' => $header,
                'field_name' => $field,
                'data_type' => $type,
                'parent_id' => $parentId,
                'sort_order' => $order
            ]);
        };

        // --- 1. Diferensiasi Misi ---
        $t1 = $createTable('tabel_1_vmts', 'Tabel 1. VMTS PT dan UPPS serta Visi Keilmuan PS');
        $createCol($t1, 'Unit', 'unit');
        $createCol($t1, 'Pernyataan', 'pernyataan');
        $createCol($t1, 'No. Surat Keputusan (SK)', 'sk');
        $createCol($t1, 'Link Dokumen', 'link');

        // --- 2. Akuntabilitas ---
        // 2a. Kerja sama
        $t2a = $createTable('tabel_2_a_kerjasama', 'Tabel 2.a. Kerja sama Tridharma Perguruan Tinggi');
        $createCol($t2a, 'No', 'no', 'number');
        $createCol($t2a, 'Lembaga Mitra', 'mitra');
        $tk2a = $createCol($t2a, 'Tingkat', 'tk_group');
        $createCol($t2a, 'Internasional', 'tk_inter', 'text', $tk2a->id);
        $createCol($t2a, 'Nasional', 'tk_nasional', 'text', $tk2a->id);
        $createCol($t2a, 'Lokal/Wilayah', 'tk_lokal', 'text', $tk2a->id);
        $createCol($t2a, 'Judul Kegiatan Kerjasama', 'judul');
        $createCol($t2a, 'Manfaat bagi PS', 'manfaat');
        $createCol($t2a, 'Tanggal Awal', 'tgl_awal');
        $createCol($t2a, 'Tanggal Akhir', 'tgl_akhir');
        $createCol($t2a, 'Bukti Kerjasama', 'bukti');

        // 2b. Keuangan
        $t2b = $createTable('tabel_2_b_dana', 'Tabel 2.b. Penggunaan Dana');
        $createCol($t2b, 'No', 'no', 'number');
        $createCol($t2b, 'Jenis Penggunaan', 'jenis');
        $upps2b = $createCol($t2b, 'Unit Pengelola (Rp.)', 'upps_group');
        $createCol($t2b, 'TS-2', 'upps_ts2', 'currency', $upps2b->id);
        $createCol($t2b, 'TS-1', 'upps_ts1', 'currency', $upps2b->id);
        $createCol($t2b, 'TS', 'upps_ts', 'currency', $upps2b->id);
        $createCol($t2b, 'Rata-rata', 'upps_rata', 'currency', $upps2b->id);
        $ps2b = $createCol($t2b, 'Program Studi (Rp.)', 'ps_group');
        $createCol($t2b, 'TS-2', 'ps_ts2', 'currency', $ps2b->id);
        $createCol($t2b, 'TS-1', 'ps_ts1', 'currency', $ps2b->id);
        $createCol($t2b, 'TS', 'ps_ts', 'currency', $ps2b->id);
        $createCol($t2b, 'Rata-rata', 'ps_rata', 'currency', $ps2b->id);

        // --- 3. Relevansi Pendidikan, Penelitian, dan PkM ---
        // 3a1. Kurikulum
        $t3a1 = $createTable('tabel_3_a_1_kurikulum', 'Tabel 3.a.1. Kurikulum dan Rencana Pembelajaran');
        $createCol($t3a1, 'No', 'no', 'number');
        $createCol($t3a1, 'Semester', 'smt', 'number');
        $createCol($t3a1, 'Kode MK', 'kode');
        $createCol($t3a1, 'Nama Mata Kuliah', 'nama');
        $createCol($t3a1, 'MK Kompetensi', 'kompetensi');
        $bobot = $createCol($t3a1, 'Bobot Kredit (sks)', 'sks_group');
        $createCol($t3a1, 'Kuliah/Responsi/Tutorial', 'sks_teori', 'number', $bobot->id);
        $createCol($t3a1, 'Seminar', 'sks_seminar', 'number', $bobot->id);
        $createCol($t3a1, 'Praktikum/Praktik Lapangan', 'sks_praktik', 'number', $bobot->id);
        $createCol($t3a1, 'Konversi Kredit ke Jam', 'jam_konversi', 'number');
        $createCol($t3a1, 'Dokumen RPS', 'rps');
        $createCol($t3a1, 'Unit Penyelenggara', 'unit');

        // 3a3. Integrasi
        $t3a3 = $createTable('tabel_3_a_3_integrasi', 'Tabel 3.a.3. Integrasi Kegiatan Penelitian / PkM dalam Pembelajaran');
        $createCol($t3a3, 'No', 'no', 'number');
        $createCol($t3a3, 'Nama Dosen', 'nama');
        $createCol($t3a3, 'Judul Penelitian/PkM', 'judul');
        $createCol($t3a3, 'Mata Kuliah', 'mk');
        $createCol($t3a3, 'Bentuk Integrasi', 'bentuk');
        $tahun3a3 = $createCol($t3a3, 'Tahun Penelitian/PkM', 'tahun_group');
        $createCol($t3a3, 'TS-2', 'ts2', 'text', $tahun3a3->id);
        $createCol($t3a3, 'TS-1', 'ts1', 'text', $tahun3a3->id);
        $createCol($t3a3, 'TS', 'ts', 'text', $tahun3a3->id);
        $createCol($t3a3, 'Kesesuaian Peta Jalan', 'peta_jalan');
        $createCol($t3a3, 'Bukti Sahih', 'bukti');
        $createCol($t3a3, 'Kesesuaian RPS', 'rps');

        // 3a4. Basic Science
        $t3a4 = $createTable('tabel_3_a_4_basic_science', 'Tabel 3.a.4. Mata Kuliah Basic Science dan Matematika dalam Proses Pembelajaran');
        $createCol($t3a4, 'No', 'no', 'number');
        $createCol($t3a4, 'Nama Mata Kuliah Basic Science dan Matematika', 'nama');
        $createCol($t3a4, 'Semester', 'smt', 'number');
        $createCol($t3a4, 'Jumlah SKS', 'sks', 'number');

        // 3a5. Capstone Design
        $t3a5 = $createTable('tabel_3_a_5_capstone', 'Tabel 3.a.5. Capstone Design / Capstone Project dalam Proses Pembelajaran');
        $createCol($t3a5, 'No', 'no', 'number');
        $createCol($t3a5, 'Nama Mata Kuliah Pendukung Capstone Design', 'nama_pendukung');
        $createCol($t3a5, 'Jumlah SKS Mata Kuliah Pendukung', 'sks_pendukung', 'number');
        $createCol($t3a5, 'Nama Mata Kuliah Capstone Design', 'nama_capstone');
        $createCol($t3a5, 'Jumlah SKS Mata Kuliah Capstone', 'sks_capstone', 'number');
        $createCol($t3a5, 'Semester', 'smt', 'number');
        $createCol($t3a5, 'Cakupan Bahasan', 'cakupan');

        // 3b. Penelitian DTPS
        $t3b = $createTable('tabel_3_b_penelitian', 'Tabel 3.b. Penelitian DTPS');
        $createCol($t3b, 'No', 'no', 'number');
        $createCol($t3b, 'Sumber Pembiayaan', 'sumber');
        $jml3b = $createCol($t3b, 'Jumlah Judul Penelitian', 'jml_group');
        $createCol($t3b, 'TS-2', 'ts2', 'number', $jml3b->id);
        $createCol($t3b, 'TS-1', 'ts1', 'number', $jml3b->id);
        $createCol($t3b, 'TS', 'ts', 'number', $jml3b->id);
        $createCol($t3b, 'Jumlah', 'total', 'number');

        // 3c. PkM DTPS
        $t3c = $createTable('tabel_3_c_pkm', 'Tabel 3.c. Pengabdian kepada Masyarakat (PkM) DTPS');
        $createCol($t3c, 'No', 'no', 'number');
        $createCol($t3c, 'Sumber Pembiayaan', 'sumber');
        $jml3c = $createCol($t3c, 'Jumlah Judul PkM', 'jml_group');
        $createCol($t3c, 'TS-2', 'ts2', 'number', $jml3c->id);
        $createCol($t3c, 'TS-1', 'ts1', 'number', $jml3c->id);
        $createCol($t3c, 'TS', 'ts', 'number', $jml3c->id);
        $createCol($t3c, 'Jumlah', 'total', 'number');

        // --- 4. Sumber Daya Manusia ---
        // 4a. Profil Dosen
        $t4a = $createTable('tabel_4_a_dosen', 'Tabel 4.a. Dosen Tetap Perguruan Tinggi');
        $createCol($t4a, 'No', 'no', 'number');
        $createCol($t4a, 'Nama Dosen', 'nama');
        $createCol($t4a, 'NIDN/NUPTK/NIDK', 'nidn');
        $pnd4a = $createCol($t4a, 'Nama Prodi tempat dosen menyelesaikan studi dan bidang ilmu', 'pnd_group');
        $createCol($t4a, 'Sarjana / Sarjana Terapan', 's1', 'text', $pnd4a->id);
        $createCol($t4a, 'Magister / Magister Terapan', 's2', 'text', $pnd4a->id);
        $createCol($t4a, 'Doktor / Doktor Terapan', 's3', 'text', $pnd4a->id);
        $createCol($t4a, 'Bidang Keahlian', 'bidang');
        $createCol($t4a, 'Perusahaan/Industri', 'perusahaan');
        $createCol($t4a, 'Kesesuaian dengan Kompetensi Inti PS', 'fit', 'boolean');
        $createCol($t4a, 'Jabatan Akademik', 'jabatan');
        $createCol($t4a, 'Nomor Sertifikat Pendidik Profesional', 'serdos');
        $sertkom4a = $createCol($t4a, 'Sertifikat Kompetensi/ Profesi/ Industri', 'sertkom_group');
        $createCol($t4a, 'Bidang Sertifikasi', 'sertkom_bidang', 'text', $sertkom4a->id);
        $createCol($t4a, 'Lembaga Penerbit', 'sertkom_lembaga', 'text', $sertkom4a->id);
        $insinyur4a = $createCol($t4a, 'Sertifikat Keinsinyuran', 'insinyur_group');
        $createCol($t4a, 'SKIP (Sertifikat Kompetensi Insinyur Profesional)', 'skip', 'text', $insinyur4a->id);
        $createCol($t4a, 'STRI (Surat Tanda Registrasi Insinyur)', 'stri', 'text', $insinyur4a->id);
        $createCol($t4a, 'Mata Kuliah yang Diampu pada PS yang Diakreditasi', 'mk_ps');
        $createCol($t4a, 'Kesesuaian Bidang Keahlian dengan Mata Kuliah yang Diampu', 'mk_fit', 'boolean');
        $createCol($t4a, 'Mata Kuliah yang Diampu pada PS Lain', 'mk_lain');

        // 4b. Tendik
        $t4b = $createTable('tabel_4_b_tendik', 'Tabel 4.b. Data Tenaga Kependidikan Laboran / Teknisi / Administrator');
        $createCol($t4b, 'No', 'no', 'number');
        $createCol($t4b, 'Nama Laboran/Teknisi/Admin', 'nama');
        $pnd4b = $createCol($t4b, 'Pendidikan Terakhir', 'pnd_group');
        $createCol($t4b, 'S3', 's3', 'text', $pnd4b->id);
        $createCol($t4b, 'S2', 's2', 'text', $pnd4b->id);
        $createCol($t4b, 'S1', 's1', 'text', $pnd4b->id);
        $createCol($t4b, 'D4', 'd4', 'text', $pnd4b->id);
        $createCol($t4b, 'D3', 'd3', 'text', $pnd4b->id);
        $createCol($t4b, 'D2', 'd2', 'text', $pnd4b->id);
        $createCol($t4b, 'D1', 'd1', 'text', $pnd4b->id);
        $createCol($t4b, 'SMA', 'sma', 'text', $pnd4b->id);
        $createCol($t4b, 'Sertifikat Kompetensi', 'sertkom');
        $createCol($t4b, 'Unit Kerja', 'unit');

        // 4c. BK DTPR
        $t4c = $createTable('tabel_4_c_bk', 'Tabel 4.c. BK Dosen Tetap Perguruan Tinggi');
        $createCol($t4c, 'No', 'no', 'number');
        $createCol($t4c, 'Nama Dosen (DT)', 'nama');
        $createCol($t4c, 'DTPS', 'dtps', 'boolean');
        $bk4c = $createCol($t4c, 'Beban Kerja (sks) pada TS', 'bk_group');
        $pnd4c = $createCol($t4c, 'Pendidikan: Pembelajaran dan Pembimbingan', 'pnd_group', 'text', $bk4c->id);
        $createCol($t4c, 'PS yang Diakreditasi', 'ps_akre', 'number', $pnd4c->id);
        $createCol($t4c, 'PS Lain di dalam PT', 'ps_lain_pt', 'number', $pnd4c->id);
        $createCol($t4c, 'PS Lain di luar PT', 'ps_luar_pt', 'number', $pnd4c->id);
        $createCol($t4c, 'Penelitian', 'pen', 'number', $bk4c->id);
        $createCol($t4c, 'PkM', 'pkm', 'number', $bk4c->id);
        $createCol($t4c, 'Tugas Tambahan/Penunjang', 'tugas', 'number', $bk4c->id);
        $createCol($t4c, 'Jumlah per Tahun', 'total_thn', 'number');
        $createCol($t4c, 'Jumlah per Semester', 'total_sem', 'number');

        // 4d. Publikasi DTPS
        $t4d = $createTable('tabel_4_d_publikasi', 'Tabel 4.d. Publikasi Ilmiah DTPS');
        $createCol($t4d, 'No', 'no', 'number');
        $createCol($t4d, 'Jenis Publikasi', 'jenis');
        $jml4d = $createCol($t4d, 'Jumlah Judul', 'jml_group');
        $createCol($t4d, 'TS-2', 'ts2', 'number', $jml4d->id);
        $createCol($t4d, 'TS-1', 'ts1', 'number', $jml4d->id);
        $createCol($t4d, 'TS', 'ts', 'number', $jml4d->id);
        $createCol($t4d, 'Jumlah', 'total', 'text');

        // 4e. Publikasi / Pagelaran
        $t4e = $createTable('tabel_4_e_publikasi', 'Tabel 4.e. Pagelaran / pameran / presentasi / publikasi Ilmiah DTPS');
        $createCol($t4e, 'No', 'no', 'number');
        $createCol($t4e, 'Jenis Publikasi', 'jenis');
        $jml4e = $createCol($t4e, 'Jumlah Judul', 'jml_group');
        $createCol($t4e, 'TS-2', 'ts2', 'number', $jml4e->id);
        $createCol($t4e, 'TS-1', 'ts1', 'number', $jml4e->id);
        $createCol($t4e, 'TS', 'ts', 'number', $jml4e->id);
        $createCol($t4e, 'Jumlah', 'total', 'number');

        // 4h. Karya Ilmiah DTPS
        $t4h = $createTable('tabel_4_h_karya_ilmiah', 'Tabel 4.h. Karya ilmiah DTPS di jurnal internasional bereputasi atau publikasi dalam prosiding internasional');
        $createCol($t4h, 'No', 'no', 'number');
        $createCol($t4h, 'Nama DTPS', 'nama');
        $jml4h = $createCol($t4h, 'Jurnal internasional bereputasi / prosiding internasional terindeks', 'jml_group');
        $createCol($t4h, 'TS-2', 'ts2', 'number', $jml4h->id);
        $createCol($t4h, 'TS-1', 'ts1', 'number', $jml4h->id);
        $createCol($t4h, 'TS', 'ts', 'number', $jml4h->id);
        $createCol($t4h, 'Keterangan', 'ket');

        // 4i. Sitasi Karya Ilmiah
        $t4i = $createTable('tabel_4_i_sitasi', 'Tabel 4.i. Karya ilmiah DTPS yang disitasi dalam 3 tahun terakhir');
        $createCol($t4i, 'No', 'no', 'number');
        $createCol($t4i, 'Nama DTPS', 'nama');
        $createCol($t4i, 'Judul Artikel yang Disitasi', 'judul');
        $createCol($t4i, 'Jumlah Sitasi di Google Scholar', 'jml', 'number');

        // 4j. Rekognisi DTPS
        $t4j = $createTable('tabel_4_j_rekognisi', 'Tabel 4.j. Pengakuan / Rekognisi DTPS');
        $createCol($t4j, 'No', 'no', 'number');
        $createCol($t4j, 'Nama DTPS', 'nama');
        $createCol($t4j, 'Bidang Keahlian', 'bidang');
        $rek4j = $createCol($t4j, 'Rekognisi dan Bukti Pendukung', 'rek_group');
        $createCol($t4j, 'Rekognisi', 'rekognisi', 'text', $rek4j->id);
        $createCol($t4j, 'Bukti Pendukung', 'bukti', 'text', $rek4j->id);
        $tk4j = $createCol($t4j, 'Tingkat', 'tk_group');
        $createCol($t4j, 'Wilayah', 'wilayah', 'boolean', $tk4j->id);
        $createCol($t4j, 'Nasional', 'nasional', 'boolean', $tk4j->id);
        $createCol($t4j, 'Internasional', 'internasional', 'boolean', $tk4j->id);
        $createCol($t4j, 'Tahun (YYYY)', 'tahun', 'number');

        // 4k. Pembimbing Lapangan
        $t4k = $createTable('tabel_4_k_pembimbing', 'Tabel 4.k. Pembimbing lapangan');
        $createCol($t4k, 'No', 'no', 'number');
        $createCol($t4k, 'Nama', 'nama');
        $createCol($t4k, 'Industri', 'industri');
        $createCol($t4k, 'Bidang Keinsinyuran', 'bidang');
        $createCol($t4k, 'Pengalaman Kerja (Tahun)', 'pengalaman', 'number');
        $createCol($t4k, 'Pendidikan Tinggi', 'pnd');
        $createCol($t4k, 'SKIP', 'skip');
        $createCol($t4k, 'Jumlah Bimbingan (3 Thn)', 'jml', 'number');

        // Luaran Tables (Generic setup)
        $t4f = $createTable('tabel_4_f_luaran', 'Tabel 4.f. Luaran Penelitian dan PkM DTPS');
        $createCol($t4f, 'No', 'no', 'number');
        $createCol($t4f, 'Judul Luaran', 'judul');
        $createCol($t4f, 'Tanggal', 'tanggal');
        $createCol($t4f, 'No Paten (Granted)', 'paten');
        $createCol($t4f, 'Keterangan (Sertifikat)', 'ket');
        $createCol($t4f, 'Status (TKT)', 'tkt');
        $createCol($t4f, 'No Sertifikat TKT', 'tkt_no');
        $createCol($t4f, 'Keterangan ISBN', 'isbn');

        // --- 5. Sarana, Prasarana, dan K3L ---
        $t5a = $createTable('tabel_5_a_sarpras', 'Tabel 5.a. Prasarana dan Peralatan Utama yang Digunakan');
        $createCol($t5a, 'No', 'no', 'number');
        $createCol($t5a, 'Nama Prasarana', 'nama');
        $createCol($t5a, 'Jumlah Prasarana', 'jml_pras', 'number');
        $createCol($t5a, 'Nama Sarana/Alat Peraga', 'sarana');
        $jml5a = $createCol($t5a, 'Jumlah Alat', 'jml_alat');
        $createCol($t5a, 'Standar Minimal', 'std', 'text', $jml5a->id);
        $createCol($t5a, 'Yg dimiliki UPPS', 'own', 'text', $jml5a->id);
        $kp5a = $createCol($t5a, 'Kepemilikan', 'own_group');
        $createCol($t5a, 'Sendiri', 'self', 'text', $kp5a->id);
        $createCol($t5a, 'Sewa', 'rent', 'text', $kp5a->id);
        $cond5a = $createCol($t5a, 'Kondisi', 'cond_group');
        $createCol($t5a, 'Terawat', 'good', 'text', $cond5a->id);
        $createCol($t5a, 'Tidak Terawat', 'bad', 'text', $cond5a->id);
        $log5a = $createCol($t5a, 'Logbook', 'log_group');
        $createCol($t5a, 'Ada', 'yes', 'text', $log5a->id);
        $createCol($t5a, 'Tidak Ada', 'no', 'text', $log5a->id);
        $createCol($t5a, 'Rata-rata Penggunaan', 'usage', 'number');

        // 5b. K3L
        $t5b = $createTable('tabel_5_b_k3l', 'Tabel 5.b. Dokumen K3L di UPPS');
        $createCol($t5b, 'No', 'no', 'number');
        $createCol($t5b, 'Jenis Dokumen', 'jenis');
        $createCol($t5b, 'Jumlah', 'jml', 'number');
        $createCol($t5b, 'Riwayat Pengesahan', 'riwayat');

        // 5c. Fasilitas K3L
        $t5c = $createTable('tabel_5_c_fasilitas_k3l', 'Tabel 5.c. Fasilitas K3L di UPPS');
        $createCol($t5c, 'No', 'no', 'number');
        $createCol($t5c, 'Nama Sarana', 'nama');
        $createCol($t5c, 'Fungsi', 'fungsi');
        $createCol($t5c, 'Jumlah Unit', 'unit', 'number');
        $cond5c = $createCol($t5c, 'Kondisi*', 'cond_group');
        $createCol($t5c, 'Terawat', 'good', 'boolean', $cond5c->id);
        $createCol($t5c, 'Tidak Terawat', 'bad', 'boolean', $cond5c->id);

        // --- 6. Mahasiswa dan Luaran Mahasiswa ---
        $t6a = $createTable('tabel_6_a_mhs', 'Tabel 6.a. Jumlah Mahasiswa (Reguler dan Asing)');
        $createCol($t6a, 'No', 'no', 'number');
        $createCol($t6a, 'Program Studi', 'prodi');
        $akt6a = $createCol($t6a, 'Jumlah Mahasiswa Aktif', 'akt_group');
        $createCol($t6a, 'TS-2', 'akt_ts2', 'number', $akt6a->id);
        $createCol($t6a, 'TS-1', 'akt_ts1', 'number', $akt6a->id);
        $createCol($t6a, 'TS', 'akt_ts', 'number', $akt6a->id);
        $full6a = $createCol($t6a, 'Jumlah Mhs Asing Full-time', 'full_group');
        $createCol($t6a, 'TS-2', 'full_ts2', 'number', $full6a->id);
        $createCol($t6a, 'TS-1', 'full_ts1', 'number', $full6a->id);
        $createCol($t6a, 'TS', 'full_ts', 'number', $full6a->id);
        $part6a = $createCol($t6a, 'Jumlah Mhs Asing Part-time', 'part_group');
        $createCol($t6a, 'TS-2', 'part_ts2', 'number', $part6a->id);
        $createCol($t6a, 'TS-1', 'part_ts1', 'number', $part6a->id);
        $createCol($t6a, 'TS', 'part_ts', 'number', $part6a->id);

        $t6b = $createTable('tabel_6_b_ipk', 'Tabel 6.b. IPK Lulusan');
        $createCol($t6b, 'No', 'no', 'number');
        $createCol($t6b, 'Tahun Lulus', 'tahun');
        $createCol($t6b, 'Jumlah Lulusan', 'jml');
        $ipk6b = $createCol($t6b, 'Indeks Prestasi Kumulatif (IPK)', 'ipk_group');
        $createCol($t6b, 'Min.', 'min', 'number', $ipk6b->id);
        $createCol($t6b, 'Rata-rata', 'avg', 'number', $ipk6b->id);
        $createCol($t6b, 'Maks.', 'max', 'number', $ipk6b->id);

        // 6c1. Prestasi Akademik
        $t6c1 = $createTable('tabel_6_c_1_akademik', 'Tabel 6.c.1. Prestasi Akademik Mahasiswa');
        $createCol($t6c1, 'No', 'no', 'number');
        $createCol($t6c1, 'Nama Kegiatan', 'nama');
        $createCol($t6c1, 'Waktu Perolehan (HH/BB/TTTT)', 'waktu');
        $tk6c = $createCol($t6c1, 'Tingkat', 'tk_group');
        $createCol($t6c1, 'Lokal / Wilayah', 'lokal', 'boolean', $tk6c->id);
        $createCol($t6c1, 'Nasional', 'nasional', 'boolean', $tk6c->id);
        $createCol($t6c1, 'Internasional', 'internasional', 'boolean', $tk6c->id);
        $createCol($t6c1, 'Prestasi yang Dicapai', 'prestasi');

        // 6c2. Prestasi Non-Akademik
        $t6c2 = $createTable('tabel_6_c_2_nonakademik', 'Tabel 6.c.2. Prestasi Nonakademik Mahasiswa');
        $createCol($t6c2, 'No', 'no', 'number');
        $createCol($t6c2, 'Nama Kegiatan', 'nama');
        $createCol($t6c2, 'Waktu Perolehan (HH/BB/TTTT)', 'waktu');
        $tk6c2 = $createCol($t6c2, 'Tingkat', 'tk_group');
        $createCol($t6c2, 'Lokal / Wilayah', 'lokal', 'boolean', $tk6c2->id);
        $createCol($t6c2, 'Nasional', 'nasional', 'boolean', $tk6c2->id);
        $createCol($t6c2, 'Internasional', 'internasional', 'boolean', $tk6c2->id);
        $createCol($t6c2, 'Prestasi yang Dicapai', 'prestasi');

        // 6d3. Masa Studi Doktor
        $t6d3s = $createTable('tabel_6_d_masa_studi_s3', 'Tabel 6.d. Masa studi Lulusan Program Doktor / Doktor Terapan Jalur Reguler');
        $createCol($t6d3s, 'No', 'no', 'number');
        $createCol($t6d3s, 'Tahun Masuk', 'tahun');
        $createCol($t6d3s, 'Jumlah Mahasiswa Masuk', 'jml_msk', 'number');
        $lg6d3s = $createCol($t6d3s, 'Jumlah Mahasiswa Lulus', 'lulus_group');
        $createCol($t6d3s, '2,5 < MS <= 3,5', 'ms35', 'number', $lg6d3s->id);
        $createCol($t6d3s, '3,5 < MS <= 4,5', 'ms45', 'number', $lg6d3s->id);
        $createCol($t6d3s, '4,5 < MS <= 6', 'ms60', 'number', $lg6d3s->id);

        // 6d2. Masa Studi Magister
        $t6d2 = $createTable('tabel_6_d_masa_studi_s2', 'Tabel 6.d. Masa studi Lulusan Program Magister / Magister Terapan Jalur Reguler');
        $createCol($t6d2, 'No', 'no', 'number');
        $createCol($t6d2, 'Tahun Masuk', 'tahun');
        $createCol($t6d2, 'Jumlah Mahasiswa Masuk', 'jml_msk', 'number');
        $lg6d2 = $createCol($t6d2, 'Jumlah Mahasiswa Lulus', 'lulus_group');
        $createCol($t6d2, '1,5 < MS <= 2,5', 'ms25', 'number', $lg6d2->id);
        $createCol($t6d2, '2,5 < MS <= 3,5', 'ms35', 'number', $lg6d2->id);
        $createCol($t6d2, '3,5 < MS <= 4', 'ms40', 'number', $lg6d2->id);

        // 6d. Masa Studi Sarjana
        $t6d_s1 = $createTable('tabel_6_d_masa_studi_s1', 'Tabel 6.d. Masa studi Lulusan Program Sarjana / Sarjana Terapan Jalur Reguler');
        $createCol($t6d_s1, 'No', 'no', 'number');
        $createCol($t6d_s1, 'Tahun Masuk', 'tahun');
        $createCol($t6d_s1, 'Jumlah Mahasiswa Masuk', 'jml_msk', 'number');
        $lg_s1 = $createCol($t6d_s1, 'Jumlah Mahasiswa Lulus', 'lulus_group');
        $createCol($t6d_s1, '3,5 < MS <= 4,5', 'ms45', 'number', $lg_s1->id);
        $createCol($t6d_s1, '4,5 < MS <= 5,5', 'ms55', 'number', $lg_s1->id);
        $createCol($t6d_s1, '5,5 < MS <= 6,5', 'ms65', 'number', $lg_s1->id);
        $createCol($t6d_s1, '6,5 < MS <= 8', 'ms80', 'number', $lg_s1->id);

        // 6d. Masa Studi Diploma Tiga
        $t6d_d3 = $createTable('tabel_6_d_masa_studi_d3', 'Tabel 6.d. Masa studi Lulusan Program Diploma Tiga Jalur Reguler');
        $createCol($t6d_d3, 'No', 'no', 'number');
        $createCol($t6d_d3, 'Tahun Masuk', 'tahun');
        $createCol($t6d_d3, 'Jumlah Mahasiswa Masuk', 'jml_msk', 'number');
        $lg_d3 = $createCol($t6d_d3, 'Jumlah Mahasiswa Lulus', 'lulus_group');
        $createCol($t6d_d3, '2.0 < MS <= 2,5', 'ms25', 'number', $lg_d3->id);
        $createCol($t6d_d3, '2,5 < MS <= 4.0', 'ms40', 'number', $lg_d3->id);
        $createCol($t6d_d3, 'MS <= 1,5', 'ms15', 'number', $lg_d3->id);

        // 6d. Masa Studi Diploma Dua
        $t6d_d2 = $createTable('tabel_6_d_masa_studi_d2', 'Tabel 6.d. Masa studi Lulusan Program Diploma Dua Jalur Reguler');
        $createCol($t6d_d2, 'No', 'no', 'number');
        $createCol($t6d_d2, 'Tahun Masuk', 'tahun');
        $createCol($t6d_d2, 'Jumlah Mahasiswa Masuk', 'jml_msk', 'number');
        $lg_d2 = $createCol($t6d_d2, 'Jumlah Mahasiswa Lulus', 'lulus_group');
        $createCol($t6d_d2, '2 < MS <= 2,5', 'ms25', 'number', $lg_d2->id);
        $createCol($t6d_d2, '2,5 < MS <= 4', 'ms40', 'number', $lg_d2->id);
        $createCol($t6d_d2, 'MS <= 1,5', 'ms15', 'number', $lg_d2->id);

        // 6d. Masa Studi Diploma Satu
        $t6d_d1 = $createTable('tabel_6_d_masa_studi_d1', 'Tabel 6.d. Masa studi Lulusan Program Diploma Satu Jalur Reguler');
        $createCol($t6d_d1, 'No', 'no', 'number');
        $createCol($t6d_d1, 'Tahun Masuk', 'tahun');
        $createCol($t6d_d1, 'Jumlah Mahasiswa Masuk', 'jml_msk', 'number');
        $lg_d1 = $createCol($t6d_d1, 'Jumlah Mahasiswa Lulus', 'lulus_group');
        $createCol($t6d_d1, '1 < MS <= 1,5', 'ms15', 'number', $lg_d1->id);
        $createCol($t6d_d1, '1,5 < MS <= 2', 'ms20', 'number', $lg_d1->id);
        $createCol($t6d_d1, 'MS <= 0,5', 'ms05', 'number', $lg_d1->id);

        // 6e1. Publikasi Ilmiah Mahasiswa
        $t6e1 = $createTable('tabel_6_e_1_publikasi', 'Tabel 6.e.1. Publikasi Ilmiah mahasiswa');
        $createCol($t6e1, 'No', 'no', 'number');
        $createCol($t6e1, 'Jenis Publikasi', 'jenis');
        $group6e1 = $createCol($t6e1, 'Jumlah Judul', 'judul_group');
        $createCol($t6e1, 'TS-2', 'ts2', 'number', $group6e1->id);
        $createCol($t6e1, 'TS-1', 'ts1', 'number', $group6e1->id);
        $createCol($t6e1, 'TS', 'ts', 'number', $group6e1->id);
        $createCol($t6e1, 'Jumlah', 'total', 'number');

        // 6e2. Pagelaran/Pameran/Presentasi Mahasiswa
        $t6e2 = $createTable('tabel_6_e_2_pagelaran', 'Tabel 6.e.2. Pagelaran/pameran/presentasi/publikasi Ilmiah mahasiswa');
        $createCol($t6e2, 'No', 'no', 'number');
        $createCol($t6e2, 'Jenis Publikasi', 'jenis');
        $group6e2 = $createCol($t6e2, 'Jumlah Judul', 'judul_group');
        $createCol($t6e2, 'TS-2', 'ts2', 'number', $group6e2->id);
        $createCol($t6e2, 'TS-1', 'ts1', 'number', $group6e2->id);
        $createCol($t6e2, 'TS', 'ts', 'number', $group6e2->id);
        $createCol($t6e2, 'Jumlah', 'total', 'number');

        // 6e3. Luaran Penelitian/PkM Mahasiswa
        $t6e3 = $createTable('tabel_6_e_3_luaran', 'Tabel 6.e.3. Luaran penelitian/PkM yang dihasilkan mahasiswa');
        $createCol($t6e3, 'No', 'no', 'number');
        $createCol($t6e3, 'Judul Luaran Penelitian dan PkM', 'judul');
        $createCol($t6e3, 'Tanggal (HH/BB/TTTT)', 'tgl');
        $createCol($t6e3, 'Status (Registered/Granted/...)', 'status');
        $createCol($t6e3, 'Nomor Registrasi/ Paten', 'no_reg');
        $createCol($t6e3, 'Nomor HKI', 'no_hki');
        $createCol($t6e3, 'Status (TKT)', 'tkt');
        $createCol($t6e3, 'Nomor Sertifikat TKT', 'tkt_no');
        $createCol($t6e3, 'Nomor ISBN', 'isbn');

        // 6e4. Produk/Jasa Mahasiswa Diadopsi Industri
        $t6e4 = $createTable('tabel_6_e_4_adopsi', 'Tabel 6.e.4. Produk/jasa yang dihasilkan mahasiswa yang diadopsi oleh industri/masyarakat');
        $createCol($t6e4, 'No', 'no', 'number');
        $createCol($t6e4, 'Nama Mahasiswa', 'mahasiswa');
        $createCol($t6e4, 'Nama Produk/Jasa', 'produk');
        $createCol($t6e4, 'Deskripsi Produk/Jasa', 'deskripsi');
        $createCol($t6e4, 'Bukti', 'bukti');

        // 6f1. Waktu Tunggu (D1)
        $t6f1_d1 = $createTable('tabel_6_f_1_tunggu_d1', 'Tabel 6.f.1. Waktu Tunggu Lulusan (D1)');
        $createCol($t6f1_d1, 'Tahun Lulus', 'tahun');
        $createCol($t6f1_d1, 'Jumlah Lulusan', 'jml', 'number');
        $createCol($t6f1_d1, 'Jumlah Lulusan yang Terlacak', 'terlacak', 'number');
        $createCol($t6f1_d1, 'Jumlah Lulusan Dipesan sblm Lulus', 'pesan', 'number');
        $wt6f1_d1 = $createCol($t6f1_d1, 'Jumlah Lulusan Terlacak dgn WT', 'wt_group');
        $createCol($t6f1_d1, 'WT < 3 Bulan', 'wt3', 'number', $wt6f1_d1->id);
        $createCol($t6f1_d1, '3 <= WT <= 6 Bulan', 'wt3_6', 'number', $wt6f1_d1->id);
        $createCol($t6f1_d1, 'WT > 6 Bulan', 'wt6', 'number', $wt6f1_d1->id);

        // 6f1. Waktu Tunggu (D2)
        $t6f1_d2 = $createTable('tabel_6_f_1_tunggu_d2', 'Tabel 6.f.1. Waktu Tunggu Lulusan (D2)');
        $createCol($t6f1_d2, 'Tahun Lulus', 'tahun');
        $createCol($t6f1_d2, 'Jumlah Lulusan', 'jml', 'number');
        $createCol($t6f1_d2, 'Jumlah Lulusan yang Terlacak', 'terlacak', 'number');
        $createCol($t6f1_d2, 'Jumlah Lulusan Dipesan sblm Lulus', 'pesan', 'number');
        $wt6f1_d2 = $createCol($t6f1_d2, 'Jumlah Lulusan Terlacak dgn WT', 'wt_group');
        $createCol($t6f1_d2, 'WT < 3 Bulan', 'wt3', 'number', $wt6f1_d2->id);
        $createCol($t6f1_d2, '3 <= WT <= 6 Bulan', 'wt3_6', 'number', $wt6f1_d2->id);
        $createCol($t6f1_d2, 'WT > 6 Bulan', 'wt6', 'number', $wt6f1_d2->id);

        // 6f1. Waktu Tunggu (D3)
        $t6f1_d3 = $createTable('tabel_6_f_1_tunggu_d3', 'Tabel 6.f.1. Waktu Tunggu Lulusan (D3)');
        $createCol($t6f1_d3, 'Tahun Lulus', 'tahun');
        $createCol($t6f1_d3, 'Jumlah Lulusan', 'jml', 'number');
        $createCol($t6f1_d3, 'Jumlah Lulusan yang Terlacak', 'terlacak', 'number');
        $createCol($t6f1_d3, 'Jumlah Lulusan Dipesan sblm Lulus', 'pesan', 'number');
        $wt6f1_d3 = $createCol($t6f1_d3, 'Jumlah Lulusan Terlacak dgn WT', 'wt_group');
        $createCol($t6f1_d3, 'WT < 3 Bulan', 'wt3', 'number', $wt6f1_d3->id);
        $createCol($t6f1_d3, '3 <= WT <= 6 Bulan', 'wt3_6', 'number', $wt6f1_d3->id);
        $createCol($t6f1_d3, 'WT > 6 Bulan', 'wt6', 'number', $wt6f1_d3->id);

        // 6f1. Waktu Tunggu (Sarjana)
        $t6f1_s1 = $createTable('tabel_6_f_1_tunggu_s1', 'Tabel 6.f.1. Waktu Tunggu Lulusan (S1)');
        $createCol($t6f1_s1, 'Tahun Lulus', 'tahun');
        $createCol($t6f1_s1, 'Jumlah Lulusan', 'jml', 'number');
        $createCol($t6f1_s1, 'Jumlah Lulusan yang Terlacak', 'terlacak', 'number');
        $wt6f1_s1 = $createCol($t6f1_s1, 'Jumlah Lulusan Terlacak dgn WT', 'wt_group');
        $createCol($t6f1_s1, 'WT < 3 Bulan', 'wt3', 'number', $wt6f1_s1->id);
        $createCol($t6f1_s1, '3 <= WT <= 18 Bulan', 'wt3_18', 'number', $wt6f1_s1->id);
        $createCol($t6f1_s1, 'WT > 18 Bulan', 'wt18', 'number', $wt6f1_s1->id);

        // 6f1. Waktu Tunggu (Sarjana Terapan)
        $t6f1_s1_terapan = $createTable('tabel_6_f_1_tunggu_s1_terapan', 'Tabel 6.f.1. Waktu Tunggu Lulusan (S1 Terapan)');
        $createCol($t6f1_s1_terapan, 'Tahun Lulus', 'tahun');
        $createCol($t6f1_s1_terapan, 'Jumlah Lulusan', 'jml', 'number');
        $createCol($t6f1_s1_terapan, 'Jumlah Lulusan yang Terlacak', 'terlacak', 'number');
        $wt6f1_s1_terapan = $createCol($t6f1_s1_terapan, 'Jumlah Lulusan Terlacak dgn WT', 'wt_group');
        $createCol($t6f1_s1_terapan, 'WT < 3 Bulan', 'wt3', 'number', $wt6f1_s1_terapan->id);
        $createCol($t6f1_s1, '3 <= WT <= 18 Bulan', 'wt3_18', 'number', $wt6f1_s1->id);
        $createCol($t6f1_s1, 'WT > 18 Bulan', 'wt18', 'number', $wt6f1_s1->id);

        // 6f1. Waktu Tunggu (Profesi)
        $t6f1_pr = $createTable('tabel_6_f_1_tunggu_profesi', 'Tabel 6.f.1. Waktu Tunggu Lulusan (Profesi)');
        $createCol($t6f1_pr, 'Tahun Lulus', 'tahun');
        $createCol($t6f1_pr, 'Jumlah Lulusan', 'jml', 'number');
        $createCol($t6f1_pr, 'Jumlah Lulusan yang Terlacak', 'terlacak', 'number');
        $createCol($t6f1_pr, 'Jumlah Lulusan Dipesan sblm Lulus', 'pesan', 'number');
        $wt6f1_pr = $createCol($t6f1_pr, 'Jumlah Lulusan Terlacak dgn WT', 'wt_group');
        $createCol($t6f1_pr, 'WT <= 3 Bulan', 'wt3', 'number', $wt6f1_pr->id);
        $createCol($t6f1_pr, '3 < WT <= 6 Bulan', 'wt3_6', 'number', $wt6f1_pr->id);
        $createCol($t6f1_pr, 'WT > 6 Bulan', 'wt6', 'number', $wt6f1_pr->id);


        // 6f2. Kesesuaian Bidang Kerja
        $t6f2 = $createTable('tabel_6_f_2_sesuai', 'Tabel 6.f.2. Kesesuaian Bidang Kerja Lulusan');
        $createCol($t6f2, 'Tahun Lulus', 'tahun');
        $createCol($t6f2, 'Jumlah Lulusan', 'jml', 'number');
        $createCol($t6f2, 'Jumlah Lulusan yang Terlacak', 'terlacak', 'number');
        $ks6f2 = $createCol($t6f2, 'Jumlah Lulusan Terlacak dgn Kesesuaian', 'ks_group');
        $createCol($t6f2, 'Rendah', 'low', 'number', $ks6f2->id);
        $createCol($t6f2, 'Sedang', 'mid', 'number', $ks6f2->id);
        $createCol($t6f2, 'Tinggi', 'high', 'number', $ks6f2->id);

        // 6g1. Tempat Kerja Lulusan
        $t6g1 = $createTable('tabel_6_g_1_wilayah', 'Tabel 6.g.1. Tempat Kerja Lulusan');
        $createCol($t6g1, 'Tahun Lulus', 'tahun');
        $createCol($t6g1, 'Jumlah Lulusan', 'jml', 'number');
        $createCol($t6g1, 'Jumlah Pengguna Memberi Tanggapan', 'tanggapan', 'number');
        $createCol($t6g1, 'Jumlah Lulusan yang Terlacak', 'terlacak', 'number');
        $tk6g1 = $createCol($t6g1, 'Jumlah Lulusan Terlacak Berdasarkan Tingkat', 'tk_group');
        $createCol($t6g1, 'Lokal/ Wilayah', 'lokal', 'number', $tk6g1->id);
        $createCol($t6g1, 'Nasional', 'nasional', 'number', $tk6g1->id);
        $createCol($t6g1, 'Multinasional/ Internasional', 'internasional', 'number', $tk6g1->id);

        // 6g2. Kepuasan Pengguna
        $t6g2 = $createTable('tabel_6_g_2_kepuasan', 'Tabel 6.g.2. Kepuasan Pengguna');
        $createCol($t6g2, 'No', 'no', 'number');
        $createCol($t6g2, 'Jenis Kemampuan', 'jenis');
        $tk6g2 = $createCol($t6g2, 'Tingkat Kepuasan Pengguna (%)', 'tk_group');
        $createCol($t6g2, 'Sangat Baik', 'sangat_baik', 'number', $tk6g2->id);
        $createCol($t6g2, 'Baik', 'baik', 'number', $tk6g2->id);
        $createCol($t6g2, 'Cukup', 'cukup', 'number', $tk6g2->id);
        $createCol($t6g2, 'Kurang', 'kurang', 'number', $tk6g2->id);
        $createCol($t6g2, 'Rencana Tindak Lanjut oleh UPPS/PS', 'rtl');

        // 6h1. Penelitian DTPS melibatkan Mahasiswa
        $t6h1 = $createTable('tabel_6_h_1_penelitian_mhs', 'Tabel 6.h.1. Penelitian DTPS yang melibatkan mahasiswa');
        $createCol($t6h1, 'No', 'no', 'number');
        $createCol($t6h1, 'Nama Dosen', 'dosen');
        $createCol($t6h1, 'Tema Penelitian sesuai Peta Jalan', 'tema');
        $createCol($t6h1, 'Nama Mahasiswa', 'mahasiswa');
        $createCol($t6h1, 'Judul Kegiatan', 'judul');
        $createCol($t6h1, 'Tahun', 'tahun');

        // 6h2. Penelitian DTPS Rujukan Tesis/Disertasi
        $t6h2 = $createTable('tabel_6_h_2_rujukan_tesis', 'Tabel 6.h.2. Penelitian DTPS yang menjadi rujukan tema Tesis/Disertasi');
        $createCol($t6h2, 'No', 'no', 'number');
        $createCol($t6h2, 'Nama Dosen', 'dosen');
        $createCol($t6h2, 'Tema Penelitian sesuai Peta Jalan', 'tema');
        $createCol($t6h2, 'Nama Mahasiswa', 'mahasiswa');
        $createCol($t6h2, 'Judul Tesis/ Disertasi', 'judul');
        $createCol($t6h2, 'Tahun', 'tahun');

        // 6i. PkM DTPS melibatkan Mahasiswa
        $t6i = $createTable('tabel_6_i_pkm_mhs', 'Tabel 6.i. PkM DTPS yang melibatkan mahasiswa');
        $createCol($t6i, 'No', 'no', 'number');
        $createCol($t6i, 'Nama Dosen', 'dosen');
        $createCol($t6i, 'Tema PkM sesuai Peta Jalan', 'tema');
        $createCol($t6i, 'Nama Mahasiswa', 'mahasiswa');
        $createCol($t6i, 'Judul Kegiatan PkM', 'judul');
        $createCol($t6i, 'Tahun (YYYY)', 'tahun');


        // --- 7. Penjaminan Mutu ---
        $t7a = $createTable('tabel_7_a_spmi', 'Tabel 7.a. Ketersediaan Dokumen / Buku SPMI');
        $createCol($t7a, 'No', 'no', 'number');
        $createCol($t7a, 'Jenis Dokumen', 'jenis');
        $createCol($t7a, 'Nomor dan Tanggal Dokumen', 'no_tgl');

        $t7b = $createTable('tabel_7_b_spmi_docs', 'Tabel 7.b. Ketersediaan Dokumen pelaksanaan SPMI');
        $createCol($t7b, 'Dokumen', 'dokumen');
        $createCol($t7b, 'Link Dokumen', 'link');
        $createCol($t7b, 'Link Laporan Hasil Audit', 'audit');
        $createCol($t7b, 'Link Laporan RTM', 'rtm');
        $createCol($t7b, 'Link Dokumen Peningkatan', 'up');
    }
}