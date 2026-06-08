<?php

namespace App\Models;

use App\Services\JurnalApi;
use App\Services\JurnalApiResponse;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
                    'product_code',
                    'name'
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

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function links()
    {
        return $this->hasMany(CouponProduct::class);
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

        $query->when($filters["set_category"] ?? null, function ($query, $setCategoryId) {
            return $query->whereHas('category', function ($q) use ($setCategoryId) {
                $q->whereHas('setCategories', function ($sq) use ($setCategoryId) {
                    $sq->where('set_categories.id', $setCategoryId);
                });
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

    public static function sync(JurnalApi $jurnalApi)
    {
        // dd($jurnalApi);
        $response = $jurnalApi->request('GET', '/public/jurnal/api/v1/products?&per_page=10000');

        if (isset($response['products'])) {
            $products = new JurnalApiResponse(collect($response['products'] ?? []));
            $products = $products->get();
        } else {
            $products = [];
        }

        try {
            DB::beginTransaction();

            // 1. Ambil semua ID produk dari Jurnal
            $jurnalProductIds = collect($products)->pluck('id')->toArray();

            // 2. Hapus produk lokal yang tidak ada lagi di Jurnal
            self::whereNotNull('jurnal_id')->whereNotIn('jurnal_id', $jurnalProductIds)->delete();

            // 3. Ambil daftar Jurnal ID dari kategori yang aktif dan memiliki relasi ke set_category_items
            $activeCategoryJurnalIds = Category::where('active', true)->pluck('jurnal_id')->toArray();

            // dd($activeCategoryJurnalIds);

            self::whereNotIn('category_id', $activeCategoryJurnalIds)->delete();
            // dd($products);
            // 4. Update atau buat produk baru dari data Jurnal
            foreach ($products as $key => $item) {
                // Dapatkan Jurnal ID kategori dari produk ini
                $productCategoryId = $item['product_categories'][0]['id'] ?? null;
                // Lanjutkan hanya jika kategori produk ada di daftar kategori aktif
                if ($productCategoryId && in_array($productCategoryId, $activeCategoryJurnalIds)) {
                    // dd($productCategoryId);
                    self::updateOrCreate(
                        ['jurnal_id' => $item['id']],
                        [
                            'product_code' => $item['product_code'],
                            'name' => $item['name'],
                            'image' => $item['image']['url'] ?? null,
                            'description' => $item['description'] ?? '',
                            'price' => $item['sell_price_per_unit'] ?? 0,
                            'unit' => $item['unit']['name'] ?? '',
                            'moq' => $item['moq'] ?? 1,
                            'active' => $item['active'] ?? true,
                            'category_id' => $productCategoryId,
                        ]
                    );
                }
            }

            DB::commit();
            session()->flash('success', 'Products synchronized successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            if (config('app.debug', false)) {
                throw $th;
            }
            session()->flash('error', 'Failed to synchronize products: ' . $th->getMessage());
        }
    }
}
