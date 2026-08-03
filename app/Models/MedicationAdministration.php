<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicationAdministration extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'prescription_schedule_id',
        'scheduled_date',
        'applied_at',
        'applied_by_user_id',
    ];

    protected $casts = [
        'scheduled_date' => 'date:Y-m-d',
        'applied_at' => 'datetime',
    ];

    public function prescriptionSchedule(): BelongsTo
    {
        return $this->belongsTo(PrescriptionSchedule::class);
    }

    public function appliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applied_by_user_id');
    }
}
