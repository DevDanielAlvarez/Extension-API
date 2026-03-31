<?php

namespace App\Filament\Widgets;

use App\Models\PrescriptionSchedule;
use Filament\Widgets\ChartWidget;

class TodayMedicationByHourChart extends ChartWidget
{
    protected string | array | int $columnSpan = 1;

    protected ?string $heading = null;

    public function getHeading(): ?string
    {
        return __('Applications by hour today');
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $today = today();

        $hourBuckets = collect(range(0, 23))
            ->mapWithKeys(fn (int $hour): array => [$hour => 0]);

        $times = PrescriptionSchedule::query()
            ->where('day_of_week', $today->dayOfWeek)
            ->whereHas('prescription', function ($query) use ($today): void {
                $query->whereDate('start_date', '<=', $today)
                    ->where(function ($subQuery) use ($today): void {
                        $subQuery->whereNull('end_date')
                            ->orWhereDate('end_date', '>=', $today);
                    });
            })
            ->pluck('time');

        foreach ($times as $time) {
            $hour = (int) substr((string) $time, 0, 2);

            if (! $hourBuckets->has($hour)) {
                continue;
            }

            $hourBuckets->put($hour, $hourBuckets->get($hour, 0) + 1);
        }

        return [
            'datasets' => [
                [
                    'label' => __('Scheduled doses'),
                    'data' => $hourBuckets->values()->toArray(),
                    'fill' => true,
                ],
            ],
            'labels' => $hourBuckets
                ->keys()
                ->map(fn (int $hour): string => str_pad((string) $hour, 2, '0', STR_PAD_LEFT) . ':00')
                ->toArray(),
        ];
    }
}
