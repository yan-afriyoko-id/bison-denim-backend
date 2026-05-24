<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $table = 'category_products';
    
    protected $fillable = [
        'fk_product_id',
        'fk_category_id', 
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fk_product_id' => 'integer',
        'fk_category_id' => 'integer',
    ];

    public function fk_product()
    {
        return $this->belongsTo(Product::class, 'fk_product_id', 'id');
    }

    public function fk_category()
    {
        return $this->belongsTo(TaxoList::class, 'fk_category_id', 'id');
    }
}

