<?php

namespace App\Filament\Resources\Patients\Schemas;

use App\Enums\DocumentTypeEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PatientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->translateLabel()
                    ->required(),
                Select::make('document_type')
                    ->translateLabel()
                    ->options(DocumentTypeEnum::class)
                    ->required(),
                TextInput::make('document_number')
                    ->translateLabel()
                    ->required(),
                DatePicker::make('admission_date')
                    ->translateLabel()
                    ->required(),
                DatePicker::make('birthday')
                    ->translateLabel()
                    ->required(),
                TextInput::make('phone')
                    ->translateLabel()
                    ->tel(),
                Textarea::make('nursing_report')
                    ->translateLabel()
                    ->columnSpanFull(),
            ]);
    }
}
