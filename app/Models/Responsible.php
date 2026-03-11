<?php

namespace App\Models;

use App\Enums\DocumentTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Responsible extends Model
{
    /** @use HasFactory<\Database\Factories\ResponsibleFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'document_type',
        'document_number',
        'phone',
    ];

    protected $casts = [
        'document_type' => DocumentTypeEnum::class,
    ];
}
