<?php

namespace App\Filament\Resources\StockItems\Schemas;

use App\Enums\StockItemCategoryEnum;
use App\Enums\StockItemUnitEnum;
use App\Models\StockItem;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StockItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados do item')
                    ->columnSpanFull()
                    ->description('Enxoval do residente (travesseiro, edredom...) ou insumo médico (agulha, seringa...).')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->translateLabel()
                            ->placeholder('Ex.: Travesseiro')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Select::make('category')
                            ->label('Categoria')
                            ->options([
                                StockItemCategoryEnum::RESIDENT_SUPPLY->value => 'Enxoval do residente',
                                StockItemCategoryEnum::MEDICAL_SUPPLY->value => 'Insumo médico',
                            ])
                            ->native(false)
                            ->required()
                            ->helperText('Itens de enxoval exigem paciente vinculado nas saídas/devoluções.'),
                        Select::make('unit')
                            ->label('Unidade')
                            ->options([
                                StockItemUnitEnum::UNIT->value => 'Unidade',
                                StockItemUnitEnum::PAIR->value => 'Par',
                                StockItemUnitEnum::BOX->value => 'Caixa',
                                StockItemUnitEnum::PACK->value => 'Pacote',
                                StockItemUnitEnum::ML->value => 'Mililitro (ML)',
                                StockItemUnitEnum::G->value => 'Grama (G)',
                            ])
                            ->native(false)
                            ->required(),
                        TextInput::make('minimum_quantity')
                            ->label('Estoque mínimo')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Usado para sinalizar estoque baixo.'),
                        Toggle::make('requires_batch_control')
                            ->label('Exige controle de lote e validade')
                            ->inline(false)
                            ->onColor('warning')
                            ->offColor('gray')
                            ->helperText('Marque para insumos que precisam de rastreabilidade sanitária.')
                            ->default(false),
                        TextInput::make('current_quantity')
                            ->label('Estoque inicial')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required()
                            ->visible(fn (string $operation): bool => $operation === 'create')
                            ->helperText('Depois de criado, o saldo só muda através da aba "Movimentações".'),
                        Placeholder::make('current_quantity_display')
                            ->label('Saldo atual')
                            ->content(fn (?StockItem $record): string => $record
                                ? "{$record->current_quantity} ({$record->unit->value})"
                                : '-')
                            ->visible(fn (string $operation): bool => $operation === 'edit'),
                        Textarea::make('additional_information')
                            ->translateLabel()
                            ->rows(4)
                            ->placeholder('Observações adicionais...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
