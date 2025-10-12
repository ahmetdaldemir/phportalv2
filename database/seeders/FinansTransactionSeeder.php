<?php

namespace Database\Seeders;

use App\Models\FinansTransaction;
use Illuminate\Database\Seeder;

class FinansTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FinansTransaction::factory(50)->create();
    }
}
