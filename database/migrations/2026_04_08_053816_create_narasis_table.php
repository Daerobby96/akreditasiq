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
        Schema::create('narasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prodi_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kriteria_id')->constrained()->cascadeOnDelete();
            $table->json('content')->nullable();
            $table->string('status')->default('todo'); // todo, in_progress, review, done
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['prodi_id', 'kriteria_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('narasis');
    }
};
