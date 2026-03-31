<?php

namespace App\Filament\Resources\Patients\RelationManagers;

use App\DTO\Prescription\CreatePrescriptionDTO;
use App\DTO\PrescriptionSchedule\CreatePrescriptionScheduleDTO;
use App\Enums\DayOfWeekEnum;
use App\Services\Prescription\PrescriptionService;
use App\Services\PrescriptionSchedule\PrescriptionScheduleService;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class PrescriptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'prescriptions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('medicine_id')
                    ->relationship('medicine', 'name')
                    ->label(__('Medicine'))
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required(),
                DatePicker::make('start_date')
                    ->label(__('Start date'))
                    ->required(),
                DatePicker::make('end_date')
                    ->columnSpanFull()
                    ->label(__('End date'))
                    ->afterOrEqual('start_date'),
                Textarea::make('instructions')
                    ->label(__('Instructions'))
                    ->rows(4)
                    ->columnSpanFull(),
                Repeater::make('prescription_schedules')
                    ->label(__('Prescription schedules'))
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        Select::make('day_of_week')
                        ->label(__('Day of week'))
                        ->native(false)
                        ->options(DayOfWeekEnum::getOptions())
                        ->required(),
                        TimePicker::make('time')
                            ->label(__('Time'))
                            ->native(false)
                            ->seconds(false)
                            ->format('H:i')
                            ->displayFormat('H:i')
                            ->required(),
                        TextInput::make('quantity')
                            ->label(__('Quantity'))
                            ->minValue(1)
                            ->type('number')
                            ->required()
                    ])

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('start_date')
            ->columns([
                TextColumn::make('start_date')
                    ->label(__('Start date'))
                    ->date()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->action(function($data){
                        DB::transaction(function() use ($data) {
                        $dtoToCreatePrescription = new CreatePrescriptionDTO(
                            patient_id: $this->ownerRecord->id,
                            medicine_id: $data['medicine_id'],
                            start_date: Carbon::parse($data['start_date']),
                            end_date: Carbon::parse($data['end_date']),
                            instructions: $data['instructions'],
                        );
                        $prescription = PrescriptionService::create($dtoToCreatePrescription);
                        // Crate prescription schedules
                        foreach ($data['prescription_schedules'] as $schedule) {
                            $dtoToCreatePrescriptionSchedule = new CreatePrescriptionScheduleDTO(
                                prescription_id: $prescription->getRecord()->id,
                                day_of_week: $schedule['day_of_week'],
                                time: $schedule['time'],
                                quantity: $schedule['quantity'],
                            );
                            PrescriptionScheduleService::create($dtoToCreatePrescriptionSchedule);
                        }
                        });
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateRecordDataUsing(function(array $data){
                        //get prescription using id in data
                        $prescription = PrescriptionService::find($data['id']);
                        $data['prescription_schedules'] = $prescription->getRecord()->prescriptionSchedules->map(function($schedule){
                            return [
                                'day_of_week' => $schedule->day_of_week,
                                'time' => $schedule->time,
                                'quantity' => $schedule->quantity,
                            ];
                        })->toArray();

                        return $data;
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
