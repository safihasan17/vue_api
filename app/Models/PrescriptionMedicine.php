<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'prescription_id',
    'medicine_id',
    'dosage',
    'duration',
    'instructions',
])]
class PrescriptionMedicine extends Model
{
    /** @use HasFactory<\Database\Factories\PrescriptionMedicineFactory> */
    use HasFactory;

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }
}