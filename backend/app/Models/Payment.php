<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number',
        'client_id',
        'invoice_id',
        'type',
        'amount',
        'payment_date',
        'payment_method',
        'notes',
        'reference',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model): void {
            $year = date('Y');
            $count = static::whereYear('created_at', $year)->count() + 1;
            $model->payment_number = sprintf('PAY-%s-%04d', $year, $count);
        });

        static::created(function (self $model): void {
            if ($model->type === 'invoice_payment' && $model->invoice_id) {
                $invoice = Invoice::find($model->invoice_id);
                if ($invoice) {
                    $newPaidAmount = $invoice->paid_amount + $model->amount;
                    $newBalance = $invoice->total_amount - $newPaidAmount;
                    $status = 'unpaid';
                    if ($newBalance <= 0) {
                        $status = 'paid';
                    } elseif ($newPaidAmount > 0) {
                        $status = 'partial';
                    }
                    $invoice->update([
                        'paid_amount' => $newPaidAmount,
                        'balance' => max(0, $newBalance),
                        'status' => $status,
                    ]);
                }
            } elseif ($model->type === 'deposit') {
                $deposit = Deposit::firstOrCreate(
                    ['client_id' => $model->client_id],
                    ['balance' => 0]
                );
                $deposit->increment('balance', $model->amount);
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
