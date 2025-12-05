<?php

namespace Database\Seeders;

use App\Models\TechnicalCustomService;
use Illuminate\Database\Seeder;

class TechnicalCustomServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TechnicalCustomService::factory(50)->create();
    }
}
