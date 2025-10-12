<?php

namespace Database\Seeders;

use App\Models\TechnicalCustomProducts;
use Illuminate\Database\Seeder;

class TechnicalCustomProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TechnicalCustomProducts::factory(50)->create();
    }
}
