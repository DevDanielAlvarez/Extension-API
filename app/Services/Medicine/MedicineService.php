<?php

namespace App\Services\Medicine;

use App\DTO\Medicine\CreateMedicineDTO;
use App\DTO\Medicine\UpdateMedicineDTO;
use App\Models\Medicine;
use Illuminate\Database\Eloquent\Model;

class MedicineService
{
    public function __construct(
        protected Model $record
    ) {
    }

    public function getRecord(): Model
    {
        return $this->record;
    }

    public static function create(CreateMedicineDTO $dtoToCreate): static
    {
        return new self(Medicine::create($dtoToCreate->toArray()));
    }

    public static function find(string $id): static
    {
        $medicine = Medicine::findOrFail($id);

        return new self($medicine);
    }

    public function update(UpdateMedicineDTO $dtoToUpdate): static
    {
        $this->record->update($dtoToUpdate->toArray());

        return $this;
    }
}
