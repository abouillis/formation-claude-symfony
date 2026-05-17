<?php

declare(strict_types=1);

namespace App\Catalog\Enum;

enum TaxRate: int
{
    case Zero = 0;
    case Reduced = 550;   // 5.5% en centièmes
    case Intermediate = 1000; // 10%
    case Standard = 2000; // 20%

    public function toFloat(): float
    {
        return $this->value / 10000;
    }

    public function label(): string
    {
        return match($this) {
            self::Zero => '0%',
            self::Reduced => '5,5%',
            self::Intermediate => '10%',
            self::Standard => '20%',
        };
    }
}
