<?php

namespace Database\Seeders;

use App\Models\SiteTechnicalServiceCategory;
use Illuminate\Database\Seeder;

class SiteTechnicalServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SiteTechnicalServiceCategory::factory(50)->create();
    }
}
