<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricelist extends Model
{
    /** @use HasFactory<\Database\Factories\PricelistFactory> */
    use HasFactory;

    public $guarded = ['id'];
}
