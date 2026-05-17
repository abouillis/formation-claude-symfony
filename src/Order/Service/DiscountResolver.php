<?php

declare(strict_types=1);

namespace App\Order\Service;

use App\Customer\Entity\Customer;
use App\Customer\Enum\CustomerTierLevel;
use App\Shared\ValueObject\Money;

/**
 * @todo EXERCICE REFACTORING J2 : Remplacer par Strategy Pattern
 * DiscountStrategyInterface + 4 implémentations (Bronze/Silver/Gold/Platinum)
 */
class DiscountResolver
{
    public function resolve(Customer $customer, Money $orderTotal): Money
    {
        $tier = $customer->getTier();

        if (null === $tier || $tier->isExpired()) {
            return Money::zero();
        }

        // LEGACY : switch à remplacer par Strategy Pattern
        switch ($tier->getLevel()) {
            case CustomerTierLevel::Bronze:
                return Money::zero(); // 0%
            case CustomerTierLevel::Silver:
                return $orderTotal->multiply(0.03); // 3%
            case CustomerTierLevel::Gold:
                return $orderTotal->multiply(0.07); // 7%
            case CustomerTierLevel::Platinum:
                return $orderTotal->multiply(0.12); // 12%
            default:
                return Money::zero();
        }
    }
}
