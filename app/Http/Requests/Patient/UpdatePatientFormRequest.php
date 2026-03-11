<?php

namespace App\Http\Requests\Patient;

use App\Enums\DocumentTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdatePatientFormRequest extends FormRequest
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
                Rule::unique('patients', 'document_number')
                    ->where('document_type', $this->input('document_type'))
                    ->ignore($this->route('patient')),
            ],
            'admission_date' => 'required|date',
            'birthday' => 'required|date',
            'phone' => 'nullable|string|max:30',
            'nursing_report' => 'nullable|array',
        ];
    }
}
