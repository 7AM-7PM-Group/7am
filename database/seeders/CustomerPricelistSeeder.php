<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\CustomerPricelist;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerPricelistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(1, 30) as $key => $value) {

            $product = Product::inRandomOrder()->first();
            $business = Business::inRandomOrder()->first();


            CustomerPricelist::factory()->create([
                'product_id' => $product->productID,
                'customer_id' => $business->customerID,
            ]);
        }
    }
}
