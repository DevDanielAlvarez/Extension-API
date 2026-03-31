<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\MedicationStatsOverview;
use App\Filament\Widgets\TodayMedicationByHourChart;
use App\Filament\Widgets\TodayMedicationsTable;
use App\Filament\Widgets\WeeklyMedicationLoadChart;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedHome;

    public static function getNavigationLabel(): string
    {
        return __('Care Dashboard');
    }

    public function getTitle(): string | Htmlable
    {
        return __('Care Dashboard');
    }

    public function getWidgets(): array
    {
        return [
            MedicationStatsOverview::class,
            TodayMedicationsTable::class,
            WeeklyMedicationLoadChart::class,
            TodayMedicationByHourChart::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 2;
    }
}
