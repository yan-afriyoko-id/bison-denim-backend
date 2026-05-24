<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantStock extends Model
{
    use HasFactory;

    protected $table = 'product_variant_stocks';

    protected $fillable = [
        'variant_id',
        'store_id',
        'qty',
        'reserved_qty',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'variant_id' => 'integer',
        'store_id' => 'integer',
        'qty' => 'integer',
        'reserved_qty' => 'integer',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id', 'id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }
}

