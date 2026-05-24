<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'variant_id', 'qty', 'note', 'store_id', 'is_protected'];
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
