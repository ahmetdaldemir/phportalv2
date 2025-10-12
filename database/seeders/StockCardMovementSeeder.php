<?php

namespace Database\Seeders;

use App\Models\StockCardMovement;
use Illuminate\Database\Seeder;

class StockCardMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StockCardMovement::factory(50)->create();
    }
}
