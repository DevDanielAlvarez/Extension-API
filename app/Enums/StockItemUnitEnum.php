<?php

namespace App\Enums;

enum StockItemUnitEnum: string
{
    case UNIT = 'UNIT';
    case PAIR = 'PAIR';
    case BOX = 'BOX';
    case PACK = 'PACK';
    case ML = 'ML';
    case G = 'G';
}
