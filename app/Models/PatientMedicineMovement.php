<?php

namespace App\Models;

use App\Enums\StockMovementTypeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientMedicineMovement extends Model
{
    /** @use HasFactory<\Database\Factories\PatientMedicineMovementFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'patient_medicine_id',
        'type',
        'quantity',
        'medication_administration_id',
        'user_id',
        'notes',
        'movement_date',
    ];

    protected $casts = [
        'type' => StockMovementTypeEnum::class,
        'quantity' => 'integer',
        'movement_date' => 'datetime',
    ];

    public function patientMedicine(): BelongsTo
    {
        return $this->belongsTo(PatientMedicine::class);
    }

    public function medicationAdministration(): BelongsTo
    {
        return $this->belongsTo(MedicationAdministration::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
