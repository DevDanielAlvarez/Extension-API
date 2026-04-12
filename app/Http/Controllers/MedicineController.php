<?php

namespace App\Http\Controllers;

use App\DTO\Medicine\CreateMedicineDTO;
use App\DTO\Medicine\UpdateMedicineDTO;
use App\Enums\ContentUnitEnum;
use App\Enums\RouteOfAdministrationEnum;
use App\Http\Requests\Medicine\CreateMedicineFormRequest;
use App\Http\Requests\Medicine\UpdateMedicineFormRequest;
use App\Http\Resources\MedicineResource;
use App\Models\Medicine;
use App\Services\Medicine\MedicineService;
use Illuminate\Support\Facades\DB;

class MedicineController extends Controller
{
    public function index()
    {
        return MedicineResource::collection(Medicine::paginate(10));
    }

    public function trashed()
    {
        return MedicineResource::collection(Medicine::onlyTrashed()->paginate(10));
    }

    public function store(CreateMedicineFormRequest $request)
    {
        $validatedData = $request->validated();

        $result = DB::transaction(function () use ($validatedData) {
            $dtoToCreate = new CreateMedicineDTO(
                name: $validatedData['name'],
                content_quantity: $validatedData['content_quantity'],
                content_unit: ContentUnitEnum::from($validatedData['content_unit']),
                strength: $validatedData['strength'],
                is_compounded: $validatedData['is_compounded'] ?? false,
                route_of_administration: RouteOfAdministrationEnum::from($validatedData['route_of_administration']),
                additional_information: $validatedData['additional_information'] ?? null,
            );

            $medicineService = MedicineService::create($dtoToCreate);

            return new MedicineResource($medicineService->getRecord());
        });

        return $result->response()->setStatusCode(201);
    }

    public function show(string $medicine)
    {
        $medicineService = MedicineService::find($medicine);

        return MedicineResource::make($medicineService->getRecord());
    }

    public function update(UpdateMedicineFormRequest $request, string $medicine)
    {
        $validatedData = $request->validated();

        $result = DB::transaction(function () use ($validatedData, $medicine) {
            $dtoToUpdate = new UpdateMedicineDTO(
                id: $medicine,
                name: $validatedData['name'],
                content_quantity: $validatedData['content_quantity'],
                content_unit: ContentUnitEnum::from($validatedData['content_unit']),
                strength: $validatedData['strength'],
                is_compounded: $validatedData['is_compounded'] ?? false,
                route_of_administration: RouteOfAdministrationEnum::from($validatedData['route_of_administration']),
                additional_information: $validatedData['additional_information'] ?? null,
            );

            $medicineService = MedicineService::find($medicine);
            $medicineService->update($dtoToUpdate);

            return MedicineResource::make($medicineService->getRecord());
        });

        return $result;
    }

    public function destroy(string $id)
    {
        $medicineService = MedicineService::find($id);
        $medicineService->getRecord()->delete();

        return response()->noContent();
    }

    public function restore(string $medicine)
    {
        $record = Medicine::onlyTrashed()->findOrFail($medicine);
        $record->restore();

        return MedicineResource::make($record->fresh());
    }

    public function forceDelete(string $medicine)
    {
        $record = Medicine::withTrashed()->findOrFail($medicine);
        $record->forceDelete();

        return response()->noContent();
    }
}
