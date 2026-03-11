<?php

namespace App\Enums;

enum RouteOfAdministrationEnum: string
{
    case ORAL = 'ORAL';
    case SUBLINGUAL = 'SUBLINGUAL';
    case TOPICAL = 'TOPICAL';
    case INHALATION = 'INHALATION';
    case INTRAVENOUS = 'INTRAVENOUS';
    case INTRAMUSCULAR = 'INTRAMUSCULAR';
    case SUBCUTANEOUS = 'SUBCUTANEOUS';
}
