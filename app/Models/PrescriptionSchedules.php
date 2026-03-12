<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrescriptionSchedules extends Model
{
    /** @use HasFactory<\Database\Factories\PrescriptionSchedulesFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'prescription_id',
        'day_of_week',
        'time',
        'quantity',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'quantity' => 'integer',
    ];

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }
}
