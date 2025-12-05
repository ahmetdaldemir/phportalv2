<?php

namespace Database\Seeders;

use App\Models\Enumeration;
use Illuminate\Database\Seeder;

class EnumerationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Enumeration::factory(50)->create();
    }
}
