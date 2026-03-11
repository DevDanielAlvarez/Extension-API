<?php

namespace App\Http\Controllers;

use App\DTO\Patient\CreatePatientDTO;
use App\DTO\Patient\UpdatePatientDTO;
use App\Enums\DocumentTypeEnum;
use App\Http\Requests\Patient\CreatePatientFormRequest;
use App\Http\Requests\Patient\UpdatePatientFormRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use App\Services\Patient\PatientService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    public function index()
    {
        return PatientResource::collection(Patient::paginate(10));
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

}
