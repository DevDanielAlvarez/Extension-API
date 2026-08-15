<?php

namespace App\Http\Controllers;

use App\DTO\StockDonation\CreateStockDonationDTO;
use App\Enums\DocumentTypeEnum;
use App\Http\Requests\StockDonation\CreateStockDonationFormRequest;
use App\Models\Patient;
use App\Models\StockItem;
use App\Services\StockDonation\StockDonationService;

class StockDonationPublicController extends Controller
{
    public function create()
    {
        return view('stock-donations.create', [
            'stockItems' => StockItem::query()->orderBy('name')->get(),
        ]);
    }

    public function store(CreateStockDonationFormRequest $request)
    {
        $validated = $request->validated();

        $patient = Patient::query()
            ->where('document_type', DocumentTypeEnum::CPF)
            ->where('document_number', $validated['patient_document_number'])
            ->first();

        if (! $patient) {
            return back()
                ->withInput()
                ->withErrors(['patient_document_number' => 'CPF não encontrado. Confira o número ou fale com a recepção.']);
        }

        StockDonationService::create(new CreateStockDonationDTO(
            stock_item_id: $validated['stock_item_id'],
            patient_id: $patient->id,
            quantity: (int) $validated['quantity'],
            donor_name: $validated['donor_name'],
            donor_phone: $validated['donor_phone'] ?? null,
            notes: $validated['notes'] ?? null,
        ));

        return redirect()
            ->route('stock-donations.create')
            ->with('success', 'Obrigado! Seu item foi registrado e será conferido pela nossa equipe na recepção.');
    }
}
