<?php

namespace App\Http\Requests;

use App\Enums\DocumentTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class LoginFormRequest extends FormRequest
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
            'document_type' => [new Enum(DocumentTypeEnum::class), 'required'],
            'document_number' => 'required|string|max:255',
            'password' => 'required|string',
        ];
    }
}
