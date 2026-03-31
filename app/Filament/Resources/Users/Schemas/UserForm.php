<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\DocumentTypeEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados do usuário')
                    ->columnSpanFull()
                    ->description('Preencha os dados de acesso e identificação do usuário.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->translateLabel()
                            ->placeholder('Ex.: Ana Souza')
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
                        TextInput::make('password')
                            ->translateLabel()
                            ->password()
                            ->revealable()
                            ->rule(Password::min(8)->symbols())
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->columnSpanFull(),
                        Select::make('roles')
                                ->label(__('Roles'))
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->columnSpanFull(),
                        Toggle::make('is_adm')
                                ->label(__('Administrator'))
                            ->inline(false)
                            ->default(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
