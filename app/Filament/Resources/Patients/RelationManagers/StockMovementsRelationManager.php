<?php

namespace App\Filament\Resources\Patients\RelationManagers;

use App\Enums\StockMovementTypeEnum;
use App\Filament\Resources\StockItems\Schemas\StockMovementForm;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class StockMovementsRelationManager extends RelationManager
{
    protected static string $relationship = 'stockMovements';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Movimentações de Estoque');
    }

    public function form(Schema $schema): Schema
    {
        return StockMovementForm::configureForPatient($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Movimentações de Estoque'))
            ->defaultSort('movement_date', 'desc')
            ->columns([
                TextColumn::make('stockItem.name')
                    ->label('Item'),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn ($state): string => match ($state instanceof \BackedEnum ? $state->value : $state) {
                        StockMovementTypeEnum::IN->value => 'Entrada',
                        StockMovementTypeEnum::OUT->value => 'Emprestado / entregue',
                        StockMovementTypeEnum::RETURNED->value => 'Devolução',
                        StockMovementTypeEnum::ADJUSTMENT->value => 'Ajuste',
                        default => (string) $state,
                    })
                    ->badge(),
                TextColumn::make('quantity')
                    ->label('Quantidade')
                    ->numeric(),
                TextColumn::make('user.name')
                    ->label('Registrado por')
                    ->placeholder('—'),
                TextColumn::make('notes')
                    ->label('Observações')
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('movement_date')
                    ->label('Data')
                    ->dateTime()
                    ->sortable(),
            ])
            ->emptyStateHeading('Nenhuma movimentação registrada para este paciente')
            ->emptyStateDescription('Registre um empréstimo de item (ex.: travesseiro, edredom) clicando no botão acima.')
            ->headerActions([
                CreateAction::make()
                    ->label('Registrar movimentação')
                    ->modalHeading('Registrar movimentação')
                    ->schema(fn ($schema) => StockMovementForm::configureForPatient($schema))
                    ->action(function (array $data): void {
                        try {
                            StockMovementForm::createForPatient($this->getOwnerRecord()->id, $data);
                        } catch (ValidationException $e) {
                            Notification::make()
                                ->title('Não foi possível registrar a movimentação')
                                ->body(collect($e->errors())->flatten()->implode(' '))
                                ->danger()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('Movimentação registrada com sucesso')
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                //
            ]);
    }
}
