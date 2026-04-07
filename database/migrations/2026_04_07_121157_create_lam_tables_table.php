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
        Schema::create('lam_tables', function (Blueprint $table) {
            $table->id();
            $table->string('lam_type'); // ban-pt, lam-infokom, etc
            $table->string('slug')->unique(); // mhs_baru, sdm_dosen
            $table->string('label'); // Mhs Baru & Lulusan (2.a)
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lam_tables');
    }
};
