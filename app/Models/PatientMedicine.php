<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientMedicine extends Model
{
    /** @use HasFactory<\Database\Factories\PatientMedicineFactory> */
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'medicine_id',
        'current_quantity',
        'minimum_quantity',
    ];

    protected $casts = [
        'current_quantity' => 'integer',
        'minimum_quantity' => 'integer',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(PatientMedicineMovement::class);
    }

    public function isLowStock(): bool
    {
        return $this->minimum_quantity !== null && $this->current_quantity <= $this->minimum_quantity;
    }
}
