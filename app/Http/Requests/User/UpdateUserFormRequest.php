<?php

namespace App\Http\Requests\User;

use App\Enums\DocumentTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class UpdateUserFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'document_type' => ['required', new Enum(DocumentTypeEnum::class)],
            'document_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'document_number')
                    ->where('document_type', $this->input('document_type'))
                    ->ignore($this->user()->id),
            ],
            'password' => ['nullable', 'string', Password::min(8)->symbols()],
        ];
    }
}