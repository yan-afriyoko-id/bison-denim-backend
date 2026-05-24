<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $table = 'product_images';

    protected $fillable = [
        'fk_product_id',
        'path',
        'order_number',
        'is_featured',
    ];

    protected $casts = [
        'fk_product_id' => 'integer',
        'order_number' => 'integer',
        'is_featured' => 'boolean',
    ];

    /**
     * Boot method to auto-enforce only one featured image per product
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($image) {
            // If this image is being set as featured, unset all other featured images for this product
            if ($image->is_featured) {
                static::where('fk_product_id', $image->fk_product_id)
                    ->where('id', '!=', $image->id)
                    ->update(['is_featured' => false]);
            }
        });
    }

    public function fk_product()
    {
        return $this->belongsTo(Product::class, 'fk_product_id', 'id');
    }
}

