<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'patient_id',
    'admission_id',
    'appointment_id',
    'total_amount',
    'paid_amount',
    'status',
    'invoice_date',
])]
class Invoice extends Model
{
    /** @use HasFactory<\Database\Factories\InvoiceFactory> */
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'invoice_date' => 'date',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Line items that make up this invoice.
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Status is always derived from paid vs total (a Cancelled invoice stays Cancelled
     * until someone explicitly sets another status).
     */
    protected static function booted(): void
    {
        static::saving(function (Invoice $invoice) {
            if ($invoice->status === 'Cancelled') {
                return;
            }

            $paid = (float) $invoice->paid_amount;
            $total = (float) $invoice->total_amount;

            $invoice->status = $paid <= 0
                ? 'Unpaid'
                : ($paid >= $total ? 'Paid' : 'Partially Paid');
        });
    }

    /**
     * paid_amount = sum of all successful payments (status follows automatically).
     */
    public function syncPayments(): void
    {
        $this->paid_amount = (float) $this->payments()->where('status', 'Success')->sum('amount');
        $this->save();
    }

    /**
     * Amount still owed on this invoice.
     */
    public function getDueAmountAttribute(): float
    {
        return (float) $this->total_amount - (float) $this->paid_amount;
    }
}