<?php

namespace App\Filament\Resources\Roles\RelationManagers;

use App\Enums\DocumentTypeEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public function form(Schema $schema): Schema
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
                TextInput::make('password')
                    ->translateLabel()
                    ->password()
                    ->required(),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label(__('ID')),
                TextEntry::make('name')
                    ->translateLabel(),
                TextEntry::make('document_type')
                    ->badge()
                    ->translateLabel(),
                TextEntry::make('document_number')
                    ->translateLabel(),
                TextEntry::make('created_at')
                    ->translateLabel()
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->translateLabel()
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->translateLabel(),
                TextColumn::make('document_type')
                    ->translateLabel()
                    ->badge()
                    ->searchable(),
                TextColumn::make('document_number')
                    ->translateLabel()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ActionGroup::make([
                    AttachAction::make()
                        ->preloadRecordSelect(),
                ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    DetachAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
