<?php

use App\Enums\DocumentTypeEnum;
use App\Models\User;

describe('UserController', function () {
    describe('Store (POST /api/users)', function () {
        it('creates a new user with valid data', function () {
            $data = [
                'name' => 'John Doe',
                'document_type' => DocumentTypeEnum::CPF->value,
                'document_number' => '12345678901',
                'password' => 'Password@123',
                'password_confirmation' => 'Password@123',
            ];

            $response = $this->postJson('/api/users', $data);

            $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'document_type',
                        'document_number',
                    ],
                ]);

            $this->assertDatabaseHas('users', [
                'name' => 'John Doe',
            ]);
        });

        it('fails with missing required fields', function () {
            $data = [
                'name' => 'John Doe',
            ];

            $response = $this->postJson('/api/users', $data);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['document_type', 'document_number', 'password']);
        });

        it('fails with invalid document type', function () {
            $data = [
                'name' => 'John Doe',
                'document_type' => 'INVALID',
                'document_number' => '12345678901',
                'password' => 'Password@123',
                'password_confirmation' => 'Password@123',
            ];

            $response = $this->postJson('/api/users', $data);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['document_type']);
        });

        it('fails with weak password', function () {
            $data = [
                'name' => 'John Doe',
                'document_type' => 'CPF',
                'document_number' => '12345678901',
                'password' => 'weak',
                'password_confirmation' => 'weak',
            ];

            $response = $this->postJson('/api/users', $data);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
        });

        it('fails with duplicate document number and type', function () {
            User::factory()->create([
                'document_type' => 'CPF',
                'document_number' => '12345678901',
            ]);

            $data = [
                'name' => 'Jane Doe',
                'document_type' => 'CPF',
                'document_number' => '12345678901',
                'password' => 'Password@123',
                'password_confirmation' => 'Password@123',
            ];

            $response = $this->postJson('/api/users', $data);

            $response->assertStatus(422);
        });
    });

    describe('Update (PATCH /api/users/{id})', function () {
        it('updates a user with valid data', function () {
            $user = User::factory()->create([
                'name' => 'Old Name',
                'document_type' => 'CPF',
                'document_number' => '11111111111',
            ]);

            $data = [
                'name' => 'Updated Name',
                'document_type' => 'CPF',
                'document_number' => '22222222222',
                'password' => 'NewPassword@123',
            ];

            $response = $this->actingAs($user)->patchJson("/api/users/{$user->id}", $data);

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'document_type',
                        'document_number',
                    ],
                ])
                ->assertJson([
                    'data' => [
                        'name' => 'Updated Name',
                        'document_number' => '22222222222',
                    ],
                ]);

            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'name' => 'Updated Name',
                'document_number' => '22222222222',
            ]);
        });

        it('fails with missing required fields', function () {
            $user = User::factory()->create();

            $data = [
                'name' => 'Updated Name',
            ];

            $response = $this->actingAs($user)->patchJson("/api/users/{$user->id}", $data);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['document_type', 'document_number']);
        });

        it('fails with invalid document type', function () {
            $user = User::factory()->create();

            $data = [
                'name' => 'Updated Name',
                'document_type' => 'INVALID',
                'document_number' => '12345678901',
            ];

            $response = $this->actingAs($user)->patchJson("/api/users/{$user->id}", $data);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['document_type']);
        });

        it('fails with duplicate document number and type', function () {
            $user1 = User::factory()->create([
                'document_type' => 'CPF',
                'document_number' => '11111111111',
            ]);

            $user2 = User::factory()->create([
                'document_type' => 'CPF',
                'document_number' => '22222222222',
            ]);

            $data = [
                'name' => 'Updated Name',
                'document_type' => 'CPF',
                'document_number' => '11111111111',
            ];

            $response = $this->actingAs($user2)->patchJson("/api/users/{$user2->id}", $data);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['document_number']);
        });

        it('allows user to keep their own document', function () {
            $user = User::factory()->create([
                'document_type' => 'CPF',
                'document_number' => '12345678901',
            ]);

            $data = [
                'name' => 'Updated Name',
                'document_type' => 'CPF',
                'document_number' => '12345678901',
            ];

            $response = $this->actingAs($user)->patchJson("/api/users/{$user->id}", $data);

            $response->assertStatus(200);
        });

        it('fails with weak password', function () {
            $user = User::factory()->create();

            $data = [
                'name' => 'Updated Name',
                'document_type' => 'CPF',
                'document_number' => '12345678901',
                'password' => 'weak',
            ];

            $response = $this->actingAs($user)->patchJson("/api/users/{$user->id}", $data);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
        });

        it('allows null password (optional field)', function () {
            $user = User::factory()->create();
            $oldPassword = $user->password;

            $data = [
                'name' => 'Updated Name',
                'document_type' => 'CPF',
                'document_number' => '99999999999',
                'password' => null,
            ];

            $response = $this->actingAs($user)->patchJson("/api/users/{$user->id}", $data);

            $response->assertStatus(200);

            expect($user->refresh()->password)->toBe($oldPassword);
        });
    });
});
