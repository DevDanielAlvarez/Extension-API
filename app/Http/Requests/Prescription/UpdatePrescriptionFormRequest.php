<?php

namespace App\Http\Requests\Prescription;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePrescriptionFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'string', Rule::exists('patients', 'id')],
            'medicine_id' => ['required', 'string', Rule::exists('medicines', 'id')],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'instructions' => 'string|max:1000',
        ];
    }
}
