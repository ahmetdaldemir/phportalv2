<?php

namespace Database\Seeders;

use App\Models\TechnicalServiceCategory;
use Illuminate\Database\Seeder;

class TechnicalServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TechnicalServiceCategory::factory(50)->create();
    }
}
