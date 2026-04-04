<?php

use App\Enums\DocumentTypeEnum;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

describe('AuthController', function () {
    describe('Register (POST /api/auth/register)', function () {
        it('registers a user with valid data', function () {
            $data = [
                'name' => 'John Doe',
                'document_type' => DocumentTypeEnum::CPF->value,
                'document_number' => '12345678901',
                'password' => 'Password@123',
                'password_confirmation' => 'Password@123',
            ];

            $response = $this->postJson('/api/auth/register', $data);

            $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'user',
                        'token',
                        'token_type',
                    ],
                ]);

            $this->assertDatabaseHas('users', [
                'name' => 'John Doe',
                'document_type' => DocumentTypeEnum::CPF->value,
                'document_number' => '12345678901',
            ]);
        });

        it('fails with missing required fields', function () {
            $response = $this->postJson('/api/auth/register', [
                'name' => 'John Doe',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['document_type', 'document_number', 'password']);
        });

        it('fails with invalid document type', function () {
            $response = $this->postJson('/api/auth/register', [
                'name' => 'John Doe',
                'document_type' => 'INVALID',
                'document_number' => '12345678901',
                'password' => 'Password@123',
                'password_confirmation' => 'Password@123',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['document_type']);
        });

        it('fails with weak password', function () {
            $response = $this->postJson('/api/auth/register', [
                'name' => 'John Doe',
                'document_type' => DocumentTypeEnum::CPF->value,
                'document_number' => '12345678901',
                'password' => 'weak',
                'password_confirmation' => 'weak',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
        });

        it('fails with duplicate document', function () {
            User::factory()->create([
                'document_type' => DocumentTypeEnum::CPF->value,
                'document_number' => '12345678901',
            ]);

            $response = $this->postJson('/api/auth/register', [
                'name' => 'Jane Doe',
                'document_type' => DocumentTypeEnum::CPF->value,
                'document_number' => '12345678901',
                'password' => 'Password@123',
                'password_confirmation' => 'Password@123',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['document_number']);
        });

        it('fails with mismatched password confirmation', function () {
            $response = $this->postJson('/api/auth/register', [
                'name' => 'John Doe',
                'document_type' => DocumentTypeEnum::CPF->value,
                'document_number' => '12345678901',
                'password' => 'Password@123',
                'password_confirmation' => 'DifferentPassword@123',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
        });
    });

    describe('Login (POST /api/auth/login)', function () {
        it('logs in with valid credentials', function () {
            $user = User::factory()->create([
                'document_type' => DocumentTypeEnum::CPF->value,
                'document_number' => '12345678901',
                'password' => Hash::make('Password@123'),
            ]);

            $response = $this->postJson('/api/auth/login', [
                'document_type' => $user->document_type->value,
                'document_number' => $user->document_number,
                'password' => 'Password@123',
            ]);

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'user',
                        'token',
                        'token_type',
                    ],
                ])
                ->assertJson([
                    'message' => 'Login successfully',
                    'data' => [
                        'token_type' => 'Bearer',
                    ],
                ]);

            expect($response->json('data.token'))->not()->toBeEmpty();
        });

        it('fails with invalid password', function () {
            $user = User::factory()->create([
                'document_type' => DocumentTypeEnum::CPF->value,
                'document_number' => '12345678901',
                'password' => Hash::make('Password@123'),
            ]);

            $response = $this->postJson('/api/auth/login', [
                'document_type' => $user->document_type->value,
                'document_number' => $user->document_number,
                'password' => 'WrongPassword@123',
            ]);

            $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Invalid credentials',
                ]);
        });

        it('fails when user does not exist', function () {
            $response = $this->postJson('/api/auth/login', [
                'document_type' => DocumentTypeEnum::CPF->value,
                'document_number' => '99999999999',
                'password' => 'Password@123',
            ]);

            $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Invalid credentials',
                ]);
        });

        it('fails with invalid document type', function () {
            $response = $this->postJson('/api/auth/login', [
                'document_type' => 'INVALID',
                'document_number' => '12345678901',
                'password' => 'Password@123',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['document_type']);
        });

        it('fails with missing required fields', function () {
            $response = $this->postJson('/api/auth/login', [
                'document_type' => DocumentTypeEnum::CPF->value,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['document_number', 'password']);
        });
    });

    describe('Logout (POST /api/auth/logout)', function () {
        it('logs out the authenticated user', function () {
            $response = $this->postJson('/api/auth/logout');

            $response->assertOk()
                ->assertJson([
                    'message' => 'Logged out successfully',
                ]);
        });
    });
});
