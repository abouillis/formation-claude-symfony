<?php

declare(strict_types=1);

namespace App\Tests\Unit\Customer\Service;

use App\Customer\Entity\Customer;
use App\Customer\Enum\CustomerStatus;
use App\Customer\Service\CreditCheckService;
use App\Shared\ValueObject\Money;
use App\Shared\ValueObject\Siret;
use PHPUnit\Framework\TestCase;

class CreditCheckServiceTest extends TestCase
{
    private CreditCheckService $service;

    protected function setUp(): void
    {
        $this->service = new CreditCheckService();
    }

    // ── canPlaceOrder ─────────────────────────────────────────────────────────

    public function testActiveCustomerWithNoCreditLimitCanAlwaysOrder(): void
    {
        $customer = $this->makeCustomer(creditLimitAmount: 0); // 0 = sans limite

        $this->assertTrue($this->service->canPlaceOrder($customer, new Money(99999, 'EUR')));
    }

    public function testActiveCustomerCanOrderBelowCreditLimit(): void
    {
        $customer = $this->makeCustomer(creditLimitAmount: 500000); // 5 000 €

        $this->assertTrue($this->service->canPlaceOrder($customer, new Money(499999, 'EUR')));
    }

    public function testActiveCustomerCanOrderExactlyAtCreditLimit(): void
    {
        $customer = $this->makeCustomer(creditLimitAmount: 500000);

        $this->assertTrue($this->service->canPlaceOrder($customer, new Money(500000, 'EUR')));
    }

    public function testActiveCustomerCannotOrderAboveCreditLimit(): void
    {
        $customer = $this->makeCustomer(creditLimitAmount: 500000);

        $this->assertFalse($this->service->canPlaceOrder($customer, new Money(500001, 'EUR')));
    }

    public function testSuspendedCustomerIsAlwaysRejected(): void
    {
        $customer = $this->makeCustomer(creditLimitAmount: 0, status: CustomerStatus::Suspended);

        $this->assertFalse($this->service->canPlaceOrder($customer, new Money(1, 'EUR')));
    }

    public function testBlacklistedCustomerIsAlwaysRejected(): void
    {
        $customer = $this->makeCustomer(creditLimitAmount: 0, status: CustomerStatus::Blacklisted);

        $this->assertFalse($this->service->canPlaceOrder($customer, new Money(1, 'EUR')));
    }

    public function testMismatchedCurrenciesThrow(): void
    {
        $customer = $this->makeCustomer(creditLimitAmount: 100000, currency: 'EUR');

        $this->expectException(\LogicException::class);
        $this->service->canPlaceOrder($customer, new Money(100, 'USD'));
    }

    // ── availableCredit ───────────────────────────────────────────────────────

    public function testAvailableCreditIsNullWhenNoLimit(): void
    {
        $customer = $this->makeCustomer(creditLimitAmount: 0);

        $this->assertNull($this->service->availableCredit($customer));
    }

    public function testAvailableCreditEqualsLimitWhenFullyAvailable(): void
    {
        $customer = $this->makeCustomer(creditLimitAmount: 500000);

        $credit = $this->service->availableCredit($customer);

        $this->assertNotNull($credit);
        $this->assertSame(500000, $credit->amount);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function makeCustomer(
        int $creditLimitAmount = 0,
        string $currency = 'EUR',
        CustomerStatus $status = CustomerStatus::Active,
    ): Customer {
        $customer = new Customer('ACME Corp', new Siret('73282932000074'), 'acme@example.com');

        // Injecter creditLimit via réflexion
        $ref = new \ReflectionProperty(Customer::class, 'creditLimitAmount');
        $ref->setValue($customer, $creditLimitAmount);

        $refCur = new \ReflectionProperty(Customer::class, 'creditLimitCurrency');
        $refCur->setValue($customer, $currency);

        if ($status !== CustomerStatus::Active) {
            $refStatus = new \ReflectionProperty(Customer::class, 'status');
            $refStatus->setValue($customer, $status);
        }

        return $customer;
    }
}
