<?php

namespace App\Models;

use App\Enums\DocumentTypeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Responsible extends Model
{
    /** @use HasFactory<\Database\Factories\ResponsibleFactory> */
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'name',
        'document_type',
        'document_number',
        'phone',
    ];

    protected $casts = [
        'document_type' => DocumentTypeEnum::class,
    ];

    public function patients(): BelongsToMany
    {
        return $this->belongsToMany(Patient::class);
    }
}
