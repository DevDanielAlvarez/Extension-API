<?php

namespace App\Services\MedicationAdministration;

use App\DTO\PatientMedicineMovement\CreatePatientMedicineMovementDTO;
use App\Enums\StockMovementTypeEnum;
use App\Models\MedicationAdministration;
use App\Models\PatientMedicine;
use App\Models\PrescriptionSchedule;
use App\Services\PatientMedicineMovement\PatientMedicineMovementService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MedicationAdministrationService
{
    /**
     * Marks a dose as applied and consumes it from the patient's own medicine
     * balance (PatientMedicine). Throws a ValidationException (via
     * PatientMedicineService::setBalance) if the patient has no balance left
     * for that medicine.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public static function markApplied(PrescriptionSchedule $schedule, Carbon $scheduledDate, ?string $userId): MedicationAdministration
    {
        return DB::transaction(function () use ($schedule, $scheduledDate, $userId) {
            $prescription = $schedule->prescription;

            $patientMedicine = PatientMedicine::firstOrCreate(
                ['patient_id' => $prescription->patient_id, 'medicine_id' => $prescription->medicine_id],
                ['current_quantity' => 0]
            );

            $administration = MedicationAdministration::updateOrCreate(
                ['prescription_schedule_id' => $schedule->id, 'scheduled_date' => $scheduledDate->toDateString()],
                ['applied_at' => now(), 'applied_by_user_id' => $userId],
            );

            PatientMedicineMovementService::create(new CreatePatientMedicineMovementDTO(
                patient_medicine_id: $patientMedicine->id,
                type: StockMovementTypeEnum::OUT,
                quantity: $schedule->quantity,
                user_id: $userId,
                notes: null,
                movement_date: now(),
                medication_administration_id: $administration->id,
            ));

            return $administration;
        });
    }

    /**
     * Undoes a dose application, restoring the consumed quantity back to the
     * patient's medicine balance.
     */
    public static function undoApplied(PrescriptionSchedule $schedule, Carbon $scheduledDate, ?string $userId): void
    {
        DB::transaction(function () use ($schedule, $scheduledDate, $userId) {
            $administration = MedicationAdministration::where('prescription_schedule_id', $schedule->id)
                ->where('scheduled_date', $scheduledDate->toDateString())
                ->first();

            if (! $administration) {
                return;
            }

            $prescription = $schedule->prescription;

            $patientMedicine = PatientMedicine::where('patient_id', $prescription->patient_id)
                ->where('medicine_id', $prescription->medicine_id)
                ->first();

            if ($patientMedicine) {
                PatientMedicineMovementService::create(new CreatePatientMedicineMovementDTO(
                    patient_medicine_id: $patientMedicine->id,
                    type: StockMovementTypeEnum::RETURNED,
                    quantity: $schedule->quantity,
                    user_id: $userId,
                    notes: 'Estorno: aplicação desfeita.',
                    movement_date: now(),
                    medication_administration_id: $administration->id,
                ));
            }

            $administration->delete();
        });
    }
}
