<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserShippingAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'email',
        'label_place',
        'address',
        'city',
        'province',
        'postal_code',
        'note_address',
        'is_primary',
        'province_id',
        'province_label',
        'city_id',
        'city_label',
        'district_id',
        'district_label',
        'sub_district_id',
        'sub_district_label',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Get the user that owns the shipping address.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
