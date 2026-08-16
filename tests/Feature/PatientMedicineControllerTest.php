<?php

use App\Models\Medicine;
use App\Models\Patient;
use App\Models\PatientMedicine;

describe('PatientMedicineController', function () {
    describe('Store (POST /api/patient-medicines)', function () {
        it('creates a patient medicine balance with valid data', function () {
            $patient = Patient::factory()->create();
            $medicine = Medicine::factory()->create();

            $payload = [
                'patient_id' => $patient->id,
                'medicine_id' => $medicine->id,
                'current_quantity' => 20,
                'minimum_quantity' => 5,
            ];

            $response = $this->postJson('/api/patient-medicines', $payload);

            $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'patient_id',
                        'medicine_id',
                        'current_quantity',
                        'minimum_quantity',
                    ],
                ]);

            $this->assertDatabaseHas('patient_medicines', [
                'patient_id' => $patient->id,
                'medicine_id' => $medicine->id,
                'current_quantity' => 20,
            ]);
        });

        it('defaults current_quantity to zero when not provided', function () {
            $patient = Patient::factory()->create();
            $medicine = Medicine::factory()->create();

            $response = $this->postJson('/api/patient-medicines', [
                'patient_id' => $patient->id,
                'medicine_id' => $medicine->id,
            ]);

            $response->assertStatus(201)
                ->assertJsonPath('data.current_quantity', 0);
        });

        it('fails with missing required fields', function () {
            $response = $this->postJson('/api/patient-medicines', []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['patient_id', 'medicine_id']);
        });

        it('rejects a duplicate balance for the same patient and medicine', function () {
            $patientMedicine = PatientMedicine::factory()->create();

            $response = $this->postJson('/api/patient-medicines', [
                'patient_id' => $patientMedicine->patient_id,
                'medicine_id' => $patientMedicine->medicine_id,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['medicine_id']);
        });
    });

    describe('Read/Update', function () {
        it('lists patient medicines', function () {
            PatientMedicine::factory()->count(2)->create();

            $response = $this->getJson('/api/patient-medicines');

            $response->assertStatus(200)
                ->assertJsonStructure(['data', 'links', 'meta']);
        });

        it('shows one patient medicine', function () {
            $patientMedicine = PatientMedicine::factory()->create();

            $response = $this->getJson("/api/patient-medicines/{$patientMedicine->id}");

            $response->assertStatus(200)
                ->assertJsonPath('data.id', $patientMedicine->id);
        });

        it('updates the minimum quantity without changing the balance', function () {
            $patientMedicine = PatientMedicine::factory()->create(['current_quantity' => 15]);

            $response = $this->patchJson("/api/patient-medicines/{$patientMedicine->id}", [
                'minimum_quantity' => 3,
                // attempting to smuggle a balance change through update — must be ignored
                'current_quantity' => 999,
            ]);

            $response->assertStatus(200)
                ->assertJsonPath('data.minimum_quantity', 3);

            $this->assertDatabaseHas('patient_medicines', [
                'id' => $patientMedicine->id,
                'current_quantity' => 15,
                'minimum_quantity' => 3,
            ]);
        });
    });

    describe('Low stock (GET /api/patient-medicines/low-stock)', function () {
        it('lists only balances at or below the minimum quantity', function () {
            $low = PatientMedicine::factory()->create(['current_quantity' => 2, 'minimum_quantity' => 5]);
            PatientMedicine::factory()->create(['current_quantity' => 50, 'minimum_quantity' => 5]);
            PatientMedicine::factory()->create(['current_quantity' => 10, 'minimum_quantity' => null]);

            $response = $this->getJson('/api/patient-medicines/low-stock');

            $response->assertStatus(200);
            $ids = collect($response->json('data'))->pluck('id');

            expect($ids)->toContain($low->id);
            expect($ids)->toHaveCount(1);
        });
    });

    describe('Soft delete lifecycle', function () {
        it('soft deletes, lists as trashed, restores and force deletes', function () {
            $patientMedicine = PatientMedicine::factory()->create();

            $this->deleteJson("/api/patient-medicines/{$patientMedicine->id}")->assertStatus(204);
            $this->assertSoftDeleted('patient_medicines', ['id' => $patientMedicine->id]);

            $trashedResponse = $this->getJson('/api/patient-medicines/trashed');
            $trashedResponse->assertStatus(200);
            expect(collect($trashedResponse->json('data'))->pluck('id'))->toContain($patientMedicine->id);

            $this->postJson("/api/patient-medicines/{$patientMedicine->id}/restore")->assertStatus(200);
            $this->assertDatabaseHas('patient_medicines', ['id' => $patientMedicine->id, 'deleted_at' => null]);

            $this->deleteJson("/api/patient-medicines/{$patientMedicine->id}")->assertStatus(204);
            $this->deleteJson("/api/patient-medicines/{$patientMedicine->id}/force-delete")->assertStatus(204);
            $this->assertDatabaseMissing('patient_medicines', ['id' => $patientMedicine->id]);
        });
    });
});

describe('Patient medicines (GET /api/patients/{patient}/medicines)', function () {
    it('lists the real, persisted balance owned by the patient', function () {
        $patient = Patient::factory()->create();
        $patientMedicine = PatientMedicine::factory()->create(['patient_id' => $patient->id, 'current_quantity' => 12]);

        $response = $this->getJson("/api/patients/{$patient->id}/medicines");

        $response->assertStatus(200);
        $data = collect($response->json('data'));
        expect($data->firstWhere('id', $patientMedicine->id)['current_quantity'])->toBe(12);
    });
});
