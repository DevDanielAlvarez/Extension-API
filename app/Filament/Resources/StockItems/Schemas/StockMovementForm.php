<?php

namespace App\Filament\Resources\StockItems\Schemas;

use App\DTO\StockMovement\CreateStockMovementDTO;
use App\Enums\StockMovementTypeEnum;
use App\Models\Patient;
use App\Models\StockItem;
use App\Services\StockMovement\StockMovementService;
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class StockMovementForm
{
    /**
     * Used from the stock item's own screen: the item is fixed (owner record),
     * so the form lets you pick the patient instead.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            self::typeField(),
            self::quantityField(),
            self::patientField(),
            self::batchField(),
            self::expiryDateField(),
            self::movementDateField(),
            self::notesField(),
        ]);
    }

    /**
     * Used from a patient's own screen: the patient is fixed (owner record),
     * so the form lets you pick which stock item was lent/returned. Restricted
     * to OUT/RETURNED — entradas e ajustes de inventário não fazem sentido no
     * contexto de um paciente específico.
     */
    public static function configureForPatient(Schema $schema): Schema
    {
        return $schema->components([
            self::stockItemField(),
            self::typeField(onlyPatientRelevantTypes: true),
            self::quantityField(),
            self::movementDateField(),
            self::notesField(),
        ]);
    }

    protected static function typeField(bool $onlyPatientRelevantTypes = false): Select
    {
        $options = $onlyPatientRelevantTypes
            ? [
                StockMovementTypeEnum::OUT->value => 'Emprestar / entregar',
                StockMovementTypeEnum::RETURNED->value => 'Devolução',
            ]
            : [
                StockMovementTypeEnum::IN->value => 'Entrada',
                StockMovementTypeEnum::OUT->value => 'Saída / consumo',
                StockMovementTypeEnum::RETURNED->value => 'Devolução',
                StockMovementTypeEnum::ADJUSTMENT->value => 'Ajuste de inventário',
            ];

        return Select::make('type')
            ->label('Tipo')
            ->options($options)
            ->native(false)
            ->required()
            ->live();
    }

    protected static function quantityField(): TextInput
    {
        return TextInput::make('quantity')
            ->label('Quantidade')
            ->numeric()
            ->minValue(0)
            ->required()
            ->helperText(fn (Get $get): string => $get('type') === StockMovementTypeEnum::ADJUSTMENT->value
                ? 'Novo saldo total, após a contagem física do item.'
                : 'Quantidade movimentada.');
    }

    protected static function stockItemField(): Select
    {
        return Select::make('stock_item_id')
            ->label('Item de estoque')
            ->options(fn () => StockItem::query()->orderBy('name')->pluck('name', 'id'))
            ->searchable()
            ->preload()
            ->native(false)
            ->required();
    }

    protected static function patientField(): Select
    {
        return Select::make('patient_id')
            ->label('Paciente')
            ->options(fn () => Patient::query()->orderBy('name')->pluck('name', 'id'))
            ->searchable()
            ->preload()
            ->native(false)
            ->visible(fn (Get $get): bool => in_array($get('type'), [
                StockMovementTypeEnum::OUT->value,
                StockMovementTypeEnum::RETURNED->value,
            ], true))
            ->helperText('Obrigatório para itens de enxoval do residente.');
    }

    protected static function batchField(): TextInput
    {
        return TextInput::make('batch')
            ->label('Lote')
            ->visible(fn (Get $get): bool => $get('type') === StockMovementTypeEnum::IN->value)
            ->helperText('Obrigatório quando o item exige controle de lote.');
    }

    protected static function expiryDateField(): DateTimePicker
    {
        return DateTimePicker::make('expiry_date')
            ->label('Validade')
            ->native(false)
            ->visible(fn (Get $get): bool => $get('type') === StockMovementTypeEnum::IN->value);
    }

    protected static function movementDateField(): DateTimePicker
    {
        return DateTimePicker::make('movement_date')
            ->label('Data/hora da movimentação')
            ->native(false)
            ->default(now())
            ->helperText('Pode ser retroativa. Se vazio, usa o momento atual.');
    }

    protected static function notesField(): Textarea
    {
        return Textarea::make('notes')
            ->label('Observações')
            ->rows(3)
            ->columnSpanFull();
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public static function create(string $stockItemId, array $data): void
    {
        DB::transaction(function () use ($stockItemId, $data): void {
            StockMovementService::create(new CreateStockMovementDTO(
                stock_item_id: $stockItemId,
                type: StockMovementTypeEnum::from($data['type']),
                quantity: (int) $data['quantity'],
                patient_id: $data['patient_id'] ?? null,
                user_id: auth()->id(),
                batch: $data['batch'] ?? null,
                expiry_date: isset($data['expiry_date']) ? Carbon::parse($data['expiry_date']) : null,
                notes: $data['notes'] ?? null,
                movement_date: isset($data['movement_date']) ? Carbon::parse($data['movement_date']) : Carbon::now(),
            ));
        });
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public static function createForPatient(string $patientId, array $data): void
    {
        DB::transaction(function () use ($patientId, $data): void {
            StockMovementService::create(new CreateStockMovementDTO(
                stock_item_id: $data['stock_item_id'],
                type: StockMovementTypeEnum::from($data['type']),
                quantity: (int) $data['quantity'],
                patient_id: $patientId,
                user_id: auth()->id(),
                batch: null,
                expiry_date: null,
                notes: $data['notes'] ?? null,
                movement_date: isset($data['movement_date']) ? Carbon::parse($data['movement_date']) : Carbon::now(),
            ));
        });
    }
}
