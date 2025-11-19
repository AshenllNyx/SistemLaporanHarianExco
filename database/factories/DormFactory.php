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
        // choose dorm name first so we can assign specific capacities
        $dormNames = ['Dorm A', 'Dorm B', 'Dorm C', 'Dorm D'];
        $nama = fake()->randomElement($dormNames);

        // set capacity per dorm (specific for Dorm A/B, default for others)
        $capacityMap = [
            'Dorm A' => 8,
            'Dorm B' => 12,
            'Dorm C' => 10,
            'Dorm D' => 6,
        ];
        $capacity = $capacityMap[$nama] ?? 8;

        // generate between 1 and capacity students
        $count = random_int(1, $capacity);
        $students = [];
        for ($i = 0; $i < $count; $i++) {
            $students[] = [
                'no_ic' => fake()->unique()->numerify('###########'),
                'nama' => fake()->name(),
                'jantina' => fake()->randomElement(['Lelaki', 'Perempuan']),
            ];
        }

        return [
            'nama_dorm' => $nama,
            'capacity' => $capacity,
            'senarai_pelajar' => json_encode($students),
        ];
    }
}
