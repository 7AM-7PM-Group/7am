<?php

namespace App\Models;

use App\Services\EsbApiAuth;
use App\Services\EsbApiRequest;
use App\Services\EsbApiRequest\ProductRequest;
use Cviebrock\EloquentSluggable\Sluggable;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, Sluggable, SoftDeletes;

    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'onUpdate' => true,
                'source' => [
                    'productCode',
                    'productName',
                ],
            ],
        ];
    }

    protected $guarded = ['id'];

    protected $perPage = 12;

    protected $jurnalApi;

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }

    public function getDisplayPriceAttribute()
    {

        // dd(
        //     $this->productID,
        //     $this->pricelist()->count(),
        //     $this->pricelist()->first()
        // );

        $customerId = Auth::user()?->businesses?->customerID;

        if (! $customerId) {

            return $this->price;
        }

        $customerPrice = $this->pricelist->where('customer_id', $customerId)->first()->price;

        return $customerPrice ?? $this->price;
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'categoryID');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id', 'subCategoryID');
    }

    public function links()
    {
        return $this->hasMany(CouponProduct::class);
    }

    public function pricelist()
    {
        // Gunakan 'id' (numeric primary key) agar cocok dengan data dari Factory/Seeder
        return $this->hasMany(CustomerPricelist::class, 'product_id', 'productID');
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, CouponProduct::class);
    }

    public function scopeActive(Builder $query, bool $active = true) {
        $query->when($active, function($query, $active) {
            return $query->where('active', $active);
        });
    }

    public function scopeFilters(Builder $query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('productName', 'like', "%{$search}%")
                    ->orWhere('productCode', 'like', "%{$search}%");
            });
        });

        $query->when($filters['min'] ?? false, function ($query, $search) {
            return $query->where('price', '>', "$search");
        });

        $query->when($filters['max'] ?? false, function ($query, $search) {
            return $query->where('price', '<', "$search");
        });

        $query->when($filters['category'] ?? false, function ($query, $category) {
            return $query->whereHas('category', function ($q) use ($category) {
                $q->where('slug', $category);
            });
        });

        $query->when($filters['sub_category'] ?? null, function ($query, $subCategory) {
            return $query->whereHas('subCategory', function ($q) use ($subCategory) {
                $q->where('slug', $subCategory);
            });
        });
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return $this->image;
        }

        return asset('assets/No-Picture-Found.png');
    }

    public static function syncProduct()
    {
        self::syncProductRequest(true);
        self::syncProductRequest(false);

    }

    public static function syncProductRequest(bool $status) {

        $request = new ProductRequest(new EsbApiRequest(app(EsbApiAuth::class)));
        $page = 1;
        $prefix = Setting::where('key', 'product_code_prefix')->value('value');

        $active = $status ? "Yes" : 'No';
        do {

            $response = $request->getMasterProducts(['page' => $page, 'statusActive'=> $active]);

            if (! $response || $response['status'] === 'fail') {
                if (config('app.debug')) {
                    throw new \Exception($response['message'] ?? '');
                }
                session()->flash('error', $response['message'] ?? '');

                return;
            }

            $result = $response['result']['data'];

            foreach ($result as $key => $item) {
                if(!str_starts_with($item['productCode'], $prefix)) continue;

                foreach ($item['productDetails'] as $detail) {
                    if ($detail['defaultUnit']['baseUnit']) {
                        $price = $detail['basePrice'];
                        $unit = $detail['unit'];
                        break;
                    }
                }
                $item['price'] = $price ?? 0;
                $item['unit'] = $unit ?? '';

                $item['category_id'] = $item['categoryID'];
                $item['sub_category_id'] = $item['subCategoryID'];
                $item['active']=$status;

                Product::updateOrCreate(
                    ['productID' => $item['productID']],
                    $item
                );
            }
            $page++;
        } while ($response['next'] ?? false);
    }
}
