<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $table = 'vouchers';

    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
        'limit_user',
        'voucher_used',
        'start_date',
        'end_date',
        'discount_type',
        'discount_value',
        'min_purchase',
        'is_published',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'discount_value' => 'decimal:2',
        'voucher_used' => 'integer',
        'is_published' => 'boolean',
        'min_purchase'   => 'integer',
    ];

    public static function updateStatus()
    {
        Voucher::where('status', 'ACTIVE')
            ->whereNotNull('start_date')
            ->where('start_date', '>', now())
            ->update(['status' => 'INACTIVE']);

        Voucher::where('status', 'ACTIVE')
            ->whereNotNull('end_date')
            ->where('end_date', '<', now())
            ->update(['status' => 'INACTIVE']);

        Voucher::where('status', 'INACTIVE')
            ->whereNotNull('start_date')
            ->where('start_date', '<=', now())
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->update(['status' => 'ACTIVE']);
    }

    public function categories()
    {
        return $this->belongsToMany(
            TaxoList::class,
            'voucher_category',
            'voucher_id',
            'category_id'
        );
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUnpublished($query)
    {
        return $query->where('is_published', false);
    }

    public function scopeAvailableForFrontend($query)
    {
        return $query->with('categories')
            ->where('status', 'ACTIVE')
            ->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->where('start_date', '<=', now());
    }
}
