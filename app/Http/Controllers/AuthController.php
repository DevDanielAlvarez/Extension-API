<?php

namespace App\Http\Controllers;

use App\DTO\User\CreateUserDTO;
use App\Http\Requests\LoginFormRequest;
use App\Http\Requests\User\CreateUserFormRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginFormRequest $request)
    {
        // get validated data from http request
        $validatedData = $request->validated();
        // find the user based in registration number passed in http request
        $user = User::where('document_type', $validatedData['document_type'])->where('document_number', $validatedData['document_number'])->first();
        // the user must exists and the password must correct
        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            return response()
                ->json([
                    'message' => 'Invalid credentials'
                ], 401);
        }
        // if the user exists and the password is correct, create a new token to login user using sanctum
        $token = $user->createToken('auth_token')->plainTextToken;
        // return the user and token found
        return response()
            ->json([
                'message' => 'Login successfully',
                'user' => UserResource::make($user),
                'token' => $token,
                'token_type' => 'Bearer'
            ]);
    }

    public function register(CreateUserFormRequest $request): JsonResponse
    {
        // Get validated data
        $validatedData = $request->validated();
        // Create a dto using validated data
        $userDto = new CreateUserDTO(
            name: $validatedData['name'],
            documentType: $validatedData['document_type'],
            documentNumber: $validatedData['document_number'],
            password: $validatedData['password']

        );
        // Create a user using validated data
        $user = UserService::create($userDto);
        // Create token to created user
        $token = $user->getRecord()->createToken('auth_token')->plainTextToken;
        // Return a json response with the user created
        return response()->json([
            'message' => 'User Registred successfully',
            'user' => UserResource::make($user->getRecord()),
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
}
