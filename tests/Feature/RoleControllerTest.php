<?php

use App\Models\Role;

describe('RoleController', function () {
    describe('Store (POST /api/roles)', function () {
        it('creates a role with valid data', function () {
            $payload = [
                'name' => 'Administrador',
            ];

            $response = $this->postJson('/api/roles', $payload);

            $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                    ],
                ]);

            $this->assertDatabaseHas('roles', [
                'name' => 'Administrador',
            ]);
        });

        it('fails with missing required fields', function () {
            $response = $this->postJson('/api/roles', []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors([
                    'name',
                ]);
        });

        it('fails with duplicated role name', function () {
            Role::factory()->create(['name' => 'Administrador']);

            $response = $this->postJson('/api/roles', [
                'name' => 'Administrador',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
        });
    });

    describe('Read/Update', function () {
        it('lists roles', function () {
            Role::factory()->count(2)->create();

            $response = $this->getJson('/api/roles');

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data',
                    'links',
                    'meta',
                ]);
        });

        it('shows one role', function () {
            $role = Role::factory()->create();

            $response = $this->getJson("/api/roles/{$role->id}");

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                    ],
                ]);
        });

        it('updates role with valid data', function () {
            $role = Role::factory()->create([
                'name' => 'Padrão',
            ]);

            $payload = [
                'name' => 'Administrador',
            ];

            $response = $this->patchJson("/api/roles/{$role->id}", $payload);

            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Administrador');

            $this->assertDatabaseHas('roles', [
                'id' => $role->id,
                'name' => 'Administrador',
            ]);
        });

        it('fails update with duplicated role name', function () {
            $existingRole = Role::factory()->create([
                'name' => 'Administrador',
            ]);

            $roleToUpdate = Role::factory()->create([
                'name' => 'Padrão',
            ]);

            $response = $this->patchJson("/api/roles/{$roleToUpdate->id}", [
                'name' => $existingRole->name,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
        });
    });

    describe('Destroy', function () {
        it('deletes one role', function () {
            $role = Role::factory()->create();

            $response = $this->deleteJson("/api/roles/{$role->id}");

            $response->assertStatus(204);

            $this->assertDatabaseMissing('roles', [
                'id' => $role->id,
            ]);
        });
    });
});
