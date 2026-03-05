<?php

namespace App\Http\Controllers;

use App\DTO\User\CreateUserDTO;
use App\Http\Requests\User\CreateUserFormRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserResource::collection(User::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateUserFormRequest $request)
    {
        $validatedData = $request->validated();
        DB::transaction(function () use ($validatedData) {
            $dtoToCreate = new CreateUserDTO(
                name: $validatedData['name'],
                documentType: $validatedData['document_type'],
                documentNumber: $validatedData['document_number'],
                password: $validatedData['password']
            );
            $userService = UserService::create($dtoToCreate);
            return new UserResource($userService->getRecord());
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
