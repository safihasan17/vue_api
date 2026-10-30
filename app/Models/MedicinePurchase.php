<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'supplier_id',
    'medicine_id',
    'quantity',
    'purchase_price',
    'purchase_date',
])]
class MedicinePurchase extends Model
{
    /** @use HasFactory<\Database\Factories\MedicinePurchaseFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'purchase_date' => 'date',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    /** quantity x purchase_price */
    public function getTotalCostAttribute(): float
    {
        return (float) $this->purchase_price * (int) $this->quantity;
    }
}
