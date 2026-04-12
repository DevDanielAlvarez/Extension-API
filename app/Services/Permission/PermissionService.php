<?php

namespace App\Services\Permission;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;

class PermissionService
{
    public function __construct(
        protected Model $record
    ) {
    }

    public function getRecord(): Model
    {
        return $this->record;
    }

    public static function find(string $id): static
    {
        $permission = Permission::findOrFail($id);

        return new self($permission);
    }

    public function delete(): void
    {
        $this->record->delete();
    }
}
