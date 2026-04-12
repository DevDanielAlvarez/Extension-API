<?php

namespace App\Services\User;

use App\DTO\User\CreateUserDTO;
use App\DTO\User\UpdateUserDTO;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserService
{
    public function __construct(
        protected Model $record
    ) {
    }

    public function getRecord(): Model
    {
        return $this->record;
    }

    public static function create(CreateUserDTO $dtoToCreate): static
    {
        try {
            return new self(User::create($dtoToCreate->toArray()));
        } catch (\Exception $e) {
            // Handle the exception or log it
            throw $e;
        }
    }

    public static function find(string $id): static
    {
        $user = User::findOrFail($id);
        return new self($user);
    }

    public function update(UpdateUserDTO $dtoToUpdate): static
    {
        $this->record->update($dtoToUpdate->toArray());
        return $this;
    }

    public function delete(): void
    {
        $this->record->delete();
    }
}
