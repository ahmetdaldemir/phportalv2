<?php

namespace Database\Seeders;

use App\Models\SellerAccountMonth;
use Illuminate\Database\Seeder;

class SellerAccountMonthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SellerAccountMonth::factory(50)->create();
    }
}
