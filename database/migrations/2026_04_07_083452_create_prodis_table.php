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
        Schema::create('prodis', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // e.g. Teknik Informatika
            $table->string('kode')->unique(); // e.g. TI-S1
            $table->string('jenjang')->default('S1');
            $table->string('lam_type')->default('ban-pt'); // ban-pt, lam-infokom, lam-emba, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prodis');
    }
};
