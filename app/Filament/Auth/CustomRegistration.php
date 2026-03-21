<?php

namespace App\Filament\Auth;

use App\DTO\User\CreateUserDTO;
use App\Enums\DocumentTypeEnum;
use App\Services\User\UserService;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Filament\Auth\Pages\Register;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class CustomRegistration extends Register{
    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema{
        return $schema
            ->components([
                TextInput::make('name')
                    ->translateLabel(),
                Select::make('document_type')
                    ->options(DocumentTypeEnum::class)
                    ->native(false)
                    ->translateLabel(),
                TextInput::make('document_number')
                    ->translateLabel(),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->translateLabel(),
                TextInput::make('password_confirmation')
                    ->password()
                    ->revealable()
                    ->translateLabel(),
            ]);
    }

    public function register(): RegistrationResponse|null{
        // create a dto to create the user using filament form data
        $createUserDTO = new CreateUserDTO(
            name: $this->form->getState()['name'],
            document_type: $this->form->getState()['document_type'],
            document_number: $this->form->getState()['document_number'],
            password: $this->form->getState()['password'],
        );
        // create the user using the user service
        $user = UserService::create($createUserDTO);
        // login the user using filament auth
        Filament::auth()->login($user->getRecord());
        // regenerate the session to prevent session fixation attacks
        session()->regenerate();
        // return the registration response
        return app(RegistrationResponse::class);
    }
}