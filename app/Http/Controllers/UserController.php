<?php

namespace App\Http\Controllers;

use App\DTO\User\CreateUserDTO;
use App\DTO\User\UpdateUserDTO;
use App\Enums\DocumentTypeEnum;
use App\Http\Requests\User\CreateUserFormRequest;
use App\Http\Requests\User\UpdateUserFormRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\User\UserService;
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
        $result = DB::transaction(function () use ($validatedData) {
            $dtoToCreate = new CreateUserDTO(
                name: $validatedData['name'],
                document_type: DocumentTypeEnum::from($validatedData['document_type']),
                document_number: $validatedData['document_number'],
                password: $validatedData['password']
            );
            $userService = UserService::create($dtoToCreate);
            return new UserResource($userService->getRecord());
        });

        return $result;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = UserService::find($id);
        return UserResource::make($user->getRecord());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserFormRequest $request, string $id)
    {
        // get validated data
        $validatedData = $request->validated();
        $result = DB::transaction(callback: function () use ($validatedData, $id) {
            // create DTO to update
            $dtoToUpdate = new UpdateUserDTO(
                id: $id,
                name: $validatedData['name'],
                document_type: DocumentTypeEnum::from($validatedData['document_type']),
                document_number: $validatedData['document_number'],
                password: $validatedData['password'] ?? null
            );

            // find the user and update
            $userService = UserService::find($id);
            $userService->update($dtoToUpdate);
            // return the updated user resource
            return UserResource::make($userService->getRecord());
        });
        return $result;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
