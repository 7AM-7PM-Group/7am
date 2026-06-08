<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use App\Services\JurnalApi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{


    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i < 6; $i++) {
            $categories = Category::factory()->create(['categoryID' => $i]);
        }

        for ($i = 1; $i < 31; $i++) {
            # code...
            SubCategory::factory()->recycle($categories)->create(['subCategoryID' => $i]);
        }
    }
}
