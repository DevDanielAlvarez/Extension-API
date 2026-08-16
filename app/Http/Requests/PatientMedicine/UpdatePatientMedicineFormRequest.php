<?php

namespace App\Http\Requests\PatientMedicine;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientMedicineFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'minimum_quantity' => 'nullable|integer|min:0',
        ];
    }
}
