<?php

namespace Database\Seeders;

use App\Models\EInvoice;
use Illuminate\Database\Seeder;

class EInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EInvoice::factory(50)->create();
    }
}
