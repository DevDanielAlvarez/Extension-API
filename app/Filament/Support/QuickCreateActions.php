<?php

namespace App\Filament\Support;

use App\DTO\Medicine\CreateMedicineDTO;
use App\DTO\Patient\CreatePatientDTO;
use App\DTO\Prescription\CreatePrescriptionDTO;
use App\DTO\PrescriptionSchedule\CreatePrescriptionScheduleDTO;
use App\Enums\DayOfWeekEnum;
use App\Enums\DocumentTypeEnum;
use App\Filament\Resources\Medicines\Schemas\MedicineForm;
use App\Filament\Resources\Patients\Schemas\PatientForm;
use App\Models\Patient;
use App\Services\Medicine\MedicineService;
use App\Services\Patient\PatientService;
use App\Services\Prescription\PrescriptionService;
use App\Services\PrescriptionSchedule\PrescriptionScheduleService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class QuickCreateActions
{
    public static function patient(): Action
    {
        return Action::make('quickCreatePatient')
            ->label(__('Novo Paciente'))
            ->icon(Heroicon::OutlinedUserPlus)
            ->modalHeading(__('Novo Paciente'))
            ->modalSubmitActionLabel(__('Criar'))
            ->schema(fn ($schema) => PatientForm::configure($schema))
            ->action(function (array $data): void {
                PatientService::create(new CreatePatientDTO(
                    name: $data['name'],
                    document_type: DocumentTypeEnum::from($data['document_type']),
                    document_number: $data['document_number'],
                    admission_date: Carbon::parse($data['admission_date']),
                    birthday: Carbon::parse($data['birthday']),
                    phone: $data['phone'] ?? null,
                    nursing_report: $data['nursing_report'] ?? null,
                ));

                Notification::make()
                    ->title(__('Paciente criado com sucesso'))
                    ->success()
                    ->send();
            });
    }

    public static function medicine(): Action
    {
        return Action::make('quickCreateMedicine')
            ->label(__('Novo Medicamento'))
            ->icon(Heroicon::OutlinedBeaker)
            ->modalHeading(__('Novo Medicamento'))
            ->modalSubmitActionLabel(__('Criar'))
            ->schema(fn ($schema) => MedicineForm::configure($schema))
            ->action(function (array $data): void {
                MedicineService::create(new CreateMedicineDTO(
                    name: $data['name'],
                    content_quantity: $data['content_quantity'],
                    content_unit: $data['content_unit'],
                    strength: $data['strength'],
                    is_compounded: $data['is_compounded'] ?? false,
                    route_of_administration: $data['route_of_administration'],
                    additional_information: $data['additional_information'] ?? null,
                ));

                Notification::make()
                    ->title(__('Medicamento criado com sucesso'))
                    ->success()
                    ->send();
            });
    }

    public static function prescription(): Action
    {
        return Action::make('quickCreatePrescription')
            ->label(__('Nova Prescrição'))
            ->icon(Heroicon::OutlinedClipboardDocumentList)
            ->modalHeading(__('Nova Prescrição'))
            ->modalSubmitActionLabel(__('Criar'))
            ->modalWidth('2xl')
            ->schema([
                Select::make('patient_id')
                    ->label(__('Patient'))
                    ->relationship('patient', 'name')
                    ->getOptionLabelFromRecordUsing(fn (Patient $record): string => $record->name . ' | ' . $record->document_type->value . ': ' . $record->document_number)
                    ->searchable(['name', 'document_number'])
                    ->native(false)
                    ->preload()
                    ->required(),
                Select::make('medicine_id')
                    ->label(__('Medicine'))
                    ->relationship('medicine', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->name . ' | ' . $record->strength)
                    ->searchable(['name'])
                    ->native(false)
                    ->preload()
                    ->required(),
                DatePicker::make('start_date')
                    ->label(__('Start date'))
                    ->required(),
                DatePicker::make('end_date')
                    ->label(__('End date'))
                    ->helperText(__('Leave blank for continuous use.'))
                    ->afterOrEqual('start_date'),
                Textarea::make('instructions')
                    ->label(__('Instructions'))
                    ->rows(3)
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
                            ->required(),
                    ]),
            ])
            ->action(function (array $data): void {
                DB::transaction(function () use ($data): void {
                    $prescription = PrescriptionService::create(new CreatePrescriptionDTO(
                        patient_id: $data['patient_id'],
                        medicine_id: $data['medicine_id'],
                        start_date: Carbon::parse($data['start_date']),
                        end_date: Carbon::parse($data['end_date'] ?? null),
                        instructions: $data['instructions'] ?? null,
                    ));

                    foreach ($data['prescription_schedules'] ?? [] as $schedule) {
                        PrescriptionScheduleService::create(new CreatePrescriptionScheduleDTO(
                            prescription_id: $prescription->getRecord()->id,
                            day_of_week: $schedule['day_of_week'],
                            time: $schedule['time'],
                            quantity: $schedule['quantity'],
                        ));
                    }
                });

                Notification::make()
                    ->title(__('Prescrição criada com sucesso'))
                    ->success()
                    ->send();
            });
    }
}
