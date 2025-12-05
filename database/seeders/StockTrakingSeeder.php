<?php

namespace Database\Seeders;

use App\Models\StockTraking;
use Illuminate\Database\Seeder;

class StockTrakingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StockTraking::factory(50)->create();
    }
}
