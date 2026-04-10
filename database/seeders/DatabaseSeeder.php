<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            KriteriaSeeder::class,
            ProdiSeeder::class,
            UserSeeder::class,
            LamInfokomSeeder::class,
            LamembaSeeder::class,
            LamTeknikSeeder::class,
            InfokomLedSeeder::class,
            LamembaLedSeeder::class,
            LamTeknikLedSeeder::class,
        ]);
    }
}
