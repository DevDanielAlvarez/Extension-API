<?php

namespace App\Services\User;

use App\DTO\User\CreateUserDTO;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserService
{
    public function __construct(
        protected Model $record
    ) {}

    public function getRecord(): Model
    {
        return $this->record;
    }

    public static function create(CreateUserDTO $dtoToCreate): static
    {
        return new self(User::create($dtoToCreate->toArray()));
    }

    public static function find(string $id): static
    {
        $user = User::findOrFail($id);
        return new self($user);
    }
}
