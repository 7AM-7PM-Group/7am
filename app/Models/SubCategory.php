<?php

namespace App\Models;

use App\Models\Product;
use App\Models\SubCategory;
use App\Services\EsbApiAuth;
use App\Services\EsbApiRequest;
use App\Services\EsbApiRequest\SubCategoryRequest;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubCategory extends Model
{
    /** @use HasFactory<\Database\Factories\SubCategoryFactory> */
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
                'source' => 'subCategoryName'
            ]
        ];
    }

    public $guarded = ['id'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'sub_category_id', 'subCategoryID');
    }

    public static function syncSubCategory()
    {
        $request = new SubCategoryRequest(new EsbApiRequest(app(EsbApiAuth::class)));

        $filters = ['limit' => 9999];

        $response = $request->getSubCategories($filters);

        if (! $response || $response['status'] === 'fail') {
            if (config('app.debug')) {
                throw new \Exception($response['message'] ?? '');
            }
            session()->flash('error', $response['message'] ?? '');

            return 0;
        }

        $result = $response['result']['data'];

        foreach ($result as $key => $item) {

            self::updateOrCreate(
                ['subCategoryID' => $item['subCategoryID']],
                $item
            );
        }

        return 1;
    }
}