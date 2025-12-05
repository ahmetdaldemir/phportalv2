<?php

namespace Database\Seeders;

use App\Models\PersonalAccountMonth;
use Illuminate\Database\Seeder;

class PersonalAccountMonthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PersonalAccountMonth::factory(50)->create();
    }
}
