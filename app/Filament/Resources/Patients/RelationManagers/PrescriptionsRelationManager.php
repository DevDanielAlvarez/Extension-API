<?php

namespace App\Filament\Resources\Patients\RelationManagers;

use App\DTO\Prescription\CreatePrescriptionDTO;
use App\Services\Prescription\PrescriptionService;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PrescriptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'prescriptions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('medicine_id')
                    ->relationship('medicine', 'name')
                    ->label(__('Medicamento'))
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required(),
                DatePicker::make('start_date')
                    ->label(__('Data de início'))
                    ->required(),
                DatePicker::make('end_date')
                    ->columnSpanFull()
                    ->label(__('Data de término'))
                    ->afterOrEqual('start_date'),
                Textarea::make('instructions')
                    ->label(__('Instruções'))
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('start_date')
            ->columns([
                TextColumn::make('start_date')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->action(function($data){
                        $dtoToCreatePrescription = new CreatePrescriptionDTO(
                            patient_id: $this->ownerRecord->id,
                            medicine_id: $data['medicine_id'],
                            start_date: Carbon::parse($data['start_date']),
                            end_date: Carbon::parse($data['end_date']),
                            instructions: $data['instructions'],
                        );
                        PrescriptionService::create($dtoToCreatePrescription);
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
