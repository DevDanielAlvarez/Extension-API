<?php

namespace App\Filament\Resources\StockItems\Pages;

use App\DTO\StockItem\CreateStockItemDTO;
use App\Enums\StockItemCategoryEnum;
use App\Enums\StockItemUnitEnum;
use App\Filament\Resources\StockItems\StockItemResource;
use App\Services\StockItem\StockItemService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateStockItem extends CreateRecord
{
    protected static string $resource = StockItemResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $createStockItemDTO = new CreateStockItemDTO(
            name: $data['name'],
            category: StockItemCategoryEnum::from($data['category']),
            unit: StockItemUnitEnum::from($data['unit']),
            current_quantity: (int) ($data['current_quantity'] ?? 0),
            minimum_quantity: isset($data['minimum_quantity']) ? (int) $data['minimum_quantity'] : null,
            requires_batch_control: $data['requires_batch_control'] ?? false,
            additional_information: $data['additional_information'] ?? null,
        );

        return StockItemService::create($createStockItemDTO)->getRecord();
    }
}
