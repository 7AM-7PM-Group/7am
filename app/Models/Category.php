<?php

namespace App\Models;

// use App\Models\Category;use Cviebrock\EloquentSluggable\Sluggable;
use App\Services\EsbApiAuth;
use App\Services\EsbApiRequest;
use App\Services\EsbApiRequest\CategoryRequest;
use Cviebrock\EloquentSluggable\Sluggable;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory, Sluggable;

    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'onUpdate' => true,
                'source' => 'categoryName',
            ],
        ];
    }

    protected $guarded = ['id'];

    protected $with = ['products'];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'categoryID');
    }

    public static function syncCategory()
    {
        $request = new CategoryRequest(new EsbApiRequest(app(EsbApiAuth::class)));

        $filters = ['limit' => 9999];

        $response = $request->getCategories($filters);

        // dd($response);

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
                ['categoryID' => $item['categoryID']],
                $item
            );
        }

        return 1;
    }
}
