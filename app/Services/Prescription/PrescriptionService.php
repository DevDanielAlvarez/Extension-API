<?php

namespace App\Services\Prescription;

use App\DTO\Prescription\CreatePrescriptionDTO;
use App\DTO\Prescription\UpdatePrescriptionDTO;
use App\Models\Prescription;
use Illuminate\Database\Eloquent\Model;

class PrescriptionService
{
    public function __construct(
        protected Model $record
    ) {
    }

    public function getRecord(): Model
    {
        return $this->record;
    }

    public static function create(CreatePrescriptionDTO $dtoToCreate): static
    {
        return new self(Prescription::create($dtoToCreate->toArray()));
    }

    public static function find(string $id): static
    {
        $prescription = Prescription::findOrFail($id);

        return new self($prescription);
    }

    public function update(UpdatePrescriptionDTO $dtoToUpdate): static
    {
        $this->record->update($dtoToUpdate->toArray());

        return $this;
    }

    public function delete(): void
    {
        $this->record->delete();
    }
}