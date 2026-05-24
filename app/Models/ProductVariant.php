<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product_variants';

    protected $fillable = [
        'fk_product_id',
        'variant_name',
        'sku',
        'image_path',
        'price',
        'strike_price',
        'discount_percent',
        'is_ignore_stock',
        'status',
        'weight',
        'type_weight',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fk_product_id' => 'integer',
        'price' => 'decimal:2',
        'strike_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'is_ignore_stock' => 'boolean',
    ];

    /**
     * Append stock to array/json output.
     */
    protected $appends = ['stock', 'weight_display'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'fk_product_id', 'id');
    }

    public function options()
    {
        return $this->hasMany(ProductVariantOption::class, 'variant_id', 'id');
    }

    /**
     * Relationship to ProductVariantStock (hasMany because one variant can have stock in multiple stores).
     * Use this for eager loading: $variant->load('stockRelations')
     */
    public function stockRelations()
    {
        return $this->hasMany(ProductVariantStock::class, 'variant_id', 'id');
    }

    /**
     * Backward compatibility: hasOne relationship for single stock record.
     * This will return the first stock record if multiple exist.
     * @deprecated Use stockRelations() instead for multi-store support.
     */
    public function stockRelation()
    {
        return $this->hasOne(ProductVariantStock::class, 'variant_id', 'id');
    }

    public function getStockAttribute($value)
    {
        if ($this->relationLoaded('stockRelations')) {
            return $this->stockRelations->sum(function ($stock) {
                return max(0, $stock->qty - $stock->reserved_qty);
            });
        }

        if ($this->relationLoaded('stockRelation') && $this->stockRelation) {
            return max(0, $this->stockRelation->qty - $this->stockRelation->reserved_qty);
        }

        return $this->stockRelations()->get()->sum(function ($stock) {
            return max(0, $stock->qty - $stock->reserved_qty);
        });
    }

    /**
     * Ensure stockRelations is included when model is serialized to JSON/Array.
     * This method is called automatically by Laravel when converting to array.
     */
    protected function getArrayableRelations()
    {
        $relations = parent::getArrayableRelations();

        // If stockRelations is loaded, ensure it's included
        if ($this->relationLoaded('stockRelations')) {
            $relations['stock_relations'] = $this->stockRelations;
        }

        return $relations;
    }

    /**
     * Accessor for image_path to return full URL.
     * Automatically formats image_path with asset() if it's a relative path.
     */
    public function getImagePathAttribute($value)
    {
        if (!$value) {
            return null;
        }

        // If already a full URL (starts with http), return as is
        if (str_starts_with($value, 'http')) {
            return $value;
        }

        // Otherwise, format with asset() to get full URL
        return asset($value);
    }

    public function getWeightDisplayAttribute()
    {
        if (!$this->weight) {
            return null;
        }

        $unit = $this->type_weight ?? 'GRAM';
        return number_format($this->weight, 2) . ' ' . $unit;
    }
}
