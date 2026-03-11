<?php

namespace App\Models;

use App\Enums\DocumentTypeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
