<?php

namespace App\Http\Controllers;

use App\DTO\PatientMedicine\CreatePatientMedicineDTO;
use App\DTO\PatientMedicine\UpdatePatientMedicineDTO;
use App\DTO\PatientMedicineMovement\CreatePatientMedicineMovementDTO;
use App\Enums\StockMovementTypeEnum;
use App\Http\Requests\PatientMedicine\CreatePatientMedicineFormRequest;
use App\Http\Requests\PatientMedicine\UpdatePatientMedicineFormRequest;
use App\Http\Requests\PatientMedicineMovement\CreatePatientMedicineMovementFormRequest;
use App\Http\Resources\PatientMedicineMovementResource;
use App\Http\Resources\PatientMedicineResource;
use App\Models\PatientMedicine;
use App\Services\PatientMedicine\PatientMedicineService;
use App\Services\PatientMedicineMovement\PatientMedicineMovementService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PatientMedicineController extends Controller
{
    public function index()
    {
        return PatientMedicineResource::collection(PatientMedicine::with(['patient', 'medicine'])->paginate(10));
    }

    public function trashed()
    {
        return PatientMedicineResource::collection(PatientMedicine::onlyTrashed()->paginate(10));
    }

    public function lowStock()
    {
        return PatientMedicineResource::collection(
            PatientMedicine::with(['patient', 'medicine'])
                ->whereNotNull('minimum_quantity')
                ->whereColumn('current_quantity', '<=', 'minimum_quantity')
                ->paginate(10)
        );
    }

    public function store(CreatePatientMedicineFormRequest $request)
    {
        $validatedData = $request->validated();

        $result = DB::transaction(function () use ($validatedData) {
            $dtoToCreate = new CreatePatientMedicineDTO(
                patient_id: $validatedData['patient_id'],
                medicine_id: $validatedData['medicine_id'],
                current_quantity: $validatedData['current_quantity'] ?? 0,
                minimum_quantity: $validatedData['minimum_quantity'] ?? null,
            );

            $patientMedicineService = PatientMedicineService::create($dtoToCreate);

            return new PatientMedicineResource($patientMedicineService->getRecord());
        });

        return $result->response()->setStatusCode(201);
    }

    public function show(string $patientMedicine)
    {
        $patientMedicineService = PatientMedicineService::find($patientMedicine);

        return PatientMedicineResource::make($patientMedicineService->getRecord());
    }

    public function update(UpdatePatientMedicineFormRequest $request, string $patientMedicine)
    {
        $validatedData = $request->validated();

        $result = DB::transaction(function () use ($validatedData, $patientMedicine) {
            $dtoToUpdate = new UpdatePatientMedicineDTO(
                id: $patientMedicine,
                minimum_quantity: $validatedData['minimum_quantity'] ?? null,
            );

            $patientMedicineService = PatientMedicineService::find($patientMedicine);
            $patientMedicineService->update($dtoToUpdate);

            return PatientMedicineResource::make($patientMedicineService->getRecord());
        });

        return $result;
    }

    public function destroy(string $patientMedicine)
    {
        $patientMedicineService = PatientMedicineService::find($patientMedicine);
        $patientMedicineService->delete();

        return response()->noContent();
    }

    public function restore(string $patientMedicine)
    {
        $record = PatientMedicine::onlyTrashed()->findOrFail($patientMedicine);
        $record->restore();

        return PatientMedicineResource::make($record->fresh());
    }

    public function forceDelete(string $patientMedicine)
    {
        $record = PatientMedicine::withTrashed()->findOrFail($patientMedicine);
        $record->forceDelete();

        return response()->noContent();
    }

    public function movements(string $patientMedicine)
    {
        $patientMedicineService = PatientMedicineService::find($patientMedicine);

        return PatientMedicineMovementResource::collection(
            $patientMedicineService->getRecord()->movements()->latest('movement_date')->paginate(10)
        );
    }

    public function storeMovement(CreatePatientMedicineMovementFormRequest $request, string $patientMedicine)
    {
        $validatedData = $request->validated();

        $result = DB::transaction(function () use ($validatedData, $patientMedicine) {
            $dtoToCreate = new CreatePatientMedicineMovementDTO(
                patient_medicine_id: $patientMedicine,
                type: StockMovementTypeEnum::from($validatedData['type']),
                quantity: $validatedData['quantity'],
                user_id: auth()->id(),
                notes: $validatedData['notes'] ?? null,
                movement_date: isset($validatedData['movement_date']) ? Carbon::parse($validatedData['movement_date']) : Carbon::now(),
            );

            $movementService = PatientMedicineMovementService::create($dtoToCreate);

            return new PatientMedicineMovementResource($movementService->getRecord());
        });

        return $result->response()->setStatusCode(201);
    }
}
