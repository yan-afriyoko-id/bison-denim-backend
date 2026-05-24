<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCollection extends Model
{
    use HasFactory;

    protected $table = 'product_collections';

    protected $fillable = [
        'fk_product_id',
        'fk_collection_id', 
    ];

    public function fk_product()
    {
        return $this->belongsTo(Product::class, 'fk_product_id', 'id');
    }

    public function fk_collection()
    {
        return $this->belongsTo(TaxoList::class, 'fk_collection_id', 'id')->where('taxonomy_type', 1);
    }
}

