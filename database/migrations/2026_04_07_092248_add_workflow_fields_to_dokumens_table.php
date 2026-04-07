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
        Schema::table('dokumens', function (Blueprint $table) {
            $table->enum('workflow_stage', ['upload', 'ai_analysis', 'review', 'revision', 'final_approval'])->default('upload')->after('status');
            $table->timestamp('submitted_at')->nullable()->after('workflow_stage');
            $table->timestamp('reviewed_at')->nullable()->after('submitted_at');
            $table->timestamp('approved_at')->nullable()->after('reviewed_at');
            $table->text('reviewer_notes')->nullable()->after('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumens', function (Blueprint $table) {
            $table->dropColumn(['workflow_stage', 'submitted_at', 'reviewed_at', 'approved_at', 'reviewer_notes']);
        });
    }
};
