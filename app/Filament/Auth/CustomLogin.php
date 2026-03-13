<?php

namespace App\Filament\Auth;

use App\Enums\DocumentTypeEnum;
use Filament\Auth\Pages\Login;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class CustomLogin extends Login
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('document_type')
                    ->native(false)
                    ->options(DocumentTypeEnum::class)
                    ->translateLabel()
                    ->live(),
                TextInput::make('document_number')
                    ->visible(fn(Get $get) => $get('document_type'))
                    ->translateLabel(),
                TextInput::make('password')
            ]);
    }
}