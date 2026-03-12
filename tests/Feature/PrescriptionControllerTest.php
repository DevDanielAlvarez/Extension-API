<?php

use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Prescription;

describe('PrescriptionController', function () {
    describe('Store (POST /api/prescriptions)', function () {
        it('creates a prescription with valid data', function () {
            $patient = Patient::factory()->create();
            $medicine = Medicine::factory()->create();

            $payload = [
                'patient_id' => $patient->id,
                'medicine_id' => $medicine->id,
                'start_date' => '2026-03-12',
                'end_date' => '2026-03-19',
                'instructions' => 'Tomar uma vez ao dia com água',
            ];

            $response = $this->postJson('/api/prescriptions', $payload);

            $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'patient_id',
                        'medicine_id',
                        'start_date',
                        'end_date',
                        'instructions',
                    ],
                ]);

            $this->assertDatabaseHas('prescriptions', [
                'patient_id' => $patient->id,
                'medicine_id' => $medicine->id,
                'instructions' => 'Tomar uma vez ao dia com água',
            ]);
        });

        it('fails with missing required fields', function () {
            $response = $this->postJson('/api/prescriptions', [
                'patient_id' => 'some-id',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors([
                    'medicine_id',
                    'start_date',
                    'end_date',
                ]);
        });

        it('fails with invalid patient_id', function () {
            $medicine = Medicine::factory()->create();

            $response = $this->postJson('/api/prescriptions', [
                'patient_id' => 'invalid-patient-id',
                'medicine_id' => $medicine->id,
                'start_date' => '2026-03-12',
                'end_date' => '2026-03-19',
                'instructions' => 'Tomar uma vez ao dia',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['patient_id']);
        });

        it('fails with invalid medicine_id', function () {
            $patient = Patient::factory()->create();

            $response = $this->postJson('/api/prescriptions', [
                'patient_id' => $patient->id,
                'medicine_id' => 'invalid-medicine-id',
                'start_date' => '2026-03-12',
                'end_date' => '2026-03-19',
                'instructions' => 'Tomar uma vez ao dia',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['medicine_id']);
        });

        it('fails with end_date before start_date', function () {
            $patient = Patient::factory()->create();
            $medicine = Medicine::factory()->create();

            $response = $this->postJson('/api/prescriptions', [
                'patient_id' => $patient->id,
                'medicine_id' => $medicine->id,
                'start_date' => '2026-03-19',
                'end_date' => '2026-03-12',
                'instructions' => 'Tomar uma vez ao dia',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['end_date']);
        });
    });

    describe('Read/Update', function () {
        it('lists prescriptions', function () {
            Prescription::factory()->count(2)->create();

            $response = $this->getJson('/api/prescriptions');

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data',
                    'links',
                    'meta',
                ]);
        });

        it('shows one prescription', function () {
            $prescription = Prescription::factory()->create();

            $response = $this->getJson("/api/prescriptions/{$prescription->id}");

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'patient_id',
                        'medicine_id',
                        'start_date',
                        'end_date',
                        'instructions',
                    ],
                ]);
        });

        it('updates prescription with valid data', function () {
            $prescription = Prescription::factory()->create();
            $newMedicine = Medicine::factory()->create();

            $payload = [
                'patient_id' => $prescription->patient_id,
                'medicine_id' => $newMedicine->id,
                'start_date' => '2026-03-15',
                'end_date' => '2026-03-25',
                'instructions' => 'Tomar duas vezes ao dia',
            ];

            $response = $this->patchJson("/api/prescriptions/{$prescription->id}", $payload);

            $response->assertStatus(200)
                ->assertJsonPath('data.instructions', 'Tomar duas vezes ao dia')
                ->assertJsonPath('data.medicine_id', $newMedicine->id);

            $this->assertDatabaseHas('prescriptions', [
                'id' => $prescription->id,
                'instructions' => 'Tomar duas vezes ao dia',
                'medicine_id' => $newMedicine->id,
            ]);
        });

        it('fails update with invalid patient_id', function () {
            $prescription = Prescription::factory()->create();

            $response = $this->patchJson("/api/prescriptions/{$prescription->id}", [
                'patient_id' => 'invalid-id',
                'medicine_id' => $prescription->medicine_id,
                'start_date' => '2026-03-12',
                'end_date' => '2026-03-19',
                'instructions' => 'Updated instructions',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['patient_id']);
        });

        it('fails update with end_date before start_date', function () {
            $prescription = Prescription::factory()->create();

            $response = $this->patchJson("/api/prescriptions/{$prescription->id}", [
                'patient_id' => $prescription->patient_id,
                'medicine_id' => $prescription->medicine_id,
                'start_date' => '2026-03-19',
                'end_date' => '2026-03-12',
                'instructions' => 'Updated instructions',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['end_date']);
        });
    });
});
