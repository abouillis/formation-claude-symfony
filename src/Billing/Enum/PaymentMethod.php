<?php

declare(strict_types=1);

namespace App\Billing\Enum;

enum PaymentMethod: string
{
    case BankTransfer = 'bank_transfer';
    case Card = 'card';
    case Check = 'check';
}
