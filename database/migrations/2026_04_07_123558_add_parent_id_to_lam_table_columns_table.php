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
        Schema::table('lam_table_columns', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('lam_table_id')->constrained('lam_table_columns')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lam_table_columns', function (Blueprint $table) {
            //
        });
    }
};
