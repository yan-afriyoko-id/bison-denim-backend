<?php

namespace App\Models;

use App\Notifications\Auth\ResetPasswordNotification;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles, CanResetPassword;

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get the shipping addresses for the user.
     */
    public function shippingAddresses()
    {
        return $this->hasMany(UserShippingAddress::class);
    }

    /**
     * Get the user's point record.
     */
    public function userPoint()
    {
        return $this->hasOne(UserPoint::class);
    }

    /**
     * Get the user's point transactions.
     */
    public function pointTransactions()
    {
        return $this->hasMany(UserPointTransaction::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'avatar',
        'name',
        'last_name',
        'phone',
        'email',
        'dob',
        'gender',
        'password',
        'erp_remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'erp_remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'dob' => 'date',
        ];
    }

    /**
     * Check if user email is verified.
     *
     * @return bool
     */
    public function hasVerifiedEmail(): bool
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Mark email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Check if user phone is verified.
     *
     * @return bool
     */
    public function hasVerifiedPhone(): bool
    {
        return $this->phone_verified_at !== null;
    }

    /**
     * Mark phone as verified.
     *
     * @return bool
     */
    public function markPhoneAsVerified(): bool
    {
        return $this->forceFill([
            'phone_verified_at' => $this->freshTimestamp(),
        ])->save();
    }
}
