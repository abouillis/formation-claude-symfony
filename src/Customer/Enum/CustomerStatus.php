<?php

declare(strict_types=1);

namespace App\Customer\Enum;

enum CustomerStatus: string
{
    case Active = 'active';
    case Suspended = 'suspended';
    case Blacklisted = 'blacklisted';

    public function label(): string
    {
        return match($this) {
            self::Active => 'Actif',
            self::Suspended => 'Suspendu',
            self::Blacklisted => 'Blacklisté',
        };
    }

    public function canPlaceOrder(): bool
    {
        return $this === self::Active;
    }
}
