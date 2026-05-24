<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'slug',
        'is_freeshiping',
        'product_information',
        'meta_keywords',
        'meta_description',
        'meta_title',
        'material',
        'finishing',
        'color',
        'weight',
        'type_weight',
        'size_long',
        'size_tall',
        'size_wide',
        'type_size',
        'package_long',
        'package_wide',
        'package_tall',
        'sku',
        'base_price',
        'base_strike_price',
        'base_discount_percent',
        'sort',
        'tags',
        'product_protection_percent',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'weight' => 'decimal:2',
        'size_long' => 'decimal:2',
        'size_tall' => 'decimal:2',
        'size_wide' => 'decimal:2',
        'package_long' => 'decimal:2',
        'package_wide' => 'decimal:2',
        'package_tall' => 'decimal:2',
        'base_price' => 'decimal:2',
        'base_strike_price' => 'decimal:2',
        'base_discount_percent' => 'decimal:2',
        'sort' => 'integer',
        'product_protection_percent' => 'integer',
    ];

    public function scopeStatus($query, $status = 'ACTIVE')
    {
        return $query->where('status', $status);
    }

    public function hasMany_category()
    {
        return $this->hasMany(ProductCategory::class, 'fk_product_id', 'id');
    }

    public function hasMany_brand()
    {
        return $this->hasMany(BrandProduct::class, 'fk_product_id', 'id')->with('fk_brand');
    }

    // Collections removed - not used in frontend
    // public function hasMany_collection()
    // {
    //     return $this->hasMany(ProductCollection::class, 'fk_product_id', 'id');
    // }

    public function hasMany_variant()
    {
        return $this->hasMany(ProductVariant::class, 'fk_product_id', 'id');
    }

    public function variant_cheapest_price()
    {
        return $this->hasMany(ProductVariant::class, 'fk_product_id', 'id')->orderBy('price', 'asc')->limit(1);
    }

    public function hasMany_image()
    {
        return $this->hasMany(ProductImage::class, 'fk_product_id', 'id')->orderBy('order_number', 'asc');
    }
    
    public function hasMany_variantActive()
    {
        return $this->hasMany(ProductVariant::class, 'fk_product_id', 'id')->where('status', 'ACTIVE')->orderBy('price', 'asc');
    }

    public function hasMany_imageThumbnail()
    {
        return $this->hasMany(ProductImage::class, 'fk_product_id', 'id')->limit(2)->orderBy('order_number', 'asc');
    }

    public function hasMany_image_getPrimaryImage()
    {
        return $this->hasOne(ProductImage::class, 'fk_product_id', 'id')->orderBy('order_number', 'asc');
    }

    public function variant_active_cheapest_price()
    {
        return $this->hasMany(ProductVariant::class, 'fk_product_id', 'id')->where('status', 'ACTIVE')->orderBy('price', 'asc')->limit(1);
    }

    public function variant_active_or_inactive_cheapest_price()
    {
        return $this->hasMany(ProductVariant::class, 'fk_product_id', 'id')->orderBy('price', 'asc')->limit(1);
    }

    /**
     * Get the stores for the product.
     */
    public function stores()
    {
        return $this->belongsToMany(Store::class, 'store_products', 'fk_product_id', 'store_id')
            ->withPivot('stock', 'shipping_cost', 'estimated_days_min', 'estimated_days_max', 'is_available')
            ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class, 'fk_product_id', 'id');
    }

    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class, 'product_id', 'id')->orderBy('sort', 'asc');
    }
}
