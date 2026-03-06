<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginFormRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
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

    public function register() {}
}
