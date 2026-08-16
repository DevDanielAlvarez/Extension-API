<?php

namespace App\Services\PatientMedicine;

use App\DTO\PatientMedicine\CreatePatientMedicineDTO;
use App\DTO\PatientMedicine\UpdatePatientMedicineDTO;
use App\Models\PatientMedicine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class PatientMedicineService
{
    public function __construct(
        protected Model $record
    ) {
    }

    public function getRecord(): Model
    {
        return $this->record;
    }

    public static function create(CreatePatientMedicineDTO $dtoToCreate): static
    {
        return new self(PatientMedicine::create($dtoToCreate->toArray()));
    }

    public static function find(string $id): static
    {
        $patientMedicine = PatientMedicine::findOrFail($id);

        return new self($patientMedicine);
    }

    public function update(UpdatePatientMedicineDTO $dtoToUpdate): static
    {
        $this->record->update($dtoToUpdate->toArray());

        return $this;
    }

    public function delete(): void
    {
        $this->record->delete();
    }

    public function adjustBalance(int $delta): static
    {
        return $this->setBalance($this->record->current_quantity + $delta);
    }

    public function setBalance(int $newQuantity): static
    {
        if ($newQuantity < 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Saldo insuficiente para esta movimentação.',
            ]);
        }

        $this->record->update(['current_quantity' => $newQuantity]);

        return $this;
    }
}
