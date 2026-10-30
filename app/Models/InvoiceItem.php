<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'invoice_id',
    'item_type',
    'item_reference_id',
    'description',
    'amount',
])]
class InvoiceItem extends Model
{
    /** @use HasFactory<\Database\Factories\InvoiceItemFactory> */
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    /**
     * item_type => which Eloquent model that type points to.
     * item_reference_id is looked up in THAT model's table.
     */
    protected static array $referenceModels = [
        'Medicine' => Medicine::class,
        'Lab Test' => LabTest::class,
        'Consultation Fee' => Doctor::class,
        'Bed Charge' => Bed::class,
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Resolve the actual related model this line item points to
     * (e.g. the Medicine or LabTest row), based on item_type.
     * Returns null for 'Other' or if the referenced row no longer exists.
     */
    public function resolveReference(): ?Model
    {
        $modelClass = static::$referenceModels[$this->item_type] ?? null;

        if (! $modelClass || ! $this->item_reference_id) {
            return null;
        }

        return $modelClass::find($this->item_reference_id);
    }
}