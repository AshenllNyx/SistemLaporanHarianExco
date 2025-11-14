<?php

namespace Database\Factories;

use App\Models\Dorm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ButiranLaporanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jenis = fake()->randomElement(['kerosakan', 'kebersihan', 'keselamatan', 'kehadiran']);
        $id_dorm = random_int(1, 10);
        
        return [
            'id_laporan' => random_int(1, 50),
            'id_dorm' => $id_dorm,
            'jenis_butiran' => $jenis,
            'deskripsi_isu' => fake()->paragraph(),
            'data_tambahan' => $this->generateDataByType($jenis, $id_dorm),
        ];
    }

    /**
     * Generate data_tambahan based on report type
     */
    private function generateDataByType(string $jenis, int $id_dorm = null): array
    {
        // Get students from the dorm if it's an attendance report
        $studentList = [];
        if ($jenis === 'kehadiran' && $id_dorm) {
            $dorm = Dorm::find($id_dorm);
            if ($dorm && is_array($dorm->senarai_pelajar)) {
                $studentList = array_slice($dorm->senarai_pelajar, 0, random_int(1, 5));
            }
        }
        
        return match($jenis) {
            'kehadiran' => [
                'tarikh' => fake()->date(),
                'pelajar_hadir' => $studentList ?: [fake()->numerify('###########'), fake()->numerify('###########')],
                'jumlah_hadir' => count($studentList),
                'status_keseluruhan' => fake()->randomElement(['Semua Hadir', 'Ada Yang Tidak Hadir', 'Sebahagian Hadir']),
                'catatan_kehadiran' => fake()->sentence(),
            ],
            'kerosakan' => [
                'waktu_kejadian' => fake()->time(),
                'lokasi_kerosakan' => fake()->randomElement(['Bilik Rehat', 'Dapur', 'Bilik Air', 'Ruang Tamu']),
                'jenis_kerosakan' => fake()->randomElement(['Pintu', 'Tingkap', 'Lampu', 'Paip', 'Cermin']),
                'gambar' => [fake()->imageUrl(), fake()->imageUrl()],
                'kos_anggaran' => fake()->numberBetween(100, 5000),
            ],
            'kebersihan' => [
                'waktu_kejadian' => fake()->time(),
                'area_bermasalah' => fake()->randomElement(['Dapur', 'Bilik Air', 'Ruang Tamu', 'Luar Rumah']),
                'jenis_masalah' => fake()->randomElement(['Sampah', 'Kotor', 'Bau', 'Serangga']),
                'gambar' => [fake()->imageUrl()],
                'tindakan' => fake()->sentence(),
            ],
            'keselamatan' => [
                'waktu_kejadian' => fake()->time(),
                'jenis_insiden' => fake()->randomElement(['Kebakaran', 'Kecelakaaan', 'Pencuri', 'Kecederaan']),
                'tahap_keterukan' => fake()->randomElement(['Ringan', 'Sederhana', 'Serius']),
                'saksi' => [fake()->name(), fake()->name()],
                'laporan_polis' => fake()->randomElement([true, false]),
                'gambar' => [fake()->imageUrl()],
            ],
            default => []
        };
    }

    /**
     * State for attendance reports
     */
    public function attendance(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_butiran' => 'kehadiran',
            'data_tambahan' => $this->generateDataByType('kehadiran'),
        ]);
    }

    /**
     * State for damage reports
     */
    public function damage(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_butiran' => 'kerosakan',
            'data_tambahan' => $this->generateDataByType('kerosakan'),
        ]);
    }

    /**
     * State for cleanliness reports
     */
    public function cleanliness(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_butiran' => 'kebersihan',
            'data_tambahan' => $this->generateDataByType('kebersihan'),
        ]);
    }

    /**
     * State for safety reports
     */
    public function safety(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_butiran' => 'keselamatan',
            'data_tambahan' => $this->generateDataByType('keselamatan'),
        ]);
    }
}
