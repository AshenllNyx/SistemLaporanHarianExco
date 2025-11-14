<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Dorm;    

class Dormseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Dorm::factory()->count(10)->create();
    }
}
