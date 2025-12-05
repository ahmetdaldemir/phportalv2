<?php

namespace Database\Seeders;

use App\Models\UserSallary;
use Illuminate\Database\Seeder;

class UserSallarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserSallary::factory(50)->create();
    }
}
