<?php

use App\Enums\StockMovementTypeEnum;
use App\Models\PatientMedicine;

describe('Patient medicine movements (POST /api/patient-medicines/{patientMedicine}/movements)', function () {
    it('increases the balance on an IN movement', function () {
        $patientMedicine = PatientMedicine::factory()->create(['current_quantity' => 10]);

        $response = $this->postJson("/api/patient-medicines/{$patientMedicine->id}/movements", [
            'type' => StockMovementTypeEnum::IN->value,
            'quantity' => 15,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('patient_medicines', ['id' => $patientMedicine->id, 'current_quantity' => 25]);
        $this->assertDatabaseHas('patient_medicine_movements', [
            'patient_medicine_id' => $patientMedicine->id,
            'type' => StockMovementTypeEnum::IN->value,
            'quantity' => 15,
        ]);
    });

    it('decreases the balance on an OUT movement', function () {
        $patientMedicine = PatientMedicine::factory()->create(['current_quantity' => 10]);

        $response = $this->postJson("/api/patient-medicines/{$patientMedicine->id}/movements", [
            'type' => StockMovementTypeEnum::OUT->value,
            'quantity' => 4,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('patient_medicines', ['id' => $patientMedicine->id, 'current_quantity' => 6]);
    });

    it('rejects an OUT movement that would leave a negative balance', function () {
        $patientMedicine = PatientMedicine::factory()->create(['current_quantity' => 3]);

        $response = $this->postJson("/api/patient-medicines/{$patientMedicine->id}/movements", [
            'type' => StockMovementTypeEnum::OUT->value,
            'quantity' => 10,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['quantity']);
        $this->assertDatabaseHas('patient_medicines', ['id' => $patientMedicine->id, 'current_quantity' => 3]);
    });

    it('sets the balance directly on an ADJUSTMENT movement', function () {
        $patientMedicine = PatientMedicine::factory()->create(['current_quantity' => 40]);

        $response = $this->postJson("/api/patient-medicines/{$patientMedicine->id}/movements", [
            'type' => StockMovementTypeEnum::ADJUSTMENT->value,
            'quantity' => 33,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('patient_medicines', ['id' => $patientMedicine->id, 'current_quantity' => 33]);
    });

    it('restores the balance on a RETURNED movement', function () {
        $patientMedicine = PatientMedicine::factory()->create(['current_quantity' => 5]);

        $response = $this->postJson("/api/patient-medicines/{$patientMedicine->id}/movements", [
            'type' => StockMovementTypeEnum::RETURNED->value,
            'quantity' => 2,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('patient_medicines', ['id' => $patientMedicine->id, 'current_quantity' => 7]);
    });

    it('lists movements for a patient medicine', function () {
        $patientMedicine = PatientMedicine::factory()->create();
        $this->postJson("/api/patient-medicines/{$patientMedicine->id}/movements", [
            'type' => StockMovementTypeEnum::IN->value,
            'quantity' => 5,
        ])->assertStatus(201);

        $response = $this->getJson("/api/patient-medicines/{$patientMedicine->id}/movements");

        $response->assertStatus(200)->assertJsonStructure(['data', 'links', 'meta']);
        expect($response->json('data'))->toHaveCount(1);
    });
});
