<?php

namespace Database\Seeders;

use App\Models\StockCardPrice;
use Illuminate\Database\Seeder;

class StockCardPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StockCardPrice::factory(50)->create();
    }
}
