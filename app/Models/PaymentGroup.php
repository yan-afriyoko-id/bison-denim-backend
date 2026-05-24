<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGroup extends Model
{
    use HasFactory;

    protected $table = 'payment_groups';

    protected $fillable = [
        'uuid',
        'group_number',
        'gross_amount',
        'payment_snap_token',
        'status',
        'fk_user_id',
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'payment_group_order', 'payment_group_id', 'order_id');
    }
}
