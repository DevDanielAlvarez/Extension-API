<?php

namespace App\DTO\StockMovement;

use Alvarez\ConcreteDto\AbstractDTO;
use App\Enums\StockMovementTypeEnum;
use Carbon\Carbon;

class CreateStockMovementDTO extends AbstractDTO
{
    public function __construct(
        public readonly string $stock_item_id,
        public readonly StockMovementTypeEnum $type,
        public readonly int $quantity,
        public readonly ?string $patient_id,
        public readonly ?string $user_id,
        public readonly ?string $batch,
        public readonly ?Carbon $expiry_date,
        public readonly ?string $notes,
        public readonly Carbon $movement_date,
        public readonly ?string $stock_donation_id = null,
    ) {
    }
}
