<?php

namespace App\Http\Requests\Responsible;

use App\Enums\DocumentTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateResponsibleFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'document_type' => ['required', new Enum(DocumentTypeEnum::class)],
            'document_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('responsibles', 'document_number')
                    ->where('document_type', $this->input('document_type'))
                    ->ignore($this->route('responsible')),
            ],
            'phone' => 'nullable|string|max:30',
        ];
    }
}
