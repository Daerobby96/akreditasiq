<?php

namespace Database\Factories;

use App\Models\Dokumen;
use App\Models\Kriteria;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dokumen>
 */
class DokumenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'kriteria_id' => Kriteria::factory(),
            'prodi_id' => Prodi::factory(),
            'nama_file' => $this->faker->sentence(3) . '.pdf',
            'file_path' => 'documents/' . $this->faker->uuid . '.pdf',
            'versi' => '1.0',
            'status' => $this->faker->randomElement(['draft', 'submitted', 'under_review', 'approved', 'rejected']),
            'workflow_stage' => $this->faker->randomElement(['upload', 'ai_analysis', 'review', 'revision', 'final_approval']),
            'metadata' => [
                'file_size' => $this->faker->numberBetween(1000, 10000000),
                'mime_type' => 'application/pdf',
                'uploaded_at' => now()->toISOString(),
                'description' => $this->faker->sentence()
            ]
        ];
    }

    /**
     * Indicate that the document is in draft status.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'workflow_stage' => 'upload',
        ]);
    }

    /**
     * Indicate that the document is submitted.
     */
    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'submitted',
            'workflow_stage' => 'ai_analysis',
            'submitted_at' => now(),
        ]);
    }

    /**
     * Indicate that the document is under review.
     */
    public function underReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'under_review',
            'workflow_stage' => 'review',
            'submitted_at' => now(),
            'reviewed_at' => now(),
        ]);
    }

    /**
     * Indicate that the document is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'workflow_stage' => 'final_approval',
            'submitted_at' => now(),
            'reviewed_at' => now(),
            'approved_at' => now(),
        ]);
    }

    /**
     * Indicate that the document has a specific workflow stage.
     */
    public function inStage(string $stage): static
    {
        return $this->state(fn (array $attributes) => [
            'workflow_stage' => $stage,
        ]);
    }

    /**
     * Indicate that the document was created from a template.
     */
    public function fromTemplate(\App\Models\DocumentTemplate $template): static
    {
        return $this->state(fn (array $attributes) => [
            'template_id' => $template->id,
            'kriteria_id' => $template->kriteria_id,
            'template_data' => [
                'template_version' => $template->currentVersion?->version_number ?? '1.0.0',
                'filled_variables' => $template->variables ?? [],
                'generated_at' => now()->toISOString()
            ],
        ]);
    }
}
