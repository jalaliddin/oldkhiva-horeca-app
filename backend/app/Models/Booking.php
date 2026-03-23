<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_number',
        'client_id',
        'event_date',
        'event_time',
        'guest_count',
        'notes',
        'status',
        'admin_notes',
        'approved_by',
        'approved_at',
        'total_amount',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'approved_at' => 'datetime',
            'total_amount' => 'decimal:2',
            'guest_count' => 'integer',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model): void {
            $year = date('Y');
            $count = static::whereYear('created_at', $year)->count() + 1;
            $model->booking_number = sprintf('BRN-%s-%04d', $year, $count);
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
