<?php

namespace App\Filament\Auth;

use App\Enums\DocumentTypeEnum;
use App\Models\User;
use Filament\Auth\Http\Responses\LoginResponse;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

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
                    ->translateLabel()
            ]);
    }

    public function authenticate(): \Filament\Auth\Http\Responses\Contracts\LoginResponse|null
    {
        $data = $this->form->getState();
        $user = User::where('document_type', $data['document_type'])
            ->where('document_number', $data['document_number'])
            ->first();

        if (!$user) {
            return null;
        }

        if (!Hash::check($data['password'], $user->password)) {
            return null;
        }

        Filament::auth()->login($user);

        session()->regenerate();

        return app(LoginResponse::class);
    }
}