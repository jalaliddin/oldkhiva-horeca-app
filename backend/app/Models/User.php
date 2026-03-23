<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'company_name',
        'inn',
        'mfo',
        'bank_account',
        'bank_name',
        'director_name',
        'phone',
        'address',
        'logo',
        'contract_agreed',
        'approved_at',
        'approved_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'approved_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'contract_agreed' => 'boolean',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'client_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'client_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'client_id');
    }

    public function deposit(): HasOne
    {
        return $this->hasOne(Deposit::class, 'client_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
