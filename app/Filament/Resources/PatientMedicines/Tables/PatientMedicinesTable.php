<?php

namespace App\Filament\Resources\PatientMedicines\Tables;

use App\Filament\Resources\PatientMedicines\Schemas\PatientMedicineMovementForm;
use App\Models\PatientMedicine;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class PatientMedicinesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('patient.name')
                    ->label('Paciente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('medicine.name')
                    ->label('Medicamento')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('current_quantity')
                    ->label('Saldo atual')
                    ->numeric()
                    ->sortable()
                    ->color(fn (PatientMedicine $record): string => $record->isLowStock() ? 'danger' : 'success')
                    ->weight(fn (PatientMedicine $record): string => $record->isLowStock() ? 'bold' : 'normal'),
                TextColumn::make('minimum_quantity')
                    ->label('Estoque mínimo')
                    ->numeric()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->emptyStateHeading('Nenhum medicamento cadastrado para pacientes')
            ->emptyStateDescription('Cadastre o primeiro saldo clicando no botão acima.')
            ->filters([
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
                        ->schema(fn ($schema) => PatientMedicineMovementForm::configure($schema))
                        ->action(function (array $data, PatientMedicine $record): void {
                            try {
                                PatientMedicineMovementForm::create($record->id, $data);
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
                        ->modalHeading('Excluir saldo permanentemente')
                        ->modalDescription('Todos os dados desse saldo serão excluídos permanentemente.'),
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
