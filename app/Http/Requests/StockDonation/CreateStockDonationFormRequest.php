<?php

namespace App\Http\Requests\StockDonation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateStockDonationFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('patient_document_number')) {
            $this->merge([
                'patient_document_number' => preg_replace('/\D/', '', (string) $this->input('patient_document_number')),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'patient_document_number' => ['required', 'digits:11'],
            'stock_item_id' => ['required', 'string', Rule::exists('stock_items', 'id')],
            'quantity' => ['required', 'integer', 'min:1'],
            'donor_name' => ['required', 'string', 'max:255'],
            'donor_phone' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'patient_document_number' => 'CPF do residente',
            'stock_item_id' => 'item',
            'quantity' => 'quantidade',
            'donor_name' => 'seu nome',
            'donor_phone' => 'telefone',
        ];
    }

    public function messages(): array
    {
        return [
            'patient_document_number.digits' => 'Informe um CPF válido, com 11 números.',
        ];
    }
}
