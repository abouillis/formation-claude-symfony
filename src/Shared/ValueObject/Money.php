<?php

declare(strict_types=1);

namespace App\Shared\ValueObject;

final readonly class Money
{
    public function __construct(
        public readonly int $amount,
        public readonly string $currency = 'EUR',
    ) {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Le montant ne peut pas être négatif.');
        }
    }

    public static function fromFloat(float $amount, string $currency = 'EUR'): self
    {
        // BUG INTENTIONNEL : arrondi float pour l'exercice de debugging
        return new self((int) round($amount * 100), $currency);
    }

    public static function zero(string $currency = 'EUR'): self
    {
        return new self(0, $currency);
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException('Impossible d\'additionner des devises différentes.');
        }
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function multiply(int|float $factor): self
    {
        return new self((int) round($this->amount * $factor), $this->currency);
    }

    public function greaterThan(self $other): bool
    {
        return $this->amount > $other->amount;
    }

    public function toFloat(): float
    {
        return $this->amount / 100;
    }

    public function format(): string
    {
        return number_format($this->toFloat(), 2, ',', ' ').' '.$this->currency;
    }
}
