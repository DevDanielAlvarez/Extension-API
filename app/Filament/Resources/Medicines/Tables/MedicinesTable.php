<?php

namespace App\Filament\Resources\Medicines\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class MedicinesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->translateLabel()
                    ->searchable(),
                TextColumn::make('content_quantity')
                    ->translateLabel()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('content_unit')
                    ->translateLabel()
                    ->badge()
                    ->searchable(),
                TextColumn::make('strength')
                    ->translateLabel()
                    ->searchable(),
                IconColumn::make('is_compounded')
                    ->translateLabel()
                    ->boolean(),
                TextColumn::make('route_of_administration')
                    ->translateLabel()
                    ->formatStateUsing(function ($state): string {
                        $value = $state instanceof \BackedEnum ? $state->value : (string) $state;

                        return match ($value) {
                            'ORAL' => __('Oral'),
                            'SUBLINGUAL' => __('Sublingual'),
                            'TOPICAL' => __('Topical'),
                            'INHALATION' => __('Inhalation'),
                            'INTRAVENOUS' => __('Intravenous'),
                            'INTRAMUSCULAR' => __('Intramuscular'),
                            'SUBCUTANEOUS' => __('Subcutaneous'),
                            default => $value,
                        };
                    })
                    ->badge()
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
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
