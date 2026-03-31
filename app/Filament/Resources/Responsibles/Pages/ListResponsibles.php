<?php

namespace App\Filament\Resources\Responsibles\Pages;

use App\Filament\Resources\Responsibles\ResponsibleResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListResponsibles extends ListRecords
{
    protected static string $resource = ResponsibleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                CreateAction::make(),
            ]),
        ];
    }
}
