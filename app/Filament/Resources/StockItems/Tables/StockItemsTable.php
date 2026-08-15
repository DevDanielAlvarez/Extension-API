<?php

namespace App\Filament\Resources\StockItems\Tables;

use App\Enums\StockItemCategoryEnum;
use App\Filament\Resources\StockItems\Schemas\StockMovementForm;
use App\Models\StockItem;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class StockItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->translateLabel()
                    ->searchable(),
                TextColumn::make('category')
                    ->label('Categoria')
                    ->formatStateUsing(fn ($state): string => match ($state instanceof \BackedEnum ? $state->value : $state) {
                        StockItemCategoryEnum::RESIDENT_SUPPLY->value => 'Enxoval do residente',
                        StockItemCategoryEnum::MEDICAL_SUPPLY->value => 'Insumo médico',
                        default => (string) $state,
                    })
                    ->badge(),
                TextColumn::make('unit')
                    ->label('Unidade')
                    ->badge(),
                TextColumn::make('current_quantity')
                    ->label('Saldo atual')
                    ->numeric()
                    ->sortable()
                    ->color(fn (StockItem $record): string => $record->isLowStock() ? 'danger' : 'success')
                    ->weight(fn (StockItem $record): string => $record->isLowStock() ? 'bold' : 'normal'),
                TextColumn::make('minimum_quantity')
                    ->label('Estoque mínimo')
                    ->numeric()
                    ->sortable()
                    ->placeholder('—'),
                IconColumn::make('requires_batch_control')
                    ->label('Controle de lote')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->emptyStateHeading('Nenhum item de estoque cadastrado')
            ->emptyStateDescription('Cadastre o primeiro item clicando no botão acima.')
            ->filters([
                SelectFilter::make('category')
                    ->label('Categoria')
                    ->options([
                        StockItemCategoryEnum::RESIDENT_SUPPLY->value => 'Enxoval do residente',
                        StockItemCategoryEnum::MEDICAL_SUPPLY->value => 'Insumo médico',
                    ]),
                Filter::make('low_stock')
                    ->label('Estoque baixo')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereNotNull('minimum_quantity')
                        ->whereColumn('current_quantity', '<=', 'minimum_quantity')),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('registerMovement')
                        ->label('Registrar movimentação')
                        ->icon('heroicon-o-arrow-path')
                        ->schema(fn ($schema) => StockMovementForm::configure($schema))
                        ->action(function (array $data, StockItem $record): void {
                            try {
                                StockMovementForm::create($record->id, $data);
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
                    EditAction::make(),
                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make()
                        ->modalHeading('Excluir item de estoque permanentemente')
                        ->modalDescription('Todos os dados desse item serão excluídos permanentemente.'),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
