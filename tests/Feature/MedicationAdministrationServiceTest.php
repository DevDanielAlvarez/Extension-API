<?php

use App\Enums\StockMovementTypeEnum;
use App\Models\MedicationAdministration;
use App\Models\PatientMedicine;
use App\Models\PrescriptionSchedule;
use App\Services\MedicationAdministration\MedicationAdministrationService;
use Illuminate\Validation\ValidationException;

describe('MedicationAdministrationService::markApplied', function () {
    it('consumes the dose quantity from the patient medicine balance', function () {
        $schedule = PrescriptionSchedule::factory()->create(['quantity' => 2]);
        $prescription = $schedule->prescription;

        PatientMedicine::factory()->create([
            'patient_id' => $prescription->patient_id,
            'medicine_id' => $prescription->medicine_id,
            'current_quantity' => 10,
        ]);

        $administration = MedicationAdministrationService::markApplied($schedule, today(), null);

        expect($administration)->toBeInstanceOf(MedicationAdministration::class);

        $patientMedicine = PatientMedicine::where('patient_id', $prescription->patient_id)
            ->where('medicine_id', $prescription->medicine_id)
            ->first();

        expect($patientMedicine->current_quantity)->toBe(8);

        $this->assertDatabaseHas('patient_medicine_movements', [
            'patient_medicine_id' => $patientMedicine->id,
            'type' => StockMovementTypeEnum::OUT->value,
            'quantity' => 2,
            'medication_administration_id' => $administration->id,
        ]);
    });

    it('rejects the application when there is no stock, rolling back everything', function () {
        $schedule = PrescriptionSchedule::factory()->create(['quantity' => 1]);

        expect(fn () => MedicationAdministrationService::markApplied($schedule, today(), null))
            ->toThrow(ValidationException::class);

        $prescription = $schedule->prescription;

        // The whole markApplied() runs in one transaction: since the balance
        // guard fails, even the zeroed PatientMedicine created along the way
        // is rolled back — no partial state is left behind.
        $this->assertDatabaseMissing('patient_medicines', [
            'patient_id' => $prescription->patient_id,
            'medicine_id' => $prescription->medicine_id,
        ]);

        $this->assertDatabaseMissing('medication_administrations', [
            'prescription_schedule_id' => $schedule->id,
        ]);
    });
});

describe('MedicationAdministrationService::undoApplied', function () {
    it('restores the balance and removes the administration', function () {
        $schedule = PrescriptionSchedule::factory()->create(['quantity' => 3]);
        $prescription = $schedule->prescription;

        PatientMedicine::factory()->create([
            'patient_id' => $prescription->patient_id,
            'medicine_id' => $prescription->medicine_id,
            'current_quantity' => 10,
        ]);

        MedicationAdministrationService::markApplied($schedule, today(), null);
        MedicationAdministrationService::undoApplied($schedule, today(), null);

        $patientMedicine = PatientMedicine::where('patient_id', $prescription->patient_id)
            ->where('medicine_id', $prescription->medicine_id)
            ->first();

        expect($patientMedicine->current_quantity)->toBe(10);

        $this->assertDatabaseMissing('medication_administrations', [
            'prescription_schedule_id' => $schedule->id,
            'scheduled_date' => today()->toDateString(),
        ]);
    });
});
