<?php

declare(strict_types=1);

namespace App\Shared\ValueObject;

final readonly class Siret
{
    public function __construct(public readonly string $value)
    {
        if (! preg_match('/^[0-9]{14}$/', $value)) {
            throw new \InvalidArgumentException("SIRET invalide : {$value}. Doit contenir exactement 14 chiffres.");
        }
    }

    public function getSiren(): string
    {
        return substr($this->value, 0, 9);
    }

    public function getNic(): string
    {
        return substr($this->value, 9, 5);
    }
}
