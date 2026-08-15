<?php

namespace App\Http\Requests\StockItem;

use App\Enums\StockItemCategoryEnum;
use App\Enums\StockItemUnitEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateStockItemFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category' => ['required', new Enum(StockItemCategoryEnum::class)],
            'unit' => ['required', new Enum(StockItemUnitEnum::class)],
            'minimum_quantity' => 'nullable|integer|min:0',
            'requires_batch_control' => 'nullable|boolean',
            'additional_information' => 'nullable|string',
        ];
    }
}
