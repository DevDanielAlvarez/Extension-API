<?php

namespace App\Http\Requests\StockMovement;

use App\Enums\StockMovementTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class CreateStockMovementFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', new Enum(StockMovementTypeEnum::class)],
            'quantity' => 'required|integer|min:0',
            'patient_id' => ['nullable', 'string', Rule::exists('patients', 'id')],
            'batch' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'movement_date' => 'nullable|date',
        ];
    }
}
