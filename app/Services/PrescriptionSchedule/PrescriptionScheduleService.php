<?php

namespace App\Services\PrescriptionSchedule;

use App\DTO\PrescriptionSchedule\CreatePrescriptionScheduleDTO;
use App\DTO\PrescriptionSchedule\UpdatePrescriptionScheduleDTO;
use App\Models\PrescriptionSchedules;
use Illuminate\Database\Eloquent\Model;

class PrescriptionScheduleService
{
    public function __construct(
        protected Model $record
    ) {
    }

    public function getRecord(): Model
    {
        return $this->record;
    }

    public static function create(CreatePrescriptionScheduleDTO $dtoToCreate): static
    {
        return new self(PrescriptionSchedules::create($dtoToCreate->toArray()));
    }

    public static function find(string $id): static
    {
        $schedule = PrescriptionSchedules::findOrFail($id);

        return new self($schedule);
    }

    public function update(UpdatePrescriptionScheduleDTO $dtoToUpdate): static
    {
        $this->record->update($dtoToUpdate->toArray());

        return $this;
    }

    public function delete(): void
    {
        $this->record->delete();
    }
}
