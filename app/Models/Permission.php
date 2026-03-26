<?php

namespace App\Models;

use App\Enums\PermissionScreenEnum;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    /** @use HasFactory<\Database\Factories\PermissionFactory> */
    use HasFactory,HasUlids;

    protected $casts = [
        'screen' => PermissionScreenEnum::class,
    ];
}
