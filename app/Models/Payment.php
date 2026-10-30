<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'invoice_id',
    'amount',
    'payment_method',
    'payment_date',
    'gateway',
    'transaction_id',
    'gateway_val_id',
    'status',
    'received_by',
])]
class Payment extends Model
{
    /** Methods a receptionist can pick when taking payment at the counter. */
    public const METHODS = ['Cash', 'bKash', 'Nagad', 'Rocket', 'Card', 'Bank Transfer'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function isGateway(): bool
    {
        return ! empty($this->gateway);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Success' => 'success',
            'Pending' => 'warning',
            'Failed' => 'danger',
            default => 'secondary',
        };
    }
}
