<?php

use App\Models\MedicationAdministration;
use App\Models\PrescriptionSchedule;
use Illuminate\Database\QueryException;

describe('MedicationAdministration', function () {
    it('relates back to its prescription schedule', function () {
        $schedule = PrescriptionSchedule::factory()->create();

        $administration = MedicationAdministration::factory()->create([
            'prescription_schedule_id' => $schedule->id,
            'scheduled_date' => today(),
        ]);

        expect($schedule->medicationAdministrations()->first()->id)->toBe($administration->id);
        expect($administration->prescriptionSchedule->id)->toBe($schedule->id);
    });

    it('prevents two administrations for the same schedule and date', function () {
        $schedule = PrescriptionSchedule::factory()->create();

        MedicationAdministration::factory()->create([
            'prescription_schedule_id' => $schedule->id,
            'scheduled_date' => today(),
        ]);

        expect(fn () => MedicationAdministration::factory()->create([
            'prescription_schedule_id' => $schedule->id,
            'scheduled_date' => today(),
        ]))->toThrow(QueryException::class);
    });

    it('updateOrCreate marks the same occurrence as applied without duplicating', function () {
        $schedule = PrescriptionSchedule::factory()->create();

        MedicationAdministration::updateOrCreate(
            ['prescription_schedule_id' => $schedule->id, 'scheduled_date' => today()],
            ['applied_at' => now()]
        );

        MedicationAdministration::updateOrCreate(
            ['prescription_schedule_id' => $schedule->id, 'scheduled_date' => today()],
            ['applied_at' => now()]
        );

        expect(MedicationAdministration::where('prescription_schedule_id', $schedule->id)->count())->toBe(1);
    });
});
