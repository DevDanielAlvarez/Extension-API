<?php

namespace App\Http\Controllers;

use App\DTO\Responsible\CreateResponsibleDTO;
use App\DTO\Responsible\UpdateResponsibleDTO;
use App\Enums\DocumentTypeEnum;
use App\Http\Requests\Responsible\CreateResponsibleFormRequest;
use App\Http\Requests\Responsible\UpdateResponsibleFormRequest;
use App\Http\Resources\PatientResource;
use App\Http\Resources\ResponsibleResource;
use App\Models\Responsible;
use App\Services\Patient\PatientService;
use App\Services\Responsible\ResponsibleService;
use Illuminate\Support\Facades\DB;

class ResponsibleController extends Controller
{
    public function index()
    {
        return ResponsibleResource::collection(Responsible::paginate(10));
    }

    public function trashed()
    {
        return ResponsibleResource::collection(Responsible::onlyTrashed()->paginate(10));
    }

    public function store(CreateResponsibleFormRequest $request)
    {
        $validatedData = $request->validated();

        $result = DB::transaction(function () use ($validatedData) {
            $dtoToCreate = new CreateResponsibleDTO(
                name: $validatedData['name'],
                document_type: DocumentTypeEnum::from($validatedData['document_type']),
                document_number: $validatedData['document_number'],
                phone: $validatedData['phone'] ?? null,
            );

            $responsibleService = ResponsibleService::create($dtoToCreate);

            return new ResponsibleResource($responsibleService->getRecord());
        });

        return $result->response()->setStatusCode(201);
    }

    public function show(string $responsible)
    {
        $responsibleService = ResponsibleService::find($responsible);

        return ResponsibleResource::make($responsibleService->getRecord());
    }

    public function update(UpdateResponsibleFormRequest $request, string $responsible)
    {
        $validatedData = $request->validated();

        $result = DB::transaction(function () use ($validatedData, $responsible) {
            $dtoToUpdate = new UpdateResponsibleDTO(
                id: $responsible,
                name: $validatedData['name'],
                document_type: DocumentTypeEnum::from($validatedData['document_type']),
                document_number: $validatedData['document_number'],
                phone: $validatedData['phone'] ?? null,
            );

            $responsibleService = ResponsibleService::find($responsible);
            $responsibleService->update($dtoToUpdate);

            return ResponsibleResource::make($responsibleService->getRecord());
        });

        return $result;
    }

    public function destroy(string $responsible)
    {
        $responsibleService = ResponsibleService::find($responsible);
        $responsibleService->delete();

        return response()->noContent();
    }

    public function restore(string $responsible)
    {
        $record = Responsible::onlyTrashed()->findOrFail($responsible);
        $record->restore();

        return ResponsibleResource::make($record->fresh());
    }

    public function forceDelete(string $responsible)
    {
        $record = Responsible::withTrashed()->findOrFail($responsible);
        $record->forceDelete();

        return response()->noContent();
    }

    public function attachPatient(string $responsible, string $patient)
    {
        $responsibleService = ResponsibleService::find($responsible);
        PatientService::find($patient);

        $responsibleService->getRecord()->patients()->syncWithoutDetaching([$patient]);

        return response()->noContent();
    }

    public function detachPatient(string $responsible, string $patient)
    {
        $responsibleService = ResponsibleService::find($responsible);
        PatientService::find($patient);

        $responsibleService->getRecord()->patients()->detach($patient);

        return response()->noContent();
    }

    public function patients(string $responsible)
    {
        $responsibleService = ResponsibleService::find($responsible);

        return PatientResource::collection(
            $responsibleService->getRecord()->patients()->paginate(10)
        );
    }
}
