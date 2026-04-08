<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('prodis', function (Blueprint $row) {
            $row->string('peringkat_saat_ini')->nullable();
            $row->date('tanggal_kadaluarsa')->nullable();
            $row->date('target_submit')->nullable();
            $row->string('status_akreditasi')->default('aktif'); // aktif, persiapan, submited, visitasi
            $row->string('target_peringkat')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prodis', function (Blueprint $row) {
            $row->dropColumn(['peringkat_saat_ini', 'tanggal_kadaluarsa', 'target_submit', 'status_akreditasi', 'target_peringkat']);
        });
    }
};
