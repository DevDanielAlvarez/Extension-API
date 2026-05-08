<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum ContentUnitEnum: string implements HasLabel
{
    case MG = 'MG';
    case MCG = 'MCG';
    case G = 'G';
    case ML = 'ML';
    case IU = 'IU';
    case UNIT = 'UNIT';

    public function getLabel(): string | Htmlable | null{
        return match ($this) {
            self::MG => __('MG'),
            self::MCG => __('MCG'),
            self::G => __('G'),
            self::ML => __('ML'),
            self::IU => __('IU'),
            self::UNIT => __('UNIT'),
        };
    }
}
