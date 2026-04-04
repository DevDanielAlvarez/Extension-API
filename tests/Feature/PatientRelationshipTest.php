<?php

use App\Models\Patient;
use App\Models\Responsible;

describe('Patient relationships', function () {
    it('associa um paciente a múltiplos responsáveis', function () {
        $patient = Patient::factory()->create();
        $responsibleOne = Responsible::factory()->create();
        $responsibleTwo = Responsible::factory()->create();

        $patient->responsibles()->attach([$responsibleOne->id, $responsibleTwo->id]);

        expect($patient->responsibles()->count())->toBe(2);
        expect($responsibleOne->patients()->whereKey($patient->id)->exists())->toBeTrue();
        expect($responsibleTwo->patients()->whereKey($patient->id)->exists())->toBeTrue();

        $this->assertDatabaseHas('patient_responsible', [
            'patient_id' => $patient->id,
            'responsible_id' => $responsibleOne->id,
        ]);

        $this->assertDatabaseHas('patient_responsible', [
            'patient_id' => $patient->id,
            'responsible_id' => $responsibleTwo->id,
        ]);
    });

    it('remove vínculos da pivot ao deletar paciente (cascade)', function () {
        $patient = Patient::factory()->create();
        $responsible = Responsible::factory()->create();

        $patient->responsibles()->attach($responsible->id);

        $this->assertDatabaseHas('patient_responsible', [
            'patient_id' => $patient->id,
            'responsible_id' => $responsible->id,
        ]);

        $patient->delete();

        $this->assertDatabaseMissing('patient_responsible', [
            'patient_id' => $patient->id,
            'responsible_id' => $responsible->id,
        ]);
    })->skip();
});
