<?php

namespace App\Filament\Resources\PatientMedicines;

use App\Filament\Resources\PatientMedicines\Pages\CreatePatientMedicine;
use App\Filament\Resources\PatientMedicines\Pages\EditPatientMedicine;
use App\Filament\Resources\PatientMedicines\Pages\ListPatientMedicines;
use App\Filament\Resources\PatientMedicines\RelationManagers\PatientMedicineMovementsRelationManager;
use App\Filament\Resources\PatientMedicines\Schemas\PatientMedicineForm;
use App\Filament\Resources\PatientMedicines\Tables\PatientMedicinesTable;
use App\Models\PatientMedicine;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PatientMedicineResource extends Resource
{
    protected static ?string $model = PatientMedicine::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    public static function getNavigationGroup(): ?string
    {
        return __('Estoque');
    }

    public static function getModelLabel(): string
    {
        return __('Medicamento do Paciente');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Medicamentos dos Pacientes');
    }

    public static function form(Schema $schema): Schema
    {
        return PatientMedicineForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PatientMedicinesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PatientMedicineMovementsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPatientMedicines::route('/'),
            'create' => CreatePatientMedicine::route('/create'),
            'edit' => EditPatientMedicine::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
