<?php

namespace App\Policies\Traits;

use App\Models\Permission;
use App\Models\User;

trait CheckPermissionTrait
{
    protected static array $permissionCache = [];

    protected static function check(User $user, string $action, string $screen): bool
    {
        if (auth()->user()->is_adm) {
            return true; // Admin users bypass permission checks
        }
        $key = "$screen:$action";

        if (! isset(self::$permissionCache[$key])) {
            self::$permissionCache[$key] = Permission::where('name', $action)
                ->where('screen', $screen)
                ->first();
        }

        $permission = self::$permissionCache[$key];

        return $permission ? $user->hasPermission($permission) : false;
    }
}