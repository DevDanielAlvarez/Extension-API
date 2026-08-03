<?php

namespace App\Filament\Auth;

use App\Enums\DocumentTypeEnum;
use App\Models\User;
use Filament\Auth\Http\Responses\LoginResponse;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class CustomLogin extends Login
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('document_number')
                    ->label(__('CPF'))
                    ->required()
                    ->autofocus(),
                TextInput::make('password')
                    ->translateLabel()
                    ->password()
                    ->revealable()
                    ->required(),
            ]);
    }

    public function authenticate(): \Filament\Auth\Http\Responses\Contracts\LoginResponse|null
    {
        $data = $this->form->getState();
        $user = User::where('document_type', DocumentTypeEnum::CPF)
            ->where('document_number', $data['document_number'])
            ->first();

        if (!$user) {
            Notification::make()
                ->title(__('Invalid credentials'))
                ->body(__('The document or password is incorrect.'))
                ->danger()
                ->send();
            return null;
        }

        if (!Hash::check($data['password'], $user->password)) {
            Notification::make()
                ->title(__('Invalid credentials'))
                ->body(__('The document or password is incorrect.'))
                ->danger()
                ->send();
            return null;
        }

        Filament::auth()->login($user);

        session()->regenerate();

        return app(LoginResponse::class);
    }
}