<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class ProductSubGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_group_id',
        'title',
        'sort',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeStatus($query, $status = 'ACTIVE')
    {
        return $query->where('status', $status);
    }

    /**
     * Get the product group this sub-group belongs to
     */
    public function productGroup(): BelongsTo
    {
        return $this->belongsTo(ProductGroup::class);
    }

    /**
     * Get all products in this sub-group
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_sub_group_product',
            'product_sub_group_id',
            'product_id'
        )
            ->withPivot('sort')
            ->orderByPivot('sort')
            ->orderByPivot('created_at');
    }
}
