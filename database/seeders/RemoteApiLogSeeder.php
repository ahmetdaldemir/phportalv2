<?php

namespace Database\Seeders;

use App\Models\RemoteApiLog;
use Illuminate\Database\Seeder;

class RemoteApiLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RemoteApiLog::factory(50)->create();
    }
}
