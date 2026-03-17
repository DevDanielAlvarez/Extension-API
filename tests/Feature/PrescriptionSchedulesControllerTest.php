<?php

use App\Models\Prescription;
use App\Models\PrescriptionSchedule;

describe('PrescriptionSchedulesController', function () {
    describe('Store (POST /api/prescription-schedules)', function () {
        it('creates a prescription schedule with valid data', function () {
            $prescription = Prescription::factory()->create();

            $payload = [
                'prescription_id' => $prescription->id,
                'day_of_week' => 1,
                'time' => '08:00',
                'quantity' => 1,
            ];

            $response = $this->postJson('/api/prescription-schedules', $payload);

            $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'prescription_id',
                        'day_of_week',
                        'time',
                        'quantity',
                    ],
                ]);

            $this->assertDatabaseHas('prescription_schedules', [
                'prescription_id' => $prescription->id,
                'day_of_week' => 1,
                'time' => '08:00',
                'quantity' => 1,
            ]);
        });

        it('fails with missing required fields', function () {
            $response = $this->postJson('/api/prescription-schedules', [
                'prescription_id' => 'some-id',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors([
                    'day_of_week',
                    'time',
                    'quantity',
                ]);
        });

        it('fails with invalid prescription_id', function () {
            $response = $this->postJson('/api/prescription-schedules', [
                'prescription_id' => 'invalid-id',
                'day_of_week' => 1,
                'time' => '08:00',
                'quantity' => 1,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['prescription_id']);
        });

        it('fails with invalid day_of_week (must be 0-6)', function () {
            $prescription = Prescription::factory()->create();

            $response = $this->postJson('/api/prescription-schedules', [
                'prescription_id' => $prescription->id,
                'day_of_week' => 7,
                'time' => '08:00',
                'quantity' => 1,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['day_of_week']);
        });

        it('fails with invalid time format', function () {
            $prescription = Prescription::factory()->create();

            $response = $this->postJson('/api/prescription-schedules', [
                'prescription_id' => $prescription->id,
                'day_of_week' => 1,
                'time' => 'invalid-time',
                'quantity' => 1,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['time']);
        });

        it('fails with quantity less than 1', function () {
            $prescription = Prescription::factory()->create();

            $response = $this->postJson('/api/prescription-schedules', [
                'prescription_id' => $prescription->id,
                'day_of_week' => 1,
                'time' => '08:00',
                'quantity' => 0,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['quantity']);
        });
    });

    describe('Read/Update/Delete', function () {
        it('lists prescription schedules', function () {
            PrescriptionSchedule::factory()->count(2)->create();

            $response = $this->getJson('/api/prescription-schedules');

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data',
                    'links',
                    'meta',
                ]);
        });

        it('shows one prescription schedule', function () {
            $schedule = PrescriptionSchedule::factory()->create();

            $response = $this->getJson("/api/prescription-schedules/{$schedule->id}");

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'prescription_id',
                        'day_of_week',
                        'time',
                        'quantity',
                    ],
                ]);
        });

        it('updates schedule with valid data', function () {
            $schedule = PrescriptionSchedule::factory()->create([
                'day_of_week' => 1,
                'time' => '08:00',
                'quantity' => 1,
            ]);

            $payload = [
                'prescription_id' => $schedule->prescription_id,
                'day_of_week' => 3,
                'time' => '14:30',
                'quantity' => 2,
            ];

            $response = $this->patchJson("/api/prescription-schedules/{$schedule->id}", $payload);

            $response->assertStatus(200)
                ->assertJsonPath('data.day_of_week', 3)
                ->assertJsonPath('data.time', '14:30')
                ->assertJsonPath('data.quantity', 2);

            $this->assertDatabaseHas('prescription_schedules', [
                'id' => $schedule->id,
                'day_of_week' => 3,
                'time' => '14:30',
                'quantity' => 2,
            ]);
        });

        it('fails update with invalid time', function () {
            $schedule = PrescriptionSchedule::factory()->create();

            $response = $this->patchJson("/api/prescription-schedules/{$schedule->id}", [
                'prescription_id' => $schedule->prescription_id,
                'day_of_week' => 1,
                'time' => 'bad-time',
                'quantity' => 1,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['time']);
        });

        it('deletes schedule', function () {
            $schedule = PrescriptionSchedule::factory()->create();

            $response = $this->deleteJson("/api/prescription-schedules/{$schedule->id}");

            $response->assertStatus(204);
            $this->assertDatabaseMissing('prescription_schedules', [
                'id' => $schedule->id,
            ]);
        });
    });
});
