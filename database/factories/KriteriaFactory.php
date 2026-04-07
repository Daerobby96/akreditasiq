<?php

namespace Database\Factories;

use App\Models\Kriteria;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kriteria>
 */
class KriteriaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode' => 'C' . $this->faker->numberBetween(1, 9),
            'nama' => $this->faker->sentence(4),
            'deskripsi' => $this->faker->paragraph(),
            'bobot' => $this->faker->numberBetween(5, 20),
            'lam_type' => $this->faker->randomElement(['sarjana', 'magister', 'doktor']),
        ];
    }

    /**
     * Indicate that the kriteria is for undergraduate programs.
     */
    public function sarjana(): static
    {
        return $this->state(fn (array $attributes) => [
            'lam_type' => 'sarjana',
        ]);
    }

    /**
     * Indicate that the kriteria is for graduate programs.
     */
    public function magister(): static
    {
        return $this->state(fn (array $attributes) => [
            'lam_type' => 'magister',
        ]);
    }

    /**
     * Indicate that the kriteria is for doctoral programs.
     */
    public function doktor(): static
    {
        return $this->state(fn (array $attributes) => [
            'lam_type' => 'doktor',
        ]);
    }

    /**
     * Create a specific BAN-PT criteria.
     */
    public function banptCriteria(string $code, string $name, int $weight = 10): static
    {
        return $this->state(fn (array $attributes) => [
            'kode' => $code,
            'nama' => $name,
            'bobot' => $weight,
            'lam_type' => 'sarjana', // Default to undergraduate
        ]);
    }
}
