<?php

namespace Database\Seeders;

use App\Models\FakeProduct;
use Illuminate\Database\Seeder;

class FakeProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FakeProduct::factory(50)->create();
    }
}
