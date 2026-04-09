<?php

namespace App\Filament\Resources\Prescriptions\RelationManagers;

use App\Filament\Resources\Prescriptions\PrescriptionResource;
use Carbon\Carbon;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PrescriptionSchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'prescriptionSchedules';

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema{
        return $schema
            ->components([
                Select::make('day_of_week')
                    ->label(__('Day of week'))
                    ->options([
                        0 => __('Sunday'),
                        1 => __('Monday'),
                        2 => __('Tuesday'),
                        3 => __('Wednesday'),
                        4 => __('Thursday'),
                        5 => __('Friday'),
                        6 => __('Saturday'),
                    ])
                    ->required()
                    ->native(false),
                TimePicker::make('time')
                    ->label(__('Time'))
                    ->native(false)
                    ->seconds(false)
                    ->format('H:i')
                    ->displayFormat('H:i')
                    ->required(),
                TextInput::make('quantity')
                    ->label(__('Quantity'))
                    ->required()
                    ->numeric()
                    ->minValue(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Prescription schedules'))
            ->columns([
                TextColumn::make('day_of_week')
                ->label('Dia da semana')
                ->formatStateUsing(function($state){
                    return match($state) {
                        0 => __('Sunday'),
                        1 => __('Monday'),
                        2 => __('Tuesday'),
                        3 => __('Wednesday'),
                        4 => __('Thursday'),
                        5 => __('Friday'),
                        6 => __('Saturday'),
                    };
                }),
                TextColumn::make('time')
                    ->translateLabel()
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('H:i')),
                TextColumn::make('quantity')
                    ->translateLabel(),
            ])
            ->actions([
                DeleteAction::make()
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Adicionar horário')
                    ->modalHeading('Adicionar horário')
                    ->form(fn($schema) => $this->form($schema)),
            ]);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Prescription schedules');
    }
}
