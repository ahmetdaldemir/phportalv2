<?php

namespace Database\Seeders;

use App\Models\TechnicalService;
use Illuminate\Database\Seeder;

class TechnicalServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TechnicalService::factory(50)->create();
    }
}
