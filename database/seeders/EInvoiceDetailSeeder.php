<?php

namespace Database\Seeders;

use App\Models\EInvoiceDetail;
use Illuminate\Database\Seeder;

class EInvoiceDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EInvoiceDetail::factory(50)->create();
    }
}
