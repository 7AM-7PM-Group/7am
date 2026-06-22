<?php

namespace Database\Seeders;

use App\Models\MigrationDb;
use Illuminate\Database\Seeder;

class MigrationDbSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $process = [
            'user', 'business', 'coupon', 'member', 'transaction', 'setting', 'outlet',
        ];

        foreach ($process as $key => $item) {
            MigrationDb::create(['process' => $item]);
        }
    }
}
