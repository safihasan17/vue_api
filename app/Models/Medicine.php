<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'generic_name',
    'category',
    'unit_price',
    'stock_quantity',
])]
class Medicine extends Model
{
    /** @use HasFactory<\Database\Factories\MedicineFactory> */
    use HasFactory;

    /** Stock below this number is treated as "low stock". */
    public const LOW_STOCK_THRESHOLD = 10;

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
        ];
    }

    public function prescriptionMedicines(): HasMany
    {
        return $this->hasMany(PrescriptionMedicine::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(MedicinePurchase::class);
    }

    /** Out of Stock | Low Stock | In Stock */
    public function getStockStatusAttribute(): string
    {
        if ($this->stock_quantity <= 0) {
            return 'Out of Stock';
        }

        return $this->stock_quantity < self::LOW_STOCK_THRESHOLD ? 'Low Stock' : 'In Stock';
    }

    public function getStockColorAttribute(): string
    {
        return match ($this->stock_status) {
            'Out of Stock' => 'danger',
            'Low Stock' => 'warning',
            default => 'success',
        };
    }
}
