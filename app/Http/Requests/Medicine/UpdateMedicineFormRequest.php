<?php

namespace App\Http\Requests\Medicine;

use App\Enums\ContentUnitEnum;
use App\Enums\RouteOfAdministrationEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateMedicineFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'content_quantity' => 'required|integer|min:1',
            'content_unit' => ['required', new Enum(ContentUnitEnum::class)],
            'strength' => 'required|string|max:255',
            'is_compounded' => 'nullable|boolean',
            'route_of_administration' => ['required', new Enum(RouteOfAdministrationEnum::class)],
            'additional_information' => 'nullable|string',
        ];
    }
}
