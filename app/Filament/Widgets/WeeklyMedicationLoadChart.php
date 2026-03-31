<?php

namespace App\Filament\Widgets;

use App\Enums\DayOfWeekEnum;
use App\Models\PrescriptionSchedule;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class WeeklyMedicationLoadChart extends ChartWidget
{
    protected string | array | int $columnSpan = 1;

    protected ?string $heading = null;

    public function getHeading(): ?string
    {
        return __('Weekly medication load');
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $start = today();

        $labels = [];
        $values = [];

        foreach (range(0, 6) as $offset) {
            $date = $start->copy()->addDays($offset);

            $labels[] = DayOfWeekEnum::getOptions()[$date->dayOfWeek] ?? $date->translatedFormat('l');
            $values[] = $this->countSchedulesForDate($date);
        }

        return [
            'datasets' => [
                [
                    'label' => __('Scheduled doses'),
                    'data' => $values,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function countSchedulesForDate(Carbon $date): int
    {
        return PrescriptionSchedule::query()
            ->where('day_of_week', $date->dayOfWeek)
            ->whereHas('prescription', function ($query) use ($date): void {
                $query->whereDate('start_date', '<=', $date)
                    ->where(function ($subQuery) use ($date): void {
                        $subQuery->whereNull('end_date')
                            ->orWhereDate('end_date', '>=', $date);
                    });
            })
            ->count();
    }
}
