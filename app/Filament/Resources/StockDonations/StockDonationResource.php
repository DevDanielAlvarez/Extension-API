<?php

namespace App\Filament\Resources\StockDonations;

use App\Enums\StockDonationStatusEnum;
use App\Filament\Resources\StockDonations\Pages\ListStockDonations;
use App\Filament\Resources\StockDonations\Tables\StockDonationsTable;
use App\Models\StockDonation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StockDonationResource extends Resource
{
    protected static ?string $model = StockDonation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    public static function getNavigationGroup(): ?string
    {
        return __('Estoque');
    }

    public static function getModelLabel(): string
    {
        return __('Doação de Item');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Doações de Itens');
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::query()
            ->where('status', StockDonationStatusEnum::PENDING)
            ->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function table(Table $table): Table
    {
        return StockDonationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockDonations::route('/'),
        ];
    }
}
