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
        Schema::create('template_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('document_templates')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('version_number');
            $table->text('content')->nullable(); // Template content/content
            $table->json('variables')->nullable(); // Variable definitions for this version
            $table->text('change_log')->nullable(); // What changed in this version
            $table->string('file_path')->nullable(); // Path to version file
            $table->boolean('is_current')->default(false);
            $table->timestamps();

            $table->index(['template_id', 'is_current']);
            $table->index(['template_id', 'version_number']);
            $table->unique(['template_id', 'version_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_versions');
    }
};
