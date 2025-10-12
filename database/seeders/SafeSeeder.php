<?php

namespace Database\Seeders;

use App\Models\Safe;
use Illuminate\Database\Seeder;

class SafeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Safe::factory(50)->create();
    }
}
