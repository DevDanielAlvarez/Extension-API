<?php

namespace App\Http\Controllers;

use App\Enums\PermissionScreenEnum;
use App\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        return response()->json(Permission::paginate(10));
    }

    public function show(string $permission)
    {
        return response()->json(Permission::findOrFail($permission));
    }

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

    public function destroy(string $permission)
    {
        $record = Permission::findOrFail($permission);
        $record->delete();

        return response()->noContent();
    }

    public function trashed()
    {
        return response()->json(Permission::onlyTrashed()->paginate(10));
    }

    public function restore(string $permission)
    {
        $record = Permission::onlyTrashed()->findOrFail($permission);
        $record->restore();

        return response()->json($record->fresh());
    }

    public function forceDelete(string $permission)
    {
        $record = Permission::withTrashed()->findOrFail($permission);
        $record->forceDelete();

        return response()->noContent();
    }
}
