<?php

namespace App\Filament\Widgets;

use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionSchedule;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MedicationStatsOverview extends StatsOverviewWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $today = today();

        $createdSeries = collect(range(6, 0))
            ->map(function (int $offset) use ($today): float {
                return (float) Prescription::query()
                    ->whereDate('created_at', $today->copy()->subDays($offset))
                    ->count();
            })
            ->toArray();

        $activePrescriptions = Prescription::query()
            ->whereDate('start_date', '<=', $today)
            ->where(function ($query) use ($today): void {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $today);
            })
            ->count();

        $todaySchedulesQuery = PrescriptionSchedule::query()
            ->where('day_of_week', $today->dayOfWeek)
            ->whereHas('prescription', function ($query) use ($today): void {
                $query->whereDate('start_date', '<=', $today)
                    ->where(function ($subQuery) use ($today): void {
                        $subQuery->whereNull('end_date')
                            ->orWhereDate('end_date', '>=', $today);
                    });
            });

        $dosesToday = (clone $todaySchedulesQuery)->count();

        $patientsWithDosesToday = (clone $todaySchedulesQuery)
            ->join('prescriptions', 'prescription_schedules.prescription_id', '=', 'prescriptions.id')
            ->distinct('prescriptions.patient_id')
            ->count('prescriptions.patient_id');

        return [
            Stat::make(__('Total patients'), number_format((int) Patient::query()->count(), 0, ',', '.'))
                ->description(__('Registered in the system'))
                ->color('info'),
            Stat::make(__('Active prescriptions'), number_format((int) $activePrescriptions, 0, ',', '.'))
                ->description(__('Valid for today'))
                ->chart($createdSeries)
                ->color('success'),
            Stat::make(__('Doses scheduled today'), number_format((int) $dosesToday, 0, ',', '.'))
                ->description(__('Applications expected in 24h'))
                ->color('warning'),
            Stat::make(__('Patients with doses today'), number_format((int) $patientsWithDosesToday, 0, ',', '.'))
                ->description(__('Patients with at least one schedule today'))
                ->color('primary'),
        ];
    }
}
