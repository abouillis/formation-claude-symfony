<?php

declare(strict_types=1);

namespace App\Tests\Unit\Order\Service;

use App\Customer\Entity\Customer;
use App\Customer\Entity\CustomerTier;
use App\Customer\Enum\CustomerTierLevel;
use App\Order\Service\DiscountResolver;
use App\Shared\ValueObject\Money;
use App\Shared\ValueObject\Siret;
use PHPUnit\Framework\TestCase;

/**
 * Tests de non-régression pour DiscountResolver.
 * Ces tests doivent rester verts après le refactoring Strategy Pattern.
 */
class DiscountResolverTest extends TestCase
{
    private DiscountResolver $resolver;

    protected function setUp(): void
    {
        // TODO après refactoring : injecter les stratégies ici
        $this->resolver = new DiscountResolver();
    }

    public function testNoDiscountWhenNoTier(): void
    {
        $customer = $this->makeCustomer();

        $discount = $this->resolver->resolve($customer, new Money(10000, 'EUR'));

        $this->assertSame(0, $discount->amount);
    }

    public function testNoDiscountForBronzeTier(): void
    {
        $customer = $this->makeCustomerWithTier(CustomerTierLevel::Bronze);

        $discount = $this->resolver->resolve($customer, new Money(10000, 'EUR'));

        $this->assertSame(0, $discount->amount);
    }

    public function testThreePercentForSilver(): void
    {
        $customer = $this->makeCustomerWithTier(CustomerTierLevel::Silver);

        $discount = $this->resolver->resolve($customer, new Money(10000, 'EUR'));

        $this->assertSame(300, $discount->amount); // 3% de 10 000 centimes
    }

    public function testSevenPercentForGold(): void
    {
        $customer = $this->makeCustomerWithTier(CustomerTierLevel::Gold);

        $discount = $this->resolver->resolve($customer, new Money(10000, 'EUR'));

        $this->assertSame(700, $discount->amount);
    }

    public function testTwelvePercentForPlatinum(): void
    {
        $customer = $this->makeCustomerWithTier(CustomerTierLevel::Platinum);

        $discount = $this->resolver->resolve($customer, new Money(10000, 'EUR'));

        $this->assertSame(1200, $discount->amount);
    }

    public function testNoDiscountWhenTierExpired(): void
    {
        $customer = $this->makeCustomer();
        $expiredTier = new CustomerTier(
            $customer,
            CustomerTierLevel::Gold,
            new \DateTimeImmutable('-1 day'), // expiré hier
        );
        $this->setTier($customer, $expiredTier);

        $discount = $this->resolver->resolve($customer, new Money(10000, 'EUR'));

        $this->assertSame(0, $discount->amount);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function makeCustomer(): Customer
    {
        return new Customer('ACME Corp', new Siret('73282932000074'), 'acme@example.com');
    }

    private function makeCustomerWithTier(CustomerTierLevel $level): Customer
    {
        $customer = $this->makeCustomer();
        $tier = new CustomerTier($customer, $level, new \DateTimeImmutable('+1 year'));
        $this->setTier($customer, $tier);

        return $customer;
    }

    /** Injecte le tier via réflexion (relation OneToOne bidirectionnelle) */
    private function setTier(Customer $customer, CustomerTier $tier): void
    {
        $ref = new \ReflectionProperty(Customer::class, 'tier');
        $ref->setValue($customer, $tier);
    }
}
