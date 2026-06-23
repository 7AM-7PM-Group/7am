<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'product_code_prefix',
                'value' => 'CKC',
                'type' => 'text',
            ],
            [
                'key' => 'branchID',
                'value' => 1,
                'type' => 'number',
            ],
            [
                'key' => 'currencyID',
                'value' => 1,
                'type' => 'number',
            ],
            [
                'key' => 'vat_value',
                'value' => 1,
                'type' => 'number',
            ],
            [
                'key' => 'dpp_value',
                'value' => 1,
                'type' => 'number',
            ],
            [
                'key' => 'b2b_whatsapp_number',
                'value' => '6281339276640',
                'type' => 'text',
            ],

        ];

        foreach ($settings as $key => $item) {
            Setting::updateOrCreate(['key' => $item['key']], $item);
        }
    }
}
