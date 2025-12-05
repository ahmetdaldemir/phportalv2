<?php

namespace Database\Seeders;

use App\Models\AccountingCategory;
use Illuminate\Database\Seeder;

class AccountingCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AccountingCategory::factory(50)->create();
    }
}
