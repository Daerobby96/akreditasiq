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
        Schema::create('lam_table_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lam_table_id')->constrained('lam_tables')->onDelete('cascade');
            $table->string('header_name'); // Nama Dosen
            $table->string('field_name'); // nama_dosen
            $table->string('data_type')->default('text'); // text, number, currency, date
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lam_table_columns');
    }
};
