<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandProduct extends Model
{
    use HasFactory;

    protected $table = 'brand_products';

    protected $fillable = [
        'fk_brand_id',
        'fk_product_id',
    ];

    protected $casts = [
        'fk_brand_id' => 'integer',
        'fk_product_id' => 'integer',
    ];

    public function fk_product()
    {
        return $this->belongsTo(Product::class, 'fk_product_id', 'id');
    }

    public function fk_brand()
    {
        return $this->belongsTo(Brand::class, 'fk_brand_id', 'id');
    }
}
