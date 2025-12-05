<?php

namespace Database\Seeders;

use App\Models\OlderEnumeration;
use Illuminate\Database\Seeder;

class OlderEnumerationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OlderEnumeration::factory(50)->create();
    }
}
