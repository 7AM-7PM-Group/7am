<?php

namespace Database\Seeders;

use App\Models\CustomerPricelist;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::syncProduct();
        CustomerPricelist::syncCustomerPricelist()
    }
}
