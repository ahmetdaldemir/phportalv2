<?php

namespace Database\Seeders;

use App\Models\TechnicalServiceProcess;
use Illuminate\Database\Seeder;

class TechnicalServiceProcessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TechnicalServiceProcess::factory(50)->create();
    }
}
