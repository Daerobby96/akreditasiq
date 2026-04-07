<?php

namespace Database\Factories;

use App\Models\Prodi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prodi>
 */
class ProdiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->randomElement([
                'Teknik Informatika',
                'Teknik Elektro',
                'Teknik Sipil',
                'Teknik Mesin',
                'Manajemen',
                'Akuntansi',
                'Psikologi',
                'Hukum',
                'Kedokteran',
                'Farmasi'
            ]),
            'kode' => strtoupper($this->faker->bothify('??###')),
            'jenjang' => $this->faker->randomElement(['S1', 'S2', 'S3']),
            'lam_type' => $this->faker->randomElement(['sarjana', 'magister', 'doktor']),
            'fakultas' => $this->faker->randomElement([
                'Fakultas Teknik',
                'Fakultas Ekonomi',
                'Fakultas Hukum',
                'Fakultas Kedokteran',
                'Fakultas Ilmu Sosial',
                'Fakultas Matematika & Ilmu Pengetahuan Alam'
            ]),
        ];
    }

    /**
     * Indicate that the program is undergraduate.
     */
    public function sarjana(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenjang' => 'S1',
            'lam_type' => 'sarjana',
        ]);
    }

    /**
     * Indicate that the program is graduate.
     */
    public function magister(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenjang' => 'S2',
            'lam_type' => 'magister',
        ]);
    }

    /**
     * Indicate that the program is doctoral.
     */
    public function doktor(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenjang' => 'S3',
            'lam_type' => 'doktor',
        ]);
    }

    /**
     * Create a program from a specific faculty.
     */
    public function inFaculty(string $faculty): static
    {
        return $this->state(fn (array $attributes) => [
            'fakultas' => $faculty,
        ]);
    }

    /**
     * Create a specific program.
     */
    public function named(string $name, string $code = null): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => $name,
            'kode' => $code ?? strtoupper(substr($name, 0, 3)) . rand(100, 999),
        ]);
    }
}
