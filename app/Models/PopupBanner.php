<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PopupBanner extends Model
{
    protected $fillable = [
        'title',
        'image',
        'url',
        'start_date',
        'end_date',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function scopeActive($query)
    {
        $now = now();

        return $query
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', $now);
            });
    }
}
