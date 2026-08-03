<?php

namespace App\Filament\Resources\Prescriptions\Schemas;

use App\Models\Patient;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PrescriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('patient_id')
                    ->searchable()
                    ->preload()
                    ->relationship('patient', 'name')
                    ->getOptionLabelFromRecordUsing(fn (Patient $record): string => $record->name . ' | ' . $record->document_type->value . ': '.$record->document_number)
                    ->searchable(['name','document_number'])
                    ->translateLabel()
                    ->native(false)
                    ->required(),
                Select::make('medicine_id')
                    ->relationship('medicine', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->name . ' | ' . $record->strength )
                    ->searchable(['name'])
                    ->translateLabel()
                    ->preload()
                    ->required()
                    ->native(false),
                DatePicker::make('start_date')
                    ->translateLabel()
                    ->required(),
                DatePicker::make('end_date')
                    ->translateLabel()
                    ->helperText(__('Leave blank for continuous use.')),
                Textarea::make('instructions')
                    ->translateLabel()
                    ->columnSpanFull(),
            ]);
    }
}
