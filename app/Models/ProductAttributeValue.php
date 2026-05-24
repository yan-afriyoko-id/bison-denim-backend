<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_attribute_id',
        'attribute_value_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'product_attribute_id' => 'integer',
        'attribute_value_id' => 'integer',
    ];

    public function productAttribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id', 'id');
    }

    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class, 'attribute_value_id', 'id');
    }
}

