<?php

namespace App\Enums;

enum StockDonationStatusEnum: string
{
    case PENDING = 'PENDING';
    case CONFIRMED = 'CONFIRMED';
    case CANCELLED = 'CANCELLED';
}
