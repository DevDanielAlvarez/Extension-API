<?php

namespace App\Filament\Resources\PatientMedicines\Schemas;

use App\DTO\PatientMedicine\CreatePatientMedicineDTO;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\PatientMedicine;
use App\Services\PatientMedicine\PatientMedicineService;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class PatientMedicineForm
{
    /**
     * Used from the top-level resource's Create/Edit pages, where $operation
     * is reliably available to toggle identity fields on/off.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Estoque de medicamento do paciente')
                ->columnSpanFull()
                ->description('Cada saldo pertence sempre a um paciente específico — a clínica não mantém estoque próprio de medicamentos.')
                ->columns(2)
                ->schema([
                    Select::make('patient_id')
                        ->label('Paciente')
                        ->options(fn () => Patient::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->required()
                        ->visible(fn (string $operation): bool => $operation === 'create'),
                    Select::make('medicine_id')
                        ->label('Medicamento')
                        ->options(fn () => self::medicineOptions())
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->required()
                        ->visible(fn (string $operation): bool => $operation === 'create'),
                    self::minimumQuantityField(),
                    TextInput::make('current_quantity')
                        ->label('Estoque inicial')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->required()
                        ->visible(fn (string $operation): bool => $operation === 'create')
                        ->helperText('Depois de criado, o saldo só muda através da aba "Movimentações".'),
                    Placeholder::make('current_quantity_display')
                        ->label('Saldo atual')
                        ->content(fn (?PatientMedicine $record): string => $record ? (string) $record->current_quantity : '-')
                        ->visible(fn (string $operation): bool => $operation === 'edit'),
                ]),
        ]);
    }

    /**
     * Used from an ad-hoc action (e.g. the patient's own screen), where the
     * patient is the fixed owner record and there is no page-level
     * $operation context — every field here is always visible.
     */
    public static function configureForPatient(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('medicine_id')
                ->label('Medicamento')
                ->options(fn () => self::medicineOptions())
                ->searchable()
                ->preload()
                ->native(false)
                ->required(),
            self::minimumQuantityField(),
            TextInput::make('current_quantity')
                ->label('Estoque inicial')
                ->numeric()
                ->minValue(0)
                ->default(0)
                ->required()
                ->helperText('Depois de criado, o saldo só muda através da aba "Movimentações".'),
        ]);
    }

    /**
     * @return array<string, string>
     */
    protected static function medicineOptions(): array
    {
        return Medicine::query()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Medicine $medicine): array => [$medicine->id => "{$medicine->name} | {$medicine->strength}"])
            ->all();
    }

    protected static function minimumQuantityField(): TextInput
    {
        return TextInput::make('minimum_quantity')
            ->label('Estoque mínimo')
            ->numeric()
            ->minValue(0)
            ->helperText('Usado para sinalizar estoque baixo deste medicamento para o paciente.');
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public static function createForPatient(string $patientId, array $data): void
    {
        DB::transaction(function () use ($patientId, $data): void {
            PatientMedicineService::create(new CreatePatientMedicineDTO(
                patient_id: $patientId,
                medicine_id: $data['medicine_id'],
                current_quantity: (int) ($data['current_quantity'] ?? 0),
                minimum_quantity: isset($data['minimum_quantity']) ? (int) $data['minimum_quantity'] : null,
            ));
        });
    }
}
