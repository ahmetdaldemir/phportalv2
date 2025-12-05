<?php

namespace Database\Seeders;

use App\Models\Demand;
use Illuminate\Database\Seeder;

class DemandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Demand::factory(50)->create();
    }
}
