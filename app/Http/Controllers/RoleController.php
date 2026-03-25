<?php

namespace App\Http\Controllers;

use App\DTO\Role\CreateRoleDTO;
use App\DTO\Role\UpdateRoleDTO;
use App\Http\Requests\Role\CreateRoleFormRequest;
use App\Http\Requests\Role\UpdateRoleFormRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Services\Role\RoleService;
use Illuminate\Support\Facades\DB;

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
}
