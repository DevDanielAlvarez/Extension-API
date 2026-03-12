<?php

namespace App\Http\Requests\PrescriptionSchedule;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePrescriptionScheduleFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prescription_id' => ['required', 'string', Rule::exists('prescriptions', 'id')],
            'day_of_week' => 'required|integer|between:0,6',
            'time' => 'required|date_format:H:i',
            'quantity' => 'required|integer|min:1',
        ];
    }
}
