<?php

namespace Database\Seeders;

use App\Models\TechnicalServiceProducts;
use Illuminate\Database\Seeder;

class TechnicalServiceProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TechnicalServiceProducts::factory(50)->create();
    }
}
