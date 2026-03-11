<?php

use App\Enums\DocumentTypeEnum;
use App\Models\Responsible;

describe('ResponsibleController', function () {
    describe('Store (POST /api/responsibles)', function () {
        it('creates a responsible with valid data', function () {
            $payload = [
                'name' => 'Responsavel 1',
                'document_type' => DocumentTypeEnum::CPF->value,
                'document_number' => '12345678901',
                'phone' => '11999999999',
            ];

            $response = $this->postJson('/api/responsibles', $payload);

            $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'document_type',
                        'document_number',
                        'phone',
                    ],
                ]);

            $this->assertDatabaseHas('responsibles', [
                'name' => 'Responsavel 1',
                'document_type' => DocumentTypeEnum::CPF->value,
                'document_number' => '12345678901',
            ]);
        });

        it('fails with invalid document type', function () {
            $response = $this->postJson('/api/responsibles', [
                'name' => 'Responsavel 1',
                'document_type' => 'INVALID',
                'document_number' => '12345678901',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['document_type']);
        });

        it('fails with duplicate document for same type', function () {
            Responsible::factory()->create([
                'document_type' => DocumentTypeEnum::CPF->value,
                'document_number' => '12345678901',
            ]);

            $response = $this->postJson('/api/responsibles', [
                'name' => 'Responsavel 2',
                'document_type' => DocumentTypeEnum::CPF->value,
                'document_number' => '12345678901',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['document_number']);
        });
    });

    describe('Read/Update/Delete', function () {
        it('lists responsibles', function () {
            Responsible::factory()->count(2)->create();

            $response = $this->getJson('/api/responsibles');

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data',
                    'links',
                    'meta',
                ]);
        });

        it('shows one responsible', function () {
            $responsible = Responsible::factory()->create();

            $response = $this->getJson("/api/responsibles/{$responsible->id}");

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'document_type',
                        'document_number',
                        'phone',
                    ],
                ]);
        });

        it('updates responsible with valid data', function () {
            $responsible = Responsible::factory()->create([
                'document_type' => DocumentTypeEnum::CPF->value,
                'document_number' => '11111111111',
            ]);

            $payload = [
                'name' => 'Responsavel Atualizado',
                'document_type' => DocumentTypeEnum::CPF->value,
                'document_number' => '22222222222',
                'phone' => '11888888888',
            ];

            $response = $this->patchJson("/api/responsibles/{$responsible->id}", $payload);

            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Responsavel Atualizado')
                ->assertJsonPath('data.document_number', '22222222222');

            $this->assertDatabaseHas('responsibles', [
                'id' => $responsible->id,
                'name' => 'Responsavel Atualizado',
                'document_number' => '22222222222',
            ]);
        });

        it('fails update with invalid document type', function () {
            $responsible = Responsible::factory()->create();

            $payload = [
                'name' => 'Responsavel Atualizado',
                'document_type' => 'INVALID',
                'document_number' => '22222222222',
                'phone' => '11888888888',
            ];

            $response = $this->patchJson("/api/responsibles/{$responsible->id}", $payload);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['document_type']);
        });

        it('deletes responsible', function () {
            $responsible = Responsible::factory()->create();

            $response = $this->deleteJson("/api/responsibles/{$responsible->id}");

            $response->assertStatus(204);
            $this->assertDatabaseMissing('responsibles', [
                'id' => $responsible->id,
            ]);
        });
    });
});
