<?php

namespace App\Filament\Resources\Patients\RelationManagers;

use App\Filament\Resources\PatientMedicines\Schemas\PatientMedicineForm;
use App\Filament\Resources\PatientMedicines\Schemas\PatientMedicineMovementForm;
use App\Models\PatientMedicine;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class PatientMedicinesRelationManager extends RelationManager
{
    protected static string $relationship = 'patientMedicines';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Medicamentos do Paciente');
    }

    public function form(Schema $schema): Schema
    {
        return PatientMedicineForm::configureForPatient($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Medicamentos do Paciente'))
            ->columns([
                TextColumn::make('medicine.name')
                    ->label('Medicamento')
                    ->searchable(),
                TextColumn::make('current_quantity')
                    ->label('Saldo atual')
                    ->numeric()
                    ->color(fn (PatientMedicine $record): string => $record->isLowStock() ? 'danger' : 'success')
                    ->weight(fn (PatientMedicine $record): string => $record->isLowStock() ? 'bold' : 'normal'),
                TextColumn::make('minimum_quantity')
                    ->label('Estoque mínimo')
                    ->numeric()
                    ->placeholder('—'),
            ])
            ->emptyStateHeading('Nenhum medicamento cadastrado para este paciente')
            ->emptyStateDescription('Adicione o primeiro medicamento clicando no botão acima.')
            ->headerActions([
                CreateAction::make()
                    ->label('Adicionar medicamento')
                    ->modalHeading('Adicionar medicamento')
                    ->schema(fn ($schema) => PatientMedicineForm::configureForPatient($schema))
                    ->action(function (array $data): void {
                        try {
                            PatientMedicineForm::createForPatient($this->getOwnerRecord()->id, $data);
                        } catch (ValidationException $e) {
                            Notification::make()
                                ->title('Não foi possível adicionar o medicamento')
                                ->body(collect($e->errors())->flatten()->implode(' '))
                                ->danger()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('Medicamento adicionado com sucesso')
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
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
            ]);
    }
}
