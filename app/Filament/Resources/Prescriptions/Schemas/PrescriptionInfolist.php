<?php

namespace App\Filament\Resources\Prescriptions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PrescriptionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                            ->label(__('ID')),
                TextEntry::make('patient.name')
                            ->label(__('Patient')),
                TextEntry::make('medicine.name')
                            ->label(__('Medicine')),
                TextEntry::make('start_date')
                    ->translateLabel()
                    ->date(),
                TextEntry::make('end_date')
                    ->translateLabel()
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('instructions')
                    ->translateLabel()
                    ->placeholder('-')
                    ->columnSpanFull(),
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
}
