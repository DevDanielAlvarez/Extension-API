<?php

namespace App\Filament\Widgets;

use App\Models\PrescriptionSchedule;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class TodayMedicationsTable extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    protected ?string $pollingInterval = '30s';

    protected function getTableHeading(): ?string
    {
        return __('Medications to apply today');
    }

    public function table(Table $table): Table
    {
        $today = today();

        return $table
            ->query(
                PrescriptionSchedule::query()
                    ->with(['prescription.patient', 'prescription.medicine'])
                    ->where('day_of_week', $today->dayOfWeek)
                    ->whereHas('prescription', function (Builder $query) use ($today): void {
                        $query->whereDate('start_date', '<=', $today)
                            ->where(function (Builder $subQuery) use ($today): void {
                                $subQuery->whereNull('end_date')
                                    ->orWhereDate('end_date', '>=', $today);
                            });
                    })
                    ->orderBy('time')
            )
            ->columns([
                TextColumn::make('time')
                    ->translateLabel()
                    ->badge()
                    ->sortable(),
                TextColumn::make('prescription.patient.name')
                    ->translateLabel()
                    ->searchable(),
                TextColumn::make('prescription.medicine.name')
                    ->translateLabel()
                    ->searchable(),
                TextColumn::make('quantity')
                    ->translateLabel()
                    ->sortable(),
                TextColumn::make('prescription.instructions')
                    ->label(__('Instructions'))
                    ->limit(50)
                    ->toggleable(),
            ])
            ->defaultSort('time');
    }
}
