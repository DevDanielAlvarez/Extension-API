<?php

namespace App\Enums;

enum DayOfWeekEnum : int
{
    case Sunday = 0;
    case Monday = 1;
    case Tuesday = 2;
    case Wednesday = 3;
    case Thursday = 4;
    case Friday = 5;
    case Saturday = 6;

    public static function getOptions() : array
    {
        return [
            self::Sunday->value => __('Sunday'),
            self::Monday->value => __('Monday'),
            self::Tuesday->value => __('Tuesday'),
            self::Wednesday->value => __('Wednesday'),
            self::Thursday->value => __('Thursday'),
            self::Friday->value => __('Friday'),
            self::Saturday->value => __('Saturday'),
        ];
    }
}