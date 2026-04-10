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
        $pnd4a = $createCol($t4a, 'Pendidikan Pascasarjana', 'pnd_group');
        $createCol($t4a, 'Sarjana', 's1', 'text', $pnd4a->id);
        $createCol($t4a, 'Magister', 's2', 'text', $pnd4a->id);
        $createCol($t4a, 'Doktor', 's3', 'text', $pnd4a->id);
        $createCol($t4a, 'Bidang Keahlian', 'bidang');
        $createCol($t4a, 'Perusahaan/Industri', 'perusahaan');
        $createCol($t4a, 'Kesesuaian Kompetensi', 'fit');
        $createCol($t4a, 'Jabatan Akademik', 'jabatan');
        $createCol($t4a, 'No Sertifikat Pnd', 'serdos');
        $sertkom4a = $createCol($t4a, 'Sertifikat Kompetensi', 'sertkom_group');
        $createCol($t4a, 'Bidang', 'sertkom_bidang', 'text', $sertkom4a->id);
        $createCol($t4a, 'Lembaga', 'sertkom_lembaga', 'text', $sertkom4a->id);
        $insinyur4a = $createCol($t4a, 'Sertifikat Insinyur', 'insinyur_group');
        $createCol($t4a, 'SKIP', 'skip', 'text', $insinyur4a->id);
        $createCol($t4a, 'STRI', 'stri', 'text', $insinyur4a->id);
        $createCol($t4a, 'MK PS Akreditasi', 'mk_ps');
        $createCol($t4a, 'Kesesuaian MK', 'mk_fit');
        $createCol($t4a, 'MK PS Lain', 'mk_lain');

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
        $createCol($t4c, 'Nama Dosen', 'nama');
        $createCol($t4c, 'DTPS', 'dtps');
        $bk4c = $createCol($t4c, 'Beban Kerja (sks) pada TS', 'bk_group');
        $pnd4c = $createCol($t4c, 'Pendidikan: Pembelajaran dan Pembimbingan', 'pnd_group', 'text', $bk4c->id);
        $createCol($t4c, 'PS yang Diakreditasi', 'ps_akre', 'number', $pnd4c->id);
        $createCol($t4c, 'PS Lain di PT', 'ps_lain_pt', 'number', $pnd4c->id);
        $createCol($t4c, 'PS Lain luar PT', 'ps_luar_pt', 'number', $pnd4c->id);
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

        // Masa Studi Chunks
        $addMS6d = function($slug, $label, $cols) use ($createTable, $createCol) {
            $t = $createTable($slug, $label);
            $createCol($t, 'No', 'no', 'number');
            $createCol($t, 'Tahun Masuk', 'tahun');
            $createCol($t, 'Jumlah Mahasiswa Masuk', 'jml_in', 'number');
            $lg = $createCol($t, 'Jumlah Mahasiswa Lulus', 'lulus_group');
            foreach ($cols as $c) {
                $createCol($t, $c, strtolower(str_replace([' ', '.', ',', '<', '=', '>'], '_', $c)), 'number', $lg->id);
            }
        };

        $addMS6d('tabel_6_d_masa_studi_s1', 'Tabel 6.d. Masa Studi (S1)', ['3.5 < MS <= 4.5', '4.5 < MS <= 5.5', '5.5 < MS <= 6.5', '6.5 < MS <= 8']);
        $addMS6d('tabel_6_d_masa_studi_d3', 'Tabel 6.d. Masa Studi (D3)', ['3 < MS <= 3,5', '3,5 < MS <= 6', 'MS <= 2,5']);

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