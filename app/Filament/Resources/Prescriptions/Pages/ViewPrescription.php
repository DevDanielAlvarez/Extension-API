<?php

namespace App\Filament\Resources\Prescriptions\Pages;

use App\Filament\Resources\Prescriptions\PrescriptionResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPrescription extends ViewRecord
{
    protected static string $resource = PrescriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                EditAction::make(),
            ]),
        ];
    }
}
