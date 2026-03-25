<?php

namespace App\Services\Role;

use App\DTO\Role\CreateRoleDTO;
use App\DTO\Role\UpdateRoleDTO;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;

class RoleService
{
    public function __construct(
        protected Model $record
    ) {
    }

    public function getRecord(): Model
    {
        return $this->record;
    }

    public static function create(CreateRoleDTO $dtoToCreate): static
    {
        return new self(Role::create($dtoToCreate->toArray()));
    }

    public static function find(string $id): static
    {
        $role = Role::findOrFail($id);

        return new self($role);
    }

    public function update(UpdateRoleDTO $dtoToUpdate): static
    {
        $this->record->update($dtoToUpdate->toArray());

        return $this;
    }

    public function delete(): void
    {
        $this->record->delete();
    }
}
