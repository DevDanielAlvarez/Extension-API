<?php

namespace App\Models;

use App\Enums\ContentUnitEnum;
use App\Enums\RouteOfAdministrationEnum;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medicine extends Model
{
    /** @use HasFactory<\Database\Factories\MedicineFactory> */
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'name',
        'content_quantity',
        'content_unit',
        'strength',
        'is_compounded',
        'route_of_administration',
        'additional_information',
    ];

    protected $casts = [
        'content_unit' => ContentUnitEnum::class,
        'route_of_administration' => RouteOfAdministrationEnum::class,
        'is_compounded' => 'boolean',
    ];

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (self $medicine): void {
            if ($medicine->isForceDeleting()) {
                $medicine->prescriptions()->withTrashed()->get()->each->forceDelete();

                return;
            }

            $medicine->prescriptions()->delete();
        });

        static::restoring(function (self $medicine): void {
            $medicine->prescriptions()->onlyTrashed()->get()->each->restore();
        });
    }
}
