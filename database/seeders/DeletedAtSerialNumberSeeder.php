<?php

namespace Database\Seeders;

use App\Models\DeletedAtSerialNumber;
use Illuminate\Database\Seeder;

class DeletedAtSerialNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DeletedAtSerialNumber::factory(50)->create();
    }
}
