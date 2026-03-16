<?php

namespace App\Services\Patient;

use App\DTO\Patient\CreatePatientDTO;
use App\DTO\Patient\UpdatePatientDTO;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Model;

class PatientService
{
    public function __construct(
        protected Model $record
    ) {
    }

    public function getRecord(): Model
    {
        return $this->record;
    }

    public static function create(CreatePatientDTO $dtoToCreate): static
    {
        return new self(Patient::create($dtoToCreate->toArray()));
    }

    public static function find(string $id): static
    {
        $patient = Patient::findOrFail($id);

        return new self($patient);
    }

    public function update(UpdatePatientDTO $dtoToUpdate): static
    {
        $this->record->update($dtoToUpdate->toArray());

        return $this;
    }

    public function delete(): void
    {
        $this->record->delete();
    }

    public function attachResponsible(string $responsibleId): self
    {
        $this->record->responsibles()->attach($responsibleId);
        return $this;
    }
}
