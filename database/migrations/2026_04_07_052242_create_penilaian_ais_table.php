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
        Schema::create('penilaian_ais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dokumen_id')->constrained()->onDelete('cascade');
            $table->decimal('skor', 5, 2)->nullable(); // AI score
            $table->text('analisis_teks');
            $table->text('gap_analysis');
            $table->text('rekomendasi');
            $table->string('engine')->default('openai');
            $table->json('raw_response')->nullable(); // Store raw output for debugging
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_ais');
    }
};
