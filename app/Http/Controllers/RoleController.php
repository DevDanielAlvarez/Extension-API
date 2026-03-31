<?php

namespace App\Http\Controllers;

use App\Enums\PermissionScreenEnum;
use App\DTO\Role\CreateRoleDTO;
use App\DTO\Role\UpdateRoleDTO;
use App\Http\Requests\Role\CreateRoleFormRequest;
use App\Http\Requests\Role\UpdateRoleFormRequest;
use App\Http\Resources\UserResource;
use App\Models\Permission;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Services\Role\RoleService;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        return RoleResource::collection(Role::paginate(10));
    }

    public function store(CreateRoleFormRequest $request)
    {
        $validatedData = $request->validated();

        $result = DB::transaction(function () use ($validatedData) {
            $dtoToCreate = new CreateRoleDTO(
                name: $validatedData['name'],
            );

            $roleService = RoleService::create($dtoToCreate);

            return new RoleResource($roleService->getRecord());
        });

        return $result->response()->setStatusCode(201);
    }

    public function show(string $role)
    {
        $roleService = RoleService::find($role);

        return RoleResource::make($roleService->getRecord());
    }

    public function update(UpdateRoleFormRequest $request, string $role)
    {
        $validatedData = $request->validated();

        $result = DB::transaction(function () use ($validatedData, $role) {
            $dtoToUpdate = new UpdateRoleDTO(
                id: $role,
                name: $validatedData['name'],
            );

            $roleService = RoleService::find($role);
            $roleService->update($dtoToUpdate);

            return RoleResource::make($roleService->getRecord());
        });

        return $result;
    }

    public function destroy(string $role)
    {
        DB::transaction(function () use ($role) {
            $roleService = RoleService::find($role);
            $roleService->delete();
        });

        return response()->noContent();
    }

    public function users(string $role)
    {
        $roleService = RoleService::find($role);

        return UserResource::collection(
            $roleService->getRecord()->users()->paginate(10)
        );
    }

    public function attachUser(string $role, string $user)
    {
        $roleService = RoleService::find($role);
        UserService::find($user);

        $roleService->getRecord()->users()->syncWithoutDetaching([$user]);

        return response()->noContent();
    }

    public function detachUser(string $role, string $user)
    {
        $roleService = RoleService::find($role);
        UserService::find($user);

        $roleService->getRecord()->users()->detach($user);

        return response()->noContent();
    }

    public function permissionsByScreen(Request $request, string $role)
    {
        $validated = $request->validate([
            'screen' => ['required', 'string', Rule::in(array_column(PermissionScreenEnum::cases(), 'value'))],
        ]);

        $roleRecord = RoleService::find($role)->getRecord();
        $available = Permission::query()
            ->where('screen', $validated['screen'])
            ->get(['id', 'name', 'screen']);

        $selected = $roleRecord->permissions()
            ->where('screen', $validated['screen'])
            ->pluck('name')
            ->toArray();

        return response()->json([
            'role_id' => $roleRecord->id,
            'screen' => $validated['screen'],
            'available_permissions' => $available,
            'selected_permissions' => $selected,
        ]);
    }

    public function syncPermissionsByScreen(Request $request, string $role)
    {
        $validated = $request->validate([
            'screen' => ['required', 'string', Rule::in(array_column(PermissionScreenEnum::cases(), 'value'))],
            'permissions' => ['array'],
            'permissions.*' => ['string'],
        ]);

        $roleRecord = RoleService::find($role)->getRecord();

        $currentScreenPermissionIds = Permission::query()
            ->where('screen', $validated['screen'])
            ->whereIn('name', $validated['permissions'] ?? [])
            ->pluck('id')
            ->toArray();

        $otherScreenPermissionIds = $roleRecord->permissions()
            ->where('screen', '!=', $validated['screen'])
            ->pluck('permissions.id')
            ->toArray();

        $roleRecord->permissions()->sync([
            ...$otherScreenPermissionIds,
            ...$currentScreenPermissionIds,
        ]);

        return response()->noContent();
    }

    public function activateAllPermissionsByScreen(Request $request, string $role)
    {
        $validated = $request->validate([
            'screen' => ['required', 'string', Rule::in(array_column(PermissionScreenEnum::cases(), 'value'))],
        ]);

        $permissions = Permission::query()
            ->where('screen', $validated['screen'])
            ->pluck('name')
            ->toArray();

        $request->merge(['permissions' => $permissions]);

        return $this->syncPermissionsByScreen($request, $role);
    }

    public function disableAllPermissionsByScreen(Request $request, string $role)
    {
        $validated = $request->validate([
            'screen' => ['required', 'string', Rule::in(array_column(PermissionScreenEnum::cases(), 'value'))],
        ]);

        $request->merge([
            'screen' => $validated['screen'],
            'permissions' => [],
        ]);

        return $this->syncPermissionsByScreen($request, $role);
    }
}
