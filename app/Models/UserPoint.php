<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'points',
        'earned_points',
        'used_points',
        'cumulative_total',
        'is_active',
    ];

    protected $casts = [
        'points' => 'integer',
        'earned_points' => 'integer',
        'used_points' => 'integer',
        'cumulative_total' => 'integer',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(UserPointTransaction::class, 'user_id', 'user_id');
    }
}
