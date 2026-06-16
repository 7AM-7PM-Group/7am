<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPricelist extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerPricelistFactory> */
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
}
