<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'sort',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function attributeValues()
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id', 'id')
            ->where('status', 'ACTIVE')
            ->orderBy('sort', 'asc');
    }

    public function allAttributeValues()
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id', 'id')
            ->orderBy('sort', 'asc');
    }
}

