<?php

namespace App\Filament\Resources\Patients\RelationManagers;

use App\Filament\Resources\Responsibles\ResponsibleResource;
use App\Filament\Resources\Responsibles\Schemas\ResponsibleForm;
use Filament\Actions\Action;
use Filament\Actions\AttachAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DetachAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
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
            ->actions([
                ActionGroup::make([
                    DetachAction::make(),
                ]),
            ])
            ->headerActions([
                AttachAction::make()
                    ->recordTitle(fn($record) => $record->name . ' | ' . $record->document_type->value . ': ' . $record->document_number)
                    ->preloadRecordSelect(),
                Action::make('create_responsible')
                    ->label(__('Criar Responsável'))
                    ->form(fn(Schema $schema) => ResponsibleForm::configure($schema))
                    ->action(function ($data) {
                        //create a responsible and attach him
                        $this->getRelationship()->create($data);
                        Notification::make()
                            ->title(__('Responsável criado com sucesso'))
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
