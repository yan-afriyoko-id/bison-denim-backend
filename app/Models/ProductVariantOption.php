<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'variant_id',
        
        'attribute_id', 
        'attribute_value_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'variant_id' => 'integer',
        'attribute_id' => 'integer',
        'attribute_value_id' => 'integer',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id', 'id');
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id', 'id');
    }

    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class, 'attribute_value_id', 'id');
    }
}

