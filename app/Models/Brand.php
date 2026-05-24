<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'brands';

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'status',
        'order',
        'description',
    ];

    /**
     * Get only active brands.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    /**
     * Order brands by order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('created_at', 'desc');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'brand_products', 'fk_brand_id', 'fk_product_id');
    }
}
