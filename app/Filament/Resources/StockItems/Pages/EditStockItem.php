<?php

namespace App\Filament\Resources\StockItems\Pages;

use App\DTO\StockItem\UpdateStockItemDTO;
use App\Enums\StockItemCategoryEnum;
use App\Enums\StockItemUnitEnum;
use App\Filament\Resources\StockItems\StockItemResource;
use App\Services\StockItem\StockItemService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditStockItem extends EditRecord
{
    protected static string $resource = StockItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $updateStockItemDTO = new UpdateStockItemDTO(
            id: $record->id,
            name: $data['name'],
            category: StockItemCategoryEnum::from($data['category']),
            unit: StockItemUnitEnum::from($data['unit']),
            minimum_quantity: isset($data['minimum_quantity']) ? (int) $data['minimum_quantity'] : null,
            requires_batch_control: $data['requires_batch_control'] ?? false,
            additional_information: $data['additional_information'] ?? null,
        );

        return StockItemService::find($record->id)->update($updateStockItemDTO)->getRecord();
    }
}
