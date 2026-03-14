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
                    ->required(),
                Select::make('document_type')
                    ->options(DocumentTypeEnum::class)
                    ->required(),
                TextInput::make('document_number')
                    ->required(),
                DatePicker::make('admission_date')
                    ->required(),
                DatePicker::make('birthday')
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                Textarea::make('nursing_report')
                    ->columnSpanFull(),
            ]);
    }
}
