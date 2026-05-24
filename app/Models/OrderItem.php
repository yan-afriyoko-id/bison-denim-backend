<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    protected $fillable = [
        'fk_order_id',
        'fk_product_id',
        'fk_variant_id',
        'store_id',
        'product_name',
        'product_image',
        'sku',
        'variant_description',
        'qty',
        'actual_price',
        'discount_price',
        'purchase_price',
        'product_protection_percent',
        'product_protection_amount',
        'subtotal',
        'note',
        'review_id',
    ];

    protected $casts = [
        'fk_order_id' => 'integer',
        'fk_product_id' => 'integer',
        'fk_variant_id' => 'integer',
        'store_id' => 'integer',
        'qty' => 'integer',
        'actual_price' => 'integer',
        'discount_price' => 'integer',
        'purchase_price' => 'integer',
        'product_protection_percent',
        'product_protection_amount',
        'subtotal' => 'integer',
    ];

    /**
     * Relationship to Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'fk_order_id', 'id');
    }

    /**
     * Relationship to Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'fk_product_id', 'id');
    }

    /**
     * Relationship to ProductVariant
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'fk_variant_id', 'id');
    }

    /**
     * Relationship to Store
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }
    public function review(): HasOne
    {
        return $this->hasOne(ProductReview::class, 'order_item_id', 'id');
    }

    /**
     * Accessor for product_image to return full URL
     */
    public function getProductImageAttribute($value)
    {
        if (!$value) {
            return null;
        }

        if (str_starts_with($value, 'http')) {
            return $value;
        }

        return asset($value);
    }
}


