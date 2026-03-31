<?php

namespace App\Filament\Resources\Prescriptions\RelationManagers;

use App\Filament\Resources\Prescriptions\PrescriptionResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PrescriptionSchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'prescriptionSchedules';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Prescription schedules'))
            ->columns([
                TextColumn::make('day_of_week')
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
                    ->translateLabel(),
                TextColumn::make('quantity')
                    ->translateLabel(),
            ])
            ->headerActions([
                ActionGroup::make([
                    CreateAction::make(),
                ]),
            ]);
    }
}
