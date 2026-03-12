<?php

namespace App\Models;

use App\Enums\DocumentTypeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    /** @use HasFactory<\Database\Factories\PatientFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'name',
        'document_type',
        'document_number',
        'admission_date',
        'birthday',
        'phone',
        'nursing_report',
    ];

    protected $casts = [
        'admission_date' => 'date',
        'birthday' => 'date',
        'document_type' => DocumentTypeEnum::class,
        'nursing_report' => 'array',
    ];

    public function responsibles(): BelongsToMany
    {
        return $this->belongsToMany(Responsible::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }
}
