<?php

namespace Database\Seeders;

use App\Models\VersionChild;
use Illuminate\Database\Seeder;

class VersionChildSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VersionChild::factory(50)->create();
    }
}
