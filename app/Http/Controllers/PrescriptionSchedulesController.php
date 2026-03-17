<?php

namespace App\Http\Controllers;

use App\DTO\PrescriptionSchedule\CreatePrescriptionScheduleDTO;
use App\DTO\PrescriptionSchedule\UpdatePrescriptionScheduleDTO;
use App\Http\Requests\PrescriptionSchedule\CreatePrescriptionScheduleFormRequest;
use App\Http\Requests\PrescriptionSchedule\UpdatePrescriptionScheduleFormRequest;
use App\Http\Resources\PrescriptionScheduleResource;
use App\Models\PrescriptionSchedule;
use App\Services\PrescriptionSchedule\PrescriptionScheduleService;
use Illuminate\Support\Facades\DB;

class PrescriptionSchedulesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PrescriptionScheduleResource::collection(PrescriptionSchedule::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePrescriptionScheduleFormRequest $request)
    {
        $validatedData = $request->validated();

        $result = DB::transaction(function () use ($validatedData) {
            $dtoToCreate = new CreatePrescriptionScheduleDTO(
                prescription_id: $validatedData['prescription_id'],
                day_of_week: $validatedData['day_of_week'],
                time: $validatedData['time'],
                quantity: $validatedData['quantity'],
            );

            $scheduleService = PrescriptionScheduleService::create($dtoToCreate);

            return new PrescriptionScheduleResource($scheduleService->getRecord());
        });

        return $result->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $schedule)
    {
        $scheduleService = PrescriptionScheduleService::find($schedule);

        return PrescriptionScheduleResource::make($scheduleService->getRecord());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePrescriptionScheduleFormRequest $request, string $schedule)
    {
        $validatedData = $request->validated();

        $result = DB::transaction(function () use ($validatedData, $schedule) {
            $dtoToUpdate = new UpdatePrescriptionScheduleDTO(
                id: $schedule,
                prescription_id: $validatedData['prescription_id'],
                day_of_week: $validatedData['day_of_week'],
                time: $validatedData['time'],
                quantity: $validatedData['quantity'],
            );

            $scheduleService = PrescriptionScheduleService::find($schedule);
            $scheduleService->update($dtoToUpdate);

            return PrescriptionScheduleResource::make($scheduleService->getRecord());
        });

        return $result;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $scheduleService = PrescriptionScheduleService::find($id);
        $scheduleService->delete();

        return response()->noContent();
    }
}

