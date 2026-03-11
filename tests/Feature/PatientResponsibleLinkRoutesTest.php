<?php

use App\Models\Patient;
use App\Models\Responsible;

describe('Patient and Responsible link routes', function () {
    it('links responsible to patient through patient route', function () {
        $patient = Patient::factory()->create();
        $responsible = Responsible::factory()->create();

        $response = $this->postJson("/api/patients/{$patient->id}/responsibles/{$responsible->id}");

        $response->assertStatus(204);

        $this->assertDatabaseHas('patient_responsible', [
            'patient_id' => $patient->id,
            'responsible_id' => $responsible->id,
        ]);
    });

    it('links patient to responsible through responsible route', function () {
        $patient = Patient::factory()->create();
        $responsible = Responsible::factory()->create();

        $response = $this->postJson("/api/responsibles/{$responsible->id}/patients/{$patient->id}");

        $response->assertStatus(204);

        $this->assertDatabaseHas('patient_responsible', [
            'patient_id' => $patient->id,
            'responsible_id' => $responsible->id,
        ]);
    });

    it('does not duplicate existing link', function () {
        $patient = Patient::factory()->create();
        $responsible = Responsible::factory()->create();

        $this->postJson("/api/patients/{$patient->id}/responsibles/{$responsible->id}")->assertStatus(204);
        $this->postJson("/api/patients/{$patient->id}/responsibles/{$responsible->id}")->assertStatus(204);

        expect($patient->responsibles()->whereKey($responsible->id)->count())->toBe(1);
    });

    it('unlinks responsible from patient through patient route', function () {
        $patient = Patient::factory()->create();
        $responsible = Responsible::factory()->create();

        $patient->responsibles()->attach($responsible->id);

        $response = $this->deleteJson("/api/patients/{$patient->id}/responsibles/{$responsible->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('patient_responsible', [
            'patient_id' => $patient->id,
            'responsible_id' => $responsible->id,
        ]);
    });

    it('unlinks patient from responsible through responsible route', function () {
        $patient = Patient::factory()->create();
        $responsible = Responsible::factory()->create();

        $responsible->patients()->attach($patient->id);

        $response = $this->deleteJson("/api/responsibles/{$responsible->id}/patients/{$patient->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('patient_responsible', [
            'patient_id' => $patient->id,
            'responsible_id' => $responsible->id,
        ]);
    });
});
