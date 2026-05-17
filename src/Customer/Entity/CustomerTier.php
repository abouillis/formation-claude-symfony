<?php

declare(strict_types=1);

namespace App\Customer\Entity;

use App\Customer\Enum\CustomerTierLevel;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'customer_tiers')]
class CustomerTier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Customer::class, inversedBy: 'tier')]
    #[ORM\JoinColumn(nullable: false)]
    private Customer $customer;

    #[ORM\Column(enumType: CustomerTierLevel::class)]
    private CustomerTierLevel $level = CustomerTierLevel::Bronze;

    #[ORM\Column]
    private \DateTimeImmutable $validUntil;

    public function __construct(Customer $customer, CustomerTierLevel $level, \DateTimeImmutable $validUntil)
    {
        $this->customer = $customer;
        $this->level = $level;
        $this->validUntil = $validUntil;
    }

    public function getId(): ?int { return $this->id; }
    public function getCustomer(): Customer { return $this->customer; }
    public function getLevel(): CustomerTierLevel { return $this->level; }
    public function getValidUntil(): \DateTimeImmutable { return $this->validUntil; }
    public function isExpired(): bool { return $this->validUntil < new \DateTimeImmutable(); }
}
