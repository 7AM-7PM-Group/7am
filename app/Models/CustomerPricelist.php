<?php

namespace App\Models;

use App\Services\EsbApiAuth;
use App\Services\EsbApiRequest;
use App\Services\EsbApiRequest\PricelistRequest;
use Database\Factories\CustomerPricelistFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPricelist extends Model
{
    /** @use HasFactory<CustomerPricelistFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'ProductID');
    }

    public function customer()
    {
        return $this->belongsTo(Business::class, 'customer_id', 'CustomerID');
    }

    public static function syncCustomerPricelist()
    {
        $request = new PricelistRequest(new EsbApiRequest(app(EsbApiAuth::class)));
        $page = 1;
        do {

            $response = $request->getCustomerPricelists(['page' => $page]);

            if (! $response || $response['status'] === 'fail') {
                if (config('app.debug')) {
                    throw new \Exception($response['message'] ?? '');
                }
                session()->flash('error', $response['message'] ?? '');

                return;
            }

            $result = $response['result']['data'];

            foreach ($result as $key => $item) {
                $business = Business::where('name', $item['customerName'])->first();
                $product = Product::where('productName', $item['productName'])->first();

                // dd($business, $product, $item);
                CustomerPricelist::updateOrCreate(['customer_id' => $business->customerID, 'product_id' => $product->productID], ['price' => $item['price']]);
            }
            $page++;
        } while ($response['next'] ?? false);

    }
}
