<?php

namespace App\Http\Controllers;

use App\DTO\Prescription\CreatePrescriptionDTO;
use App\DTO\Prescription\UpdatePrescriptionDTO;
use App\Http\Requests\Prescription\CreatePrescriptionFormRequest;
use App\Http\Requests\Prescription\UpdatePrescriptionFormRequest;
use App\Http\Resources\PrescriptionResource;
use App\Http\Resources\PrescriptionScheduleResource;
use App\Models\Prescription;
use App\Services\Prescription\PrescriptionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PrescriptionResource::collection(Prescription::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePrescriptionFormRequest $request)
    {
        $validatedData = $request->validated();

        $result = DB::transaction(function () use ($validatedData) {
            $dtoToCreate = new CreatePrescriptionDTO(
                patient_id: $validatedData['patient_id'],
                medicine_id: $validatedData['medicine_id'],
                start_date: Carbon::parse($validatedData['start_date']),
                end_date: Carbon::parse($validatedData['end_date']),
                instructions: $validatedData['instructions'],
            );

            $prescriptionService = PrescriptionService::create($dtoToCreate);

            return new PrescriptionResource($prescriptionService->getRecord());
        });

        return $result->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $prescription)
    {
        $prescriptionService = PrescriptionService::find($prescription);

        return PrescriptionResource::make($prescriptionService->getRecord());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePrescriptionFormRequest $request, string $prescription)
    {
        $validatedData = $request->validated();

        $result = DB::transaction(function () use ($validatedData, $prescription) {
            $dtoToUpdate = new UpdatePrescriptionDTO(
                id: $prescription,
                patient_id: $validatedData['patient_id'],
                medicine_id: $validatedData['medicine_id'],
                start_date: Carbon::parse($validatedData['start_date']),
                end_date: Carbon::parse($validatedData['end_date']),
                instructions: $validatedData['instructions'],
            );

            $prescriptionService = PrescriptionService::find($prescription);
            $prescriptionService->update($dtoToUpdate);

            return PrescriptionResource::make($prescriptionService->getRecord());
        });

        return $result;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $prescriptionService = PrescriptionService::find($id);
        $prescriptionService->delete();

        return response()->noContent();
    }

    public function schedules(string $prescription)
    {
        $prescriptionService = PrescriptionService::find($prescription);

        return PrescriptionScheduleResource::collection(
            $prescriptionService->getRecord()->prescriptionSchedules()->paginate(10)
        );
    }
}
