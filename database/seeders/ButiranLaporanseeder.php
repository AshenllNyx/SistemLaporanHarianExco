<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ButiranLaporan;

class ButiranLaporanseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ButiranLaporan::factory()->count(10)->create();
    }
}
