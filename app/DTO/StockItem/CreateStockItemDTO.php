<?php

namespace App\DTO\StockItem;

use Alvarez\ConcreteDto\AbstractDTO;
use App\Enums\StockItemCategoryEnum;
use App\Enums\StockItemUnitEnum;

class CreateStockItemDTO extends AbstractDTO
{
    public function __construct(
        public readonly string $name,
        public readonly StockItemCategoryEnum $category,
        public readonly StockItemUnitEnum $unit,
        public readonly int $current_quantity,
        public readonly ?int $minimum_quantity,
        public readonly bool $requires_batch_control,
        public readonly ?string $additional_information,
    ) {
    }
}
