<?php

namespace App\Filament\Widgets;

use App\Filament\Support\QuickCreateActions;
use App\Models\MedicationAdministration;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\PrescriptionSchedule;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
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
                    ->select('prescription_schedules.*')
                    ->addSelect([
                        'administration_applied_at' => MedicationAdministration::query()
                            ->select('applied_at')
                            ->whereColumn('prescription_schedule_id', 'prescription_schedules.id')
                            ->where('scheduled_date', $today->toDateString()),
                    ])
                    ->with(['prescription.patient', 'prescription.medicine'])
                    ->where('day_of_week', $today->dayOfWeek)
                    ->whereHas('prescription', function (Builder $query) use ($today): void {
                        $query->whereDate('start_date', '<=', $today)
                            ->where(function (Builder $subQuery) use ($today): void {
                                $subQuery->whereNull('end_date')
                                    ->orWhereDate('end_date', '>=', $today);
                            });
                    })
                    ->orderByRaw('administration_applied_at is null desc')
            )
            ->columns([
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->getStateUsing(fn (PrescriptionSchedule $record): string => static::resolveStatus($record, $today))
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'applied' => __('Applied'),
                        'late' => __('Late'),
                        default => __('Pending'),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'applied' => 'success',
                        'late' => 'danger',
                        default => 'gray',
                    }),
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
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('patient')
                    ->schema([
                        \Filament\Forms\Components\Select::make('patient_id')
                            ->label(__('Patient'))
                            ->native(false)
                            ->searchable()
                            ->options(fn (): array => Patient::query()->orderBy('name')->pluck('name', 'id')->all()),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['patient_id'] ?? null,
                        fn (Builder $q, string $patientId) => $q->whereHas(
                            'prescription',
                            fn (Builder $sub) => $sub->where('patient_id', $patientId)
                        ),
                    )),
                Filter::make('medicine')
                    ->schema([
                        \Filament\Forms\Components\Select::make('medicine_id')
                            ->label(__('Medicine'))
                            ->native(false)
                            ->searchable()
                            ->options(fn (): array => Medicine::query()->orderBy('name')->pluck('name', 'id')->all()),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['medicine_id'] ?? null,
                        fn (Builder $q, string $medicineId) => $q->whereHas(
                            'prescription',
                            fn (Builder $sub) => $sub->where('medicine_id', $medicineId)
                        ),
                    )),
            ])
            ->recordActions([
                Action::make('markApplied')
                    ->label(__('Mark as applied'))
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (PrescriptionSchedule $record): bool => blank($record->administration_applied_at))
                    ->action(function (PrescriptionSchedule $record) use ($today): void {
                        MedicationAdministration::updateOrCreate(
                            ['prescription_schedule_id' => $record->id, 'scheduled_date' => $today->toDateString()],
                            ['applied_at' => now(), 'applied_by_user_id' => auth()->id()],
                        );

                        Notification::make()
                            ->title(__('Dose marked as applied'))
                            ->success()
                            ->send();
                    }),
                Action::make('undoApplied')
                    ->label(__('Undo'))
                    ->icon(Heroicon::OutlinedArrowUturnLeft)
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading(__('Undo application'))
                    ->modalDescription(__('This will mark the dose as pending again.'))
                    ->visible(fn (PrescriptionSchedule $record): bool => filled($record->administration_applied_at))
                    ->action(function (PrescriptionSchedule $record) use ($today): void {
                        MedicationAdministration::query()
                            ->where('prescription_schedule_id', $record->id)
                            ->where('scheduled_date', $today->toDateString())
                            ->delete();

                        Notification::make()
                            ->title(__('Application undone'))
                            ->success()
                            ->send();
                    }),
            ])
            ->headerActions([
                QuickCreateActions::prescription(),
            ])
            ->defaultSort('time');
    }

    protected static function resolveStatus(PrescriptionSchedule $record, \Carbon\Carbon $today): string
    {
        if (filled($record->administration_applied_at)) {
            return 'applied';
        }

        $scheduledAt = \Carbon\Carbon::parse($today->toDateString() . ' ' . $record->time);

        return $scheduledAt->isPast() ? 'late' : 'pending';
    }
}
