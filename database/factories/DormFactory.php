<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dorm>
 */
class DormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $students = array_map(function () {
            return [
                'no_ic' => fake()->unique()->numerify('###########'),
                'nama' => fake()->name(),
            ];
        }, range(1, random_int(5, 20)));

        return [
            'nama_dorm' => fake()->company(),
            'senarai_pelajar' => json_encode($students),
        ];
    }
}
