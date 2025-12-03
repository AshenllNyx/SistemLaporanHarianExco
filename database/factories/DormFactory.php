<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DormFactory extends Factory
{
    public function definition(): array
    {
        // 1. SENARAI BLOK → DORM
        $blocks = [
            'A' => ['A1', 'A2', 'A3', 'A4'],
            'B' => ['B1', 'B2'],
            'C' => ['C1', 'C2'],
            'D' => ['D1'],
        ];

        // 2. PILIH BLOK
        $blok = fake()->randomElement(array_keys($blocks));

        // 3. PILIH DORM DALAM BLOK
        $nama = fake()->randomElement($blocks[$blok]);

        // 4. RANDOM BILANGAN PELAJAR
        $capacity = fake()->numberBetween(5, 15);

        $students = [];
        for ($i = 0; $i < $capacity; $i++) {
            $students[] = [
                'no_ic' => fake()->unique()->numerify('###########'),
                'nama' => fake()->name(),
                'jantina' => fake()->randomElement(['Lelaki', 'Perempuan']),
            ];
        }

        return [
            'nama_dorm' => $nama,
            'blok' => $blok,
            'senarai_pelajar' => json_encode($students),
        ];
    }
}
