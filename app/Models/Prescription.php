<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'patient_id',
    'doctor_id',
    'appointment_id',
    'admission_id',
    'admission_advised',
    'notes',
    'prescription_date',
])]
class Prescription extends Model
{
    /** @use HasFactory<\Database\Factories\PrescriptionFactory> */
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'prescription_date' => 'date',
            'admission_advised' => 'boolean',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * The admission created from this prescription.
     * NULL  => patient was not admitted.
     */
    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }

    /**
     * Raw prescription_medicines rows (with dosage/duration/instructions).
     */
    public function prescriptionMedicines(): HasMany
    {
        return $this->hasMany(PrescriptionMedicine::class);
    }

    /**
     * Medicines attached to this prescription, with pivot details.
     */
    public function medicines(): BelongsToMany
    {
        return $this->belongsToMany(Medicine::class, 'prescription_medicines')
            ->withPivot(['dosage', 'duration', 'instructions'])
            ->withTimestamps();
    }

    /**
     * Lab tests ordered as part of this prescription.
     */
    public function labTestOrders(): HasMany
    {
        return $this->hasMany(LabTestOrder::class);
    }
}