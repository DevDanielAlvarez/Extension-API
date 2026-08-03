<?php

namespace App\Filament\Resources\Patients\Schemas;

use App\Enums\DocumentTypeEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PatientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações do paciente')
                    ->columnSpanFull()
                    ->description('Preencha os dados cadastrais e clínicos do paciente.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->translateLabel()
                            ->placeholder('Ex.: Maria da Silva')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Select::make('document_type')
                            ->translateLabel()
                            ->options(DocumentTypeEnum::class)
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('document_number')
                            ->translateLabel()
                            ->placeholder('Somente números')
                            ->required()
                            ->maxLength(255),
                        DatePicker::make('admission_date')
                            ->translateLabel()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->required(),
                        DatePicker::make('birthday')
                            ->translateLabel()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->required(),
                        TextInput::make('phone')
                            ->columnSpanFull()
                            ->translateLabel()
                            ->tel()
                            ->placeholder('Ex.: (11) 99999-9999')
                            ->maxLength(30),
                        KeyValue::make('nursing_report')
                            ->translateLabel()
                            ->helperText(__('Anotações livres de enfermagem, no formato campo/valor (ex.: alergia: penicilina).'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
