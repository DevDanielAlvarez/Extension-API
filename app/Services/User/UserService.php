<?php

namespace App\Services\User;

use App\DTO\User\CreateUserDTO;
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
}
