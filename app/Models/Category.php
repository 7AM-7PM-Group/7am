<?php

namespace App\Models;

use App\Models\Product;
// use App\Models\Category;
use App\Models\SetCategory;
use App\Services\JurnalApi;
use App\Livewire\Test\Jurnal;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory, Sluggable;

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'onUpdate' => true,
                'source' => 'categoryName'
            ]
        ];
    }

    protected $guarded = ['id'];
    protected $with = ['products'];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'categoryID');
    }
}
