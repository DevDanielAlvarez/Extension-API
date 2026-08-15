<?php

namespace App\Filament\Resources\StockItems;

use App\Filament\Resources\StockItems\Pages\CreateStockItem;
use App\Filament\Resources\StockItems\Pages\EditStockItem;
use App\Filament\Resources\StockItems\Pages\ListStockItems;
use App\Filament\Resources\StockItems\RelationManagers\StockMovementsRelationManager;
use App\Filament\Resources\StockItems\Schemas\StockItemForm;
use App\Filament\Resources\StockItems\Tables\StockItemsTable;
use App\Models\StockItem;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StockItemResource extends Resource
{
    protected static ?string $model = StockItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBoxArrowDown;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return __('Estoque');
    }

    public static function getModelLabel(): string
    {
        return __('Item de Estoque');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Itens de Estoque');
    }

    public static function form(Schema $schema): Schema
    {
        return StockItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockItemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            StockMovementsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockItems::route('/'),
            'create' => CreateStockItem::route('/create'),
            'edit' => EditStockItem::route('/{record}/edit'),
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
