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
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('dokumens')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('version_number');
            $table->text('content')->nullable(); // For text content
            $table->json('metadata')->nullable(); // For additional version data
            $table->text('change_summary')->nullable(); // Summary of changes
            $table->string('file_path')->nullable(); // If version has different file
            $table->timestamps();

            $table->index(['document_id', 'version_number']);
            $table->index(['document_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_versions');
    }
};
