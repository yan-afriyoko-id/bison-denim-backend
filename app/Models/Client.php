<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_client',
        'name',
        'phone',
        'address',
    ];

    /**
     * Generate next ID_Client (Java_001, Java_002, etc.)
     */
    public static function generateIdClient(): string
    {
        $lastClient = self::orderBy('id', 'desc')->first();
        $nextNumber = $lastClient ? (int) substr($lastClient->id_client, strrpos($lastClient->id_client, '_') + 1) + 1 : 1;
        return 'Java_' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}

