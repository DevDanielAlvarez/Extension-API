<?php

namespace App\DTO\StockDonation;

use Alvarez\ConcreteDto\AbstractDTO;

class CreateStockDonationDTO extends AbstractDTO
{
    public function __construct(
        public readonly string $stock_item_id,
        public readonly string $patient_id,
        public readonly int $quantity,
        public readonly string $donor_name,
        public readonly ?string $donor_phone,
        public readonly ?string $notes,
    ) {
    }
}
