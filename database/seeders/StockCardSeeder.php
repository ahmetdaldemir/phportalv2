<?php

namespace Database\Seeders;

use App\Models\StockCard;
use Illuminate\Database\Seeder;

class StockCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StockCard::factory(50)->create();
    }
}
