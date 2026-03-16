<?php

namespace App\Filament\Resources\Patients\RelationManagers;

use App\Filament\Resources\Responsibles\ResponsibleResource;
use App\Filament\Resources\Responsibles\Schemas\ResponsibleForm;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ResponsiblesRelationManager extends RelationManager
{
    protected static string $relationship = 'responsibles';

    protected static ?string $relatedResource = ResponsibleResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Action::make('create_responsible')
                    ->label('Criar Responsável')
                    ->form(fn(Schema $schema) => ResponsibleForm::configure($schema))
                    ->action(function ($data) {
                        dd($data);
                    }),
            ]);
    }
}
