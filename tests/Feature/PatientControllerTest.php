<?php

use App\Enums\DocumentTypeEnum;
use App\Models\Patient;

describe('PatientController', function () {
    describe('Store (POST /api/patients)', function () {
        it('creates a patient with valid data', function () {
            $payload = [
                'name' => 'Paciente 1',
                'document_type' => DocumentTypeEnum::CPF->value,
                'document_number' => '12345678901',
                'admission_date' => '2026-03-10',
                'birthday' => '2000-01-10',
                'phone' => '11999999999',
                'nursing_report' => ['first note'],
            ];

            $response = $this->postJson('/api/patients', $payload);

            $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'document_type',
                        'document_number',
                        'admission_date',
                        'birthday',
                        'phone',
                        'nursing_report',
                    ],
                ]);

            $this->assertDatabaseHas('patients', [
                'name' => 'Paciente 1',
                'document_type' => DocumentTypeEnum::CPF->value,
                'document_number' => '12345678901',
            ]);
        });

        it('fails with invalid document type', function () {
            $response = $this->postJson('/api/patients', [
                'name' => 'Paciente 1',
                'document_type' => 'INVALID',
                'document_number' => '12345678901',
                'admission_date' => '2026-03-10',
                'birthday' => '2000-01-10',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['document_type']);
        });

        it('fails with duplicate document for same type', function () {
            Patient::factory()->create([
                'document_type' => DocumentTypeEnum::CPF->value,
                'document_number' => '12345678901',
            ]);

            $response = $this->postJson('/api/patients', [
                'name' => 'Paciente 2',
                'document_type' => DocumentTypeEnum::CPF->value,
                'document_number' => '12345678901',
                'admission_date' => '2026-03-10',
                'birthday' => '2000-01-10',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['document_number']);
        });
    });

    describe('Read/Update/Delete', function () {
        it('lists patients', function () {
            Patient::factory()->count(2)->create();

            $response = $this->getJson('/api/patients');

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data',
                    'links',
                    'meta',
                ]);
        });

        it('shows one patient', function () {
            $patient = Patient::factory()->create();

            $response = $this->getJson("/api/patients/{$patient->id}");

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'document_type',
                        'document_number',
                    ],
                ]);
        });

        it('updates patient with valid data', function () {
            $patient = Patient::factory()->create([
                'document_type' => DocumentTypeEnum::CPF->value,
                'document_number' => '11111111111',
            ]);

            $payload = [
                'name' => 'Paciente Atualizado',
                'document_type' => DocumentTypeEnum::CPF->value,
                'document_number' => '22222222222',
                'admission_date' => '2026-03-11',
                'birthday' => '1999-05-20',
                'phone' => '11888888888',
                'nursing_report' => ['updated note'],
            ];

            $response = $this->patchJson("/api/patients/{$patient->id}", $payload);

            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Paciente Atualizado')
                ->assertJsonPath('data.document_number', '22222222222');

            $this->assertDatabaseHas('patients', [
                'id' => $patient->id,
                'name' => 'Paciente Atualizado',
                'document_number' => '22222222222',
            ]);
        });

        it('fails update with invalid document type', function () {
            $patient = Patient::factory()->create();

            $payload = [
                'name' => 'Paciente Atualizado',
                'document_type' => 'INVALID',
                'document_number' => '22222222222',
                'admission_date' => '2026-03-11',
                'birthday' => '1999-05-20',
            ];

            $response = $this->patchJson("/api/patients/{$patient->id}", $payload);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['document_type']);
        });
    });
});
