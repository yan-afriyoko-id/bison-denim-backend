<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orders';

    protected $fillable = [
        'uuid',
        'queue_number',
        'order_number',
        'contact_email',
        'contact_phone',
        'shipping_country',
        'shipping_first_name',
        'shipping_last_name',
        'shipping_address',
        'shipping_city',
        'shipping_province',
        'shipping_postal_code',
        'shipping_label_place',
        'shipping_note_address',
        'billing_country',
        'billing_first_name',
        'billing_last_name',
        'billing_address',
        'billing_city',
        'billing_province',
        'billing_postal_code',
        'billing_label_place',
        'billing_note_address',
        'courier_agent',
        'courier_agent_service',
        'courier_agent_service_desc',
        'courier_estimate_delivered',
        'courier_resi_number',
        'courier_cost',
        'payment_method',
        'payment_reference_code',
        'payment_snap_token',
        'payment_status',
        'invoice_note',
        'delivery_order_note',
        'subtotal',
        'discount_amount',
        'points_used',
        'shipping_cost',
        'total_amount',
        'fk_user_id',
        'fk_voucher_id',
        'status',
    ];

    protected $casts = [
        'queue_number' => 'integer',
        'courier_cost' => 'integer',
        'subtotal' => 'integer',
        'discount_amount' => 'integer',
        'points_used' => 'integer',
        'shipping_cost' => 'integer',
        'total_amount' => 'integer',
        'fk_user_id' => 'integer',
        'fk_voucher_id' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->uuid)) {
                $order->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'fk_user_id', 'id');
    }

    /**
     * Relationship to Voucher (if exists)
     */
    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'fk_voucher_id', 'id');
    }

    /**
     * Relationship to OrderItems
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'fk_order_id', 'id');
    }

    /**
     * Get latest queue number for current month
     */
    public static function getLatestQueueNumberForMonth()
    {
        return static::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->max('queue_number') ?? 0;
    }

    /**
     * Generate order number
     */
    public static function generateOrderNumber()
    {
        $date = now()->format('Ymd');
        $queueNumber = static::getLatestQueueNumberForMonth() + 1;
        $queueNumberPadded = str_pad($queueNumber, 4, '0', STR_PAD_LEFT);

        return "ORD-{$date}-{$queueNumberPadded}";
    }

    public function getProductProtectionAmountAttribute(): int
    {
        if (!$this->relationLoaded('orderItems')) {
            return $this->orderItems()->sum('product_protection_amount');
        }

        return $this->orderItems->sum('product_protection_amount');
    }
}
