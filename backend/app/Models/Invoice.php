<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'booking_id',
        'client_id',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'balance',
        'due_date',
        'status',
        'file_path',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model): void {
            $year = date('Y');
            $count = static::whereYear('created_at', $year)->count() + 1;
            $model->invoice_number = sprintf('INV-%s-%04d', $year, $count);
        });
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
