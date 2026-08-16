<?php

namespace App\Http\Requests\PatientMedicine;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePatientMedicineFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'string', Rule::exists('patients', 'id')],
            'medicine_id' => [
                'required',
                'string',
                Rule::exists('medicines', 'id'),
                Rule::unique('patient_medicines')->where(fn ($query) => $query->where('patient_id', $this->input('patient_id'))),
            ],
            'current_quantity' => 'nullable|integer|min:0',
            'minimum_quantity' => 'nullable|integer|min:0',
        ];
    }
}
