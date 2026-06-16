<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class Business extends Model
{
    /** @use HasFactory<\Database\Factories\BusinessFactory> */
    use HasFactory;

    public $guarded = ['id'];

    public $table = 'bussinesses';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pricelist()
    {
        return $this->hasMany(CustomerPricelist::class, 'customer_id', 'customerID');
    }

    public function scopeFilters(Builder $query, array $filters)
    {
        $query->when($filters["search"] ?? false, function ($query, $search) {
            return $query->where("name", "like", "%{$search}%");
        });
        $query->when($filters["status"] ?? false, function ($query, $search) {
            return $query->where("status", $search);
        });
    }
}
