<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class LaporanHarianFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'no_ic' => fake()->unique()->numerify('###########'),
            'nama_exco' => fake()->name(),
            'tarikh_laporan' => fake()->date(),
            'tarikh_hantar' => fake()->date(),
            'sebab_hantar_semula' => fake()->sentence(),
            'status_laporan' => fake()->randomElement(['pending', 'approved', 'rejected']),
        ];
    }
}
