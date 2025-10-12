<?php

namespace Database\Seeders;

use App\Models\TechnicalProcess;
use Illuminate\Database\Seeder;

class TechnicalProcessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TechnicalProcess::factory(50)->create();
    }
}
