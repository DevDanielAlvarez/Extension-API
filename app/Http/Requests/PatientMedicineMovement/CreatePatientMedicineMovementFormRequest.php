<?php

namespace App\Http\Requests\PatientMedicineMovement;

use App\Enums\StockMovementTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CreatePatientMedicineMovementFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', new Enum(StockMovementTypeEnum::class)],
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'movement_date' => 'nullable|date',
        ];
    }
}
