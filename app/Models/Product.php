<?php

namespace App\Models;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CouponProduct;
use App\Models\CustomerPricelist;
use App\Models\SubCategory;
use App\Services\EsbApiAuth;
use App\Services\EsbApiRequest;
use App\Services\EsbApiRequest\ProductRequest;
use App\Services\JurnalApi;
use App\Services\JurnalApiResponse;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, Sluggable, SoftDeletes;

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
                'source' => [
                    'productCode',
                    'productName'
                ]
            ]
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

        dd(
            $this->productID,
            $this->pricelist()->count(),
            $this->pricelist()->first()
        );

        $customerId = Auth::user()?->businesses?->id;

        if (!$customerId) {
            return $this->price;
        }
        $customerPrice = $this->pricelist()
            ->where('customer_id', $customerId)
            ->value('price');

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

    public function scopeFilters(Builder $query, array $filters)
    {
        $query->when($filters["search"] ?? false, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where("name", "like", "%{$search}%")
                    ->orWhere("product_code", "like", "%{$search}%");
            });
        });

        $query->when($filters["min"] ?? false, function ($query, $search) {
            return $query->where("price", ">", "$search");
        });

        $query->when($filters["max"] ?? false, function ($query, $search) {
            return $query->where("price", "<", "$search");
        });

        $query->when($filters["category"] ?? false, function ($query, $category) {
            return $query->whereHas('category', function ($q) use ($category) {
                $q->where('slug', $category);
            });
        });

        $query->when($filters["sub_category"] ?? null, function ($query, $subCategory) {
            return $query->whereHas('subCategory', function ($q) use ($subCategory) {
                $q->where('slug', $subCategory);
            });
        });
    }

    public static function skuNumberGenerator()
    {
        $prefix = 'SKU-';

        // Hitung jumlah produk yang sudah
        $lastTransaction = self::orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;

        if ($lastTransaction) {
            $lastNumber = (int) substr($lastTransaction->sku, -4);
            $nextNumber = $lastNumber + 1;
        }

        $formattedNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return $prefix . $formattedNumber;
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return $this->image;
        }

        return asset('assets/No-Picture-Found.png');
    }

    public static function sync()
    {
        $request = new ProductRequest(new EsbApiRequest(app(EsbApiAuth::class)));

        $response = $request->getMasterProducts();

        if (!$response || $response['status'] === "fail") {
            if (config('app.debug')) {
                throw new \Exception($response['message'] ?? '');
            }
            session()->flash('error', $response['message'] ?? '');
            return;
        }

        $result = $response['result']['data'];

        foreach ($result as $key => $item) {
            foreach ($item['productDetails'] as  $detail) {
                if ($detail['defaultUnit']['baseUnit']) {
                    $price = $detail['basePrice'];
                    break;
                }
            }
            $item['price'] = $price ?? 0;

            Product::updateOrCreate(
                ['productID' => $item['productID']],
                $item
            );
        }
    }
}