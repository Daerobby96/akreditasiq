<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LamTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banpt = [
            [
                'slug' => 'mahasiswa',
                'label' => 'Mhs Baru & Lulusan (2.a)',
                'columns' => [
                    ['header' => 'Tahun Akademik', 'field' => 'tahun', 'type' => 'text'],
                    ['header' => 'Pendaftar', 'field' => 'pendaftar', 'type' => 'number'],
                    ['header' => 'Lulus Seleksi', 'field' => 'lulus_seleksi', 'type' => 'number'],
                    ['header' => 'Maba Reguler', 'field' => 'maba', 'type' => 'number'],
                    ['header' => 'Mhs Aktif', 'field' => 'aktif', 'type' => 'number'],
                    ['header' => 'Lulus Tepat', 'field' => 'lulus_tepat', 'type' => 'number'],
                ]
            ],
            [
                'slug' => 'dosen',
                'label' => 'SDM / Dosen Tetap (3.a.1)',
                'columns' => [
                    ['header' => 'Nama Dosen', 'field' => 'nama', 'type' => 'text'],
                    ['header' => 'Jabatan', 'field' => 'jabatan', 'type' => 'text'],
                    ['header' => 'Pendidikan', 'field' => 'pendidikan', 'type' => 'text'],
                    ['header' => 'Serdos', 'field' => 'sertifikasi', 'type' => 'text'],
                    ['header' => 'Bidang', 'field' => 'bidang', 'type' => 'text'],
                ]
            ]
        ];

        foreach ($banpt as $t) {
            $table = \App\Models\LamTable::create([
                'lam_type' => 'ban-pt',
                'slug' => $t['slug'],
                'label' => $t['label'],
            ]);

            foreach ($t['columns'] as $index => $c) {
                $table->columns()->create([
                    'header_name' => $c['header'],
                    'field_name' => $c['field'],
                    'data_type' => $c['type'],
                    'sort_order' => $index,
                ]);
            }
        }

        $infokom = [
            [
                'slug' => 'budaya_mutu',
                'label' => 'Budaya Mutu (Tabel 1.A)',
                'columns' => [
                    ['header' => 'No', 'field' => 'idx', 'type' => 'number'],
                    ['header' => 'Unit Kerja', 'field' => 'unit', 'type' => 'text'],
                    ['header' => 'Nama Ketua', 'field' => 'nama', 'type' => 'text'],
                    ['header' => 'Jabatan', 'field' => 'jabatan', 'type' => 'text'],
                    ['header' => 'Pendidikan', 'field' => 'pendidikan', 'type' => 'text'],
                    ['header' => 'Tugas Pokok', 'field' => 'bidang', 'type' => 'text'],
                ]
            ],
            [
                'slug' => 'pendanaan',
                'label' => 'Sumber Pendanaan (1.A.2)',
                'columns' => [
                    ['header' => 'Sumber Pendanaan', 'field' => 'nama', 'type' => 'text'],
                    ['header' => 'TS-2 (Juta)', 'field' => 'ts2', 'type' => 'currency'],
                    ['header' => 'TS-1 (Juta)', 'field' => 'ts1', 'type' => 'currency'],
                    ['header' => 'TS (Juta)', 'field' => 'ts', 'type' => 'currency'],
                ]
            ]
        ];

        foreach ($infokom as $t) {
            $table = \App\Models\LamTable::create([
                'lam_type' => 'lam-infokom',
                'slug' => $t['slug'],
                'label' => $t['label'],
            ]);

            foreach ($t['columns'] as $index => $c) {
                $table->columns()->create([
                    'header_name' => $c['header'],
                    'field_name' => $c['field'],
                    'data_type' => $c['type'],
                    'sort_order' => $index,
                ]);
            }
        }
    }
}
