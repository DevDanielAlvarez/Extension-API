<?php

namespace App\Http\Controllers;

use App\DTO\Patient\CreatePatientDTO;
use App\DTO\Patient\UpdatePatientDTO;
use App\DTO\Prescription\CreatePrescriptionDTO;
use App\DTO\PrescriptionSchedule\CreatePrescriptionScheduleDTO;
use App\Enums\DocumentTypeEnum;
use App\Http\Requests\Patient\CreatePatientFormRequest;
use App\Http\Requests\Patient\UpdatePatientFormRequest;
use App\Http\Resources\PatientResource;
use App\Http\Resources\PrescriptionResource;
use App\Http\Resources\ResponsibleResource;
use App\Models\Patient;
use App\Services\Patient\PatientService;
use App\Services\Prescription\PrescriptionService;
use App\Services\PrescriptionSchedule\PrescriptionScheduleService;
use App\Services\Responsible\ResponsibleService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    public function index()
    {
        return PatientResource::collection(Patient::paginate(10));
    }

    public function trashed()
    {
        return PatientResource::collection(Patient::onlyTrashed()->paginate(10));
    }

    public function store(CreatePatientFormRequest $request)
    {
        $validatedData = $request->validated();

        $result = DB::transaction(function () use ($validatedData) {
            $dtoToCreate = new CreatePatientDTO(
                name: $validatedData['name'],
                document_type: DocumentTypeEnum::from($validatedData['document_type']),
                document_number: $validatedData['document_number'],
                admission_date: Carbon::parse($validatedData['admission_date']),
                birthday: Carbon::parse($validatedData['birthday']),
                phone: $validatedData['phone'] ?? null,
                nursing_report: $validatedData['nursing_report'] ?? null,
            );

            $patientService = PatientService::create($dtoToCreate);

            return new PatientResource($patientService->getRecord());
        });

        return $result->response()->setStatusCode(201);
    }

    public function show(string $patient)
    {
        $patientService = PatientService::find($patient);

        return PatientResource::make($patientService->getRecord());
    }

    public function update(UpdatePatientFormRequest $request, string $patient)
    {
        $validatedData = $request->validated();

        $result = DB::transaction(function () use ($validatedData, $patient) {
            $dtoToUpdate = new UpdatePatientDTO(
                id: $patient,
                name: $validatedData['name'],
                document_type: DocumentTypeEnum::from($validatedData['document_type']),
                document_number: $validatedData['document_number'],
                admission_date: Carbon::parse($validatedData['admission_date']),
                birthday: Carbon::parse($validatedData['birthday']),
                phone: $validatedData['phone'] ?? null,
                nursing_report: $validatedData['nursing_report'] ?? null,
            );

            $patientService = PatientService::find($patient);
            $patientService->update($dtoToUpdate);

            return PatientResource::make($patientService->getRecord());
        });

        return $result;
    }

    public function attachResponsible(string $patient, string $responsible)
    {
        $patientService = PatientService::find($patient);
        ResponsibleService::find($responsible);

        $patientService->getRecord()->responsibles()->syncWithoutDetaching([$responsible]);

        return response()->noContent();
    }

    public function detachResponsible(string $patient, string $responsible)
    {
        $patientService = PatientService::find($patient);
        ResponsibleService::find($responsible);

        $patientService->getRecord()->responsibles()->detach($responsible);

        return response()->noContent();
    }

    public function responsibles(string $patient)
    {
        $patientService = PatientService::find($patient);

        return ResponsibleResource::collection(
            $patientService->getRecord()->responsibles()->paginate(10)
        );
    }

    public function prescriptions(string $patient)
    {
        $patientService = PatientService::find($patient);

        return PrescriptionResource::collection(
            $patientService->getRecord()->prescriptions()->paginate(10)
        );
    }

    public function storePrescription(Request $request, string $patient)
    {
        PatientService::find($patient);

        $validatedData = $request->validate([
            'medicine_id' => ['required', 'string', Rule::exists('medicines', 'id')],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'instructions' => ['nullable', 'string', 'max:1000'],
            'prescription_schedules' => ['nullable', 'array'],
            'prescription_schedules.*.day_of_week' => ['required_with:prescription_schedules', 'integer', 'between:0,6'],
            'prescription_schedules.*.time' => ['required_with:prescription_schedules', 'date_format:H:i'],
            'prescription_schedules.*.quantity' => ['required_with:prescription_schedules', 'integer', 'min:1'],
        ]);

        $result = DB::transaction(function () use ($validatedData, $patient) {
            $dtoToCreate = new CreatePrescriptionDTO(
                patient_id: $patient,
                medicine_id: $validatedData['medicine_id'],
                start_date: Carbon::parse($validatedData['start_date']),
                end_date: Carbon::parse($validatedData['end_date']),
                instructions: $validatedData['instructions'] ?? null,
            );

            $prescriptionService = PrescriptionService::create($dtoToCreate);

            foreach ($validatedData['prescription_schedules'] ?? [] as $schedule) {
                $dtoSchedule = new CreatePrescriptionScheduleDTO(
                    prescription_id: $prescriptionService->getRecord()->id,
                    day_of_week: $schedule['day_of_week'],
                    time: $schedule['time'],
                    quantity: $schedule['quantity'],
                );

                PrescriptionScheduleService::create($dtoSchedule);
            }

            return new PrescriptionResource($prescriptionService->getRecord());
        });

        return $result->response()->setStatusCode(201);
    }

    public function destroy(string $patient)
    {
        $patientService = PatientService::find($patient);
        $patientService->delete();

        return response()->noContent();
    }

    public function restore(string $patient)
    {
        $record = Patient::onlyTrashed()->findOrFail($patient);
        $record->restore();

        return PatientResource::make($record->fresh());
    }

    public function forceDelete(string $patient)
    {
        $record = Patient::withTrashed()->findOrFail($patient);
        $record->forceDelete();

        return response()->noContent();
    }

}
