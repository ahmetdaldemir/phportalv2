<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    public function run()
    {
        $currencies = [
            [
                'id' => 1,
                'name' => 'Türk Lirası',
                'code' => 'TRY',
                'symbol' => '₺',
                'format' => '{amount} {symbol}',
                'exchange_rate' => '1.00',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'US Dollar',
                'code' => 'USD',
                'symbol' => '$',
                'format' => '{symbol}{amount}',
                'exchange_rate' => '32.50',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
                'format' => '{amount} {symbol}',
                'exchange_rate' => '35.20',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Pound Sterling',
                'code' => 'GBP',
                'symbol' => '£',
                'format' => '{symbol}{amount}',
                'exchange_rate' => '40.80',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('currencies')->insert($currencies);
    }
}