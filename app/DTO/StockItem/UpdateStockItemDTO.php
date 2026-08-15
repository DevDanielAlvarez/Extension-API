<?php

namespace App\DTO\StockItem;

use Alvarez\ConcreteDto\AbstractDTO;
use App\Enums\StockItemCategoryEnum;
use App\Enums\StockItemUnitEnum;

/**
 * current_quantity is intentionally absent: balance only changes through
 * StockMovement entries, never through a direct item update.
 */
class UpdateStockItemDTO extends AbstractDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly StockItemCategoryEnum $category,
        public readonly StockItemUnitEnum $unit,
        public readonly ?int $minimum_quantity,
        public readonly bool $requires_batch_control,
        public readonly ?string $additional_information,
    ) {
    }
}
