<?php

use App\Enums\ContentUnitEnum;
use App\Enums\RouteOfAdministrationEnum;
use App\Models\Medicine;

describe('MedicineController', function () {
    describe('Store (POST /api/medicines)', function () {
        it('creates a medicine with valid data', function () {
            $payload = [
                'name' => 'Dipirona',
                'content_quantity' => 20,
                'content_unit' => ContentUnitEnum::MG->value,
                'strength' => '500mg',
                'is_compounded' => false,
                'route_of_administration' => RouteOfAdministrationEnum::ORAL->value,
                'additional_information' => 'Tomar após refeição',
            ];

            $response = $this->postJson('/api/medicines', $payload);

            $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'content_quantity',
                        'content_unit',
                        'strength',
                        'is_compounded',
                        'route_of_administration',
                        'additional_information',
                    ],
                ]);

            $this->assertDatabaseHas('medicines', [
                'name' => 'Dipirona',
                'content_quantity' => 20,
                'content_unit' => ContentUnitEnum::MG->value,
                'route_of_administration' => RouteOfAdministrationEnum::ORAL->value,
            ]);
        });

        it('fails with missing required fields', function () {
            $response = $this->postJson('/api/medicines', [
                'name' => 'Dipirona',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors([
                    'content_quantity',
                    'content_unit',
                    'strength',
                    'route_of_administration',
                ]);
        });

        it('fails with invalid enums', function () {
            $response = $this->postJson('/api/medicines', [
                'name' => 'Dipirona',
                'content_quantity' => 20,
                'content_unit' => 'INVALID',
                'strength' => '500mg',
                'route_of_administration' => 'INVALID',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['content_unit', 'route_of_administration']);
        });
    });

    describe('Read/Update', function () {
        it('lists medicines', function () {
            Medicine::factory()->count(2)->create();

            $response = $this->getJson('/api/medicines');

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data',
                    'links',
                    'meta',
                ]);
        });

        it('shows one medicine', function () {
            $medicine = Medicine::factory()->create();

            $response = $this->getJson("/api/medicines/{$medicine->id}");

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'content_quantity',
                        'content_unit',
                        'strength',
                        'is_compounded',
                        'route_of_administration',
                    ],
                ]);
        });

        it('updates medicine with valid data', function () {
            $medicine = Medicine::factory()->create([
                'name' => 'Medicamento Antigo',
                'content_quantity' => 10,
                'content_unit' => ContentUnitEnum::MG->value,
                'strength' => '250mg',
                'route_of_administration' => RouteOfAdministrationEnum::ORAL->value,
            ]);

            $payload = [
                'name' => 'Medicamento Atualizado',
                'content_quantity' => 30,
                'content_unit' => ContentUnitEnum::ML->value,
                'strength' => '10mg/ml',
                'is_compounded' => true,
                'route_of_administration' => RouteOfAdministrationEnum::INHALATION->value,
                'additional_information' => 'Uso contínuo',
            ];

            $response = $this->patchJson("/api/medicines/{$medicine->id}", $payload);

            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Medicamento Atualizado')
                ->assertJsonPath('data.content_quantity', 30);

            $this->assertDatabaseHas('medicines', [
                'id' => $medicine->id,
                'name' => 'Medicamento Atualizado',
                'content_quantity' => 30,
                'content_unit' => ContentUnitEnum::ML->value,
                'route_of_administration' => RouteOfAdministrationEnum::INHALATION->value,
            ]);
        });

        it('fails update with invalid enums', function () {
            $medicine = Medicine::factory()->create();

            $response = $this->patchJson("/api/medicines/{$medicine->id}", [
                'name' => 'Medicamento Atualizado',
                'content_quantity' => 20,
                'content_unit' => 'INVALID',
                'strength' => '500mg',
                'route_of_administration' => 'INVALID',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['content_unit', 'route_of_administration']);
        });
    });
});
