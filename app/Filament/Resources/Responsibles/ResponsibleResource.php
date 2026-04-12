<?php

namespace App\Filament\Resources\Responsibles;

use App\Filament\Resources\Responsibles\Pages\CreateResponsible;
use App\Filament\Resources\Responsibles\Pages\EditResponsible;
use App\Filament\Resources\Responsibles\Pages\ListResponsibles;
use App\Filament\Resources\Responsibles\Schemas\ResponsibleForm;
use App\Filament\Resources\Responsibles\Tables\ResponsiblesTable;
use App\Models\Responsible;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ResponsibleResource extends Resource
{
    protected static ?string $model = Responsible::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('Responsável');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Responsáveis');
    }

    public static function form(Schema $schema): Schema
    {
        return ResponsibleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ResponsiblesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListResponsibles::route('/'),
            'create' => CreateResponsible::route('/create'),
            'edit' => EditResponsible::route('/{record}/edit'),
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
