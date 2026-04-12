<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    /** @use HasFactory<\Database\Factories\PrescriptionFactory> */
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'medicine_id',
        'start_date',
        'end_date',
        'instructions',
    ];
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    public function prescriptionSchedules(): HasMany
    {
        return $this->hasMany(PrescriptionSchedule::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (self $prescription): void {
            if ($prescription->isForceDeleting()) {
                $prescription->prescriptionSchedules()->withTrashed()->get()->each->forceDelete();

                return;
            }

            $prescription->prescriptionSchedules()->delete();
        });

        static::restoring(function (self $prescription): void {
            $prescription->prescriptionSchedules()->onlyTrashed()->get()->each->restore();
        });
    }
}
