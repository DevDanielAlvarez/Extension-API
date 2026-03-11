<?php

namespace App\Services\Responsible;

use App\DTO\Responsible\CreateResponsibleDTO;
use App\DTO\Responsible\UpdateResponsibleDTO;
use App\Models\Responsible;
use Illuminate\Database\Eloquent\Model;

class ResponsibleService
{
    public function __construct(
        protected Model $record
    ) {
    }

    public function getRecord(): Model
    {
        return $this->record;
    }

    public static function create(CreateResponsibleDTO $dtoToCreate): static
    {
        return new self(Responsible::create($dtoToCreate->toArray()));
    }

    public static function find(string $id): static
    {
        $responsible = Responsible::findOrFail($id);

        return new self($responsible);
    }

    public function update(UpdateResponsibleDTO $dtoToUpdate): static
    {
        $this->record->update($dtoToUpdate->toArray());

        return $this;
    }

    public function delete(): void
    {
        $this->record->delete();
    }
}
