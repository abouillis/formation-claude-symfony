<?php

declare(strict_types=1);

namespace App\Order\Service;

use App\Order\Entity\Order;
use App\Shared\ValueObject\Money;

class OrderCalculator
{
    public function calculateTotal(Order $order): Money
    {
        $total = Money::zero();

        foreach ($order->getLines() as $line) {
            $total = $total->add($line->getLineTotal());
        }

        return $total;
    }

    public function calculateTotalWithTax(Order $order): Money
    {
        $total = Money::zero();

        foreach ($order->getLines() as $line) {
            $lineTotal = $line->getLineTotal();
            $taxAmount = $line->getTaxAmount();
            $total = $total->add($lineTotal)->add($taxAmount);
        }

        return $total;
    }

    /** @return array<string, Money> TVA par taux */
    public function calculateTaxByRate(Order $order): array
    {
        $taxByRate = [];

        foreach ($order->getLines() as $line) {
            $rateLabel = $line->getTaxRate()->label();
            $taxAmount = $line->getTaxAmount();

            if (! isset($taxByRate[$rateLabel])) {
                $taxByRate[$rateLabel] = Money::zero();
            }

            $taxByRate[$rateLabel] = $taxByRate[$rateLabel]->add($taxAmount);
        }

        return $taxByRate;
    }
}
