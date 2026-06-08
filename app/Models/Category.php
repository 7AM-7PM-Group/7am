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
        return $this->hasMany(Product::class, 'category_id', 'jurnal_id');
    }

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }

    public function setCategories()
    {
        return $this->belongsToMany(
            SetCategory::class,          // model tujuan
            'set_category_items',        // tabel pivot
            'category_id',               // FK dari Category
            'set_category_id'            // FK dari SetCategory
        )
            ->select('set_categories.*'); // 👈 cegah konflik id
    }
}
