<?php

namespace App\Filament\Resources\StockItems\RelationManagers;

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
    protected static string $relationship = 'movements';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Movimentações');
    }

    public function form(Schema $schema): Schema
    {
        return StockMovementForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Movimentações'))
            ->defaultSort('movement_date', 'desc')
            ->columns([
                TextColumn::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn ($state): string => match ($state instanceof \BackedEnum ? $state->value : $state) {
                        StockMovementTypeEnum::IN->value => 'Entrada',
                        StockMovementTypeEnum::OUT->value => 'Saída',
                        StockMovementTypeEnum::RETURNED->value => 'Devolução',
                        StockMovementTypeEnum::ADJUSTMENT->value => 'Ajuste',
                        default => (string) $state,
                    })
                    ->badge(),
                TextColumn::make('quantity')
                    ->label('Quantidade')
                    ->numeric(),
                TextColumn::make('patient.name')
                    ->label('Paciente')
                    ->placeholder('—'),
                TextColumn::make('user.name')
                    ->label('Registrado por')
                    ->placeholder('—'),
                TextColumn::make('batch')
                    ->label('Lote')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('expiry_date')
                    ->label('Validade')
                    ->date()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->emptyStateHeading('Nenhuma movimentação registrada')
            ->emptyStateDescription('Registre a primeira movimentação clicando no botão acima.')
            ->headerActions([
                CreateAction::make()
                    ->label('Registrar movimentação')
                    ->modalHeading('Registrar movimentação')
                    ->schema(fn ($schema) => StockMovementForm::configure($schema))
                    ->action(function (array $data): void {
                        try {
                            StockMovementForm::create($this->getOwnerRecord()->id, $data);
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
