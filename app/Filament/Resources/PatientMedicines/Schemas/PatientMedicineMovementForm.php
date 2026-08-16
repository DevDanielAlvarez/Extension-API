<?php

namespace App\Filament\Resources\PatientMedicines\Schemas;

use App\DTO\PatientMedicineMovement\CreatePatientMedicineMovementDTO;
use App\Enums\StockMovementTypeEnum;
use App\Services\PatientMedicineMovement\PatientMedicineMovementService;
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class PatientMedicineMovementForm
{
    /**
     * The owning PatientMedicine is always fixed (either the resource's own
     * record or a specific row picked on the patient's screen), so this form
     * only asks about the movement itself.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('type')
                ->label('Tipo')
                ->options([
                    StockMovementTypeEnum::IN->value => 'Entrada / reposição',
                    StockMovementTypeEnum::OUT->value => 'Saída / consumo',
                    StockMovementTypeEnum::RETURNED->value => 'Devolução',
                    StockMovementTypeEnum::ADJUSTMENT->value => 'Ajuste de inventário',
                ])
                ->native(false)
                ->required()
                ->live(),
            TextInput::make('quantity')
                ->label('Quantidade')
                ->numeric()
                ->minValue(0)
                ->required()
                ->helperText(fn ($get): string => $get('type') === StockMovementTypeEnum::ADJUSTMENT->value
                    ? 'Novo saldo total, após a contagem física.'
                    : 'Quantidade movimentada.'),
            DateTimePicker::make('movement_date')
                ->label('Data/hora da movimentação')
                ->native(false)
                ->default(now())
                ->helperText('Pode ser retroativa. Se vazio, usa o momento atual.'),
            Textarea::make('notes')
                ->label('Observações')
                ->rows(3)
                ->columnSpanFull(),
        ]);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public static function create(string $patientMedicineId, array $data): void
    {
        DB::transaction(function () use ($patientMedicineId, $data): void {
            PatientMedicineMovementService::create(new CreatePatientMedicineMovementDTO(
                patient_medicine_id: $patientMedicineId,
                type: StockMovementTypeEnum::from($data['type']),
                quantity: (int) $data['quantity'],
                user_id: auth()->id(),
                notes: $data['notes'] ?? null,
                movement_date: isset($data['movement_date']) ? Carbon::parse($data['movement_date']) : Carbon::now(),
            ));
        });
    }
}
