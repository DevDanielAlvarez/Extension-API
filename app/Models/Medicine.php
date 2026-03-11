<?php

namespace App\Models;

use App\Enums\ContentUnitEnum;
use App\Enums\RouteOfAdministrationEnum;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    /** @use HasFactory<\Database\Factories\MedicineFactory> */
    use HasFactory, HasUlids;

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
}
