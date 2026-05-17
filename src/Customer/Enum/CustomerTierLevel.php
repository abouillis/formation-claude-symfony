<?php

declare(strict_types=1);

namespace App\Customer\Enum;

enum CustomerTierLevel: string
{
    case Bronze = 'bronze';
    case Silver = 'silver';
    case Gold = 'gold';
    case Platinum = 'platinum';
}
