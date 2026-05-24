<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $table = 'stores';

    protected $fillable = [
        'name',
        'code',
        'email',
        'phone',
        'address',
        'city',
        'province',
        'country',
        'postal_code',
        'status',
        'description',
        'city_id',
    ];

    /**
     * Get the products for the store.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'store_products', 'store_id', 'fk_product_id')
            ->withPivot('stock', 'shipping_cost', 'estimated_days_min', 'estimated_days_max', 'is_available')
            ->withTimestamps();
    }

    /**
     * Get the variant stocks for the store.
     */
    public function variantStocks()
    {
        return $this->hasMany(ProductVariantStock::class, 'store_id', 'id');
    }
}
