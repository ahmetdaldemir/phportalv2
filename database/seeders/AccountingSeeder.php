<?php

namespace Database\Seeders;

use App\Models\Accounting;
use Illuminate\Database\Seeder;

class AccountingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Accounting::factory(50)->create();
    }
}
