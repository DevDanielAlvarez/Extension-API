<?php

namespace App\Http\Controllers;

use App\Enums\PermissionScreenEnum;
use App\Models\Permission;

class PermissionController extends Controller
{
    public function screens()
    {
        return response()->json(
            collect(PermissionScreenEnum::cases())
                ->map(fn (PermissionScreenEnum $screen): array => [
                    'value' => $screen->value,
                    'label' => $screen->label(),
                ])
                ->values()
                ->toArray()
        );
    }

    public function grouped()
    {
        return response()->json(
            Permission::query()
                ->select(['id', 'name', 'screen'])
                ->get()
                ->groupBy('screen')
                ->toArray()
        );
    }
}
