<?php

namespace App\Filament\Resources\Prescriptions\Schemas;

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
                    ->relationship('patient', 'name')
                    ->translateLabel()
                    ->required(),
                Select::make('medicine_id')
                    ->relationship('medicine', 'name')
                    ->translateLabel()
                    ->required(),
                DatePicker::make('start_date')
                    ->translateLabel()
                    ->required(),
                DatePicker::make('end_date')
                    ->translateLabel(),
                Textarea::make('instructions')
                    ->translateLabel()
                    ->columnSpanFull(),
            ]);
    }
}
