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
        Schema::table('workflows', function (Blueprint $table) {
            $table->string('action')->after('to_status');
            $table->string('old_value')->nullable()->after('action');
            $table->string('new_value')->nullable()->after('old_value');
            $table->json('metadata')->nullable()->after('new_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workflows', function (Blueprint $table) {
            $table->dropColumn(['action', 'old_value', 'new_value', 'metadata']);
        });
    }
};
