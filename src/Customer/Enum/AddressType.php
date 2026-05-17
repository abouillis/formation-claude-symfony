<?php

declare(strict_types=1);

namespace App\Customer\Enum;

enum AddressType: string
{
    case Billing = 'billing';
    case Shipping = 'shipping';
}
