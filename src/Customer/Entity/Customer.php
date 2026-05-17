<?php

declare(strict_types=1);

namespace App\Customer\Entity;

use App\Customer\Enum\CustomerStatus;
use App\Order\Entity\Order;
use App\Shared\ValueObject\Money;
use App\Shared\ValueObject\Siret;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'customers')]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $companyName;

    #[ORM\Column(length: 14)]
    private string $siret;

    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    #[ORM\Column(enumType: CustomerStatus::class)]
    private CustomerStatus $status = CustomerStatus::Active;

    #[ORM\Column]
    private int $creditLimitAmount = 0;

    #[ORM\Column(length: 3)]
    private string $creditLimitCurrency = 'EUR';

    #[ORM\OneToMany(targetEntity: Address::class, mappedBy: 'customer', cascade: ['persist', 'remove'])]
    private Collection $addresses;

    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'customer')]
    private Collection $orders;

    #[ORM\OneToOne(targetEntity: CustomerTier::class, mappedBy: 'customer', cascade: ['persist'])]
    private ?CustomerTier $tier = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct(string $companyName, Siret $siret, string $email)
    {
        $this->companyName = $companyName;
        $this->siret = $siret->value;
        $this->email = $email;
        $this->createdAt = new \DateTimeImmutable();
        $this->addresses = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getCompanyName(): string { return $this->companyName; }
    public function getSiret(): Siret { return new Siret($this->siret); }
    public function getEmail(): string { return $this->email; }
    public function getStatus(): CustomerStatus { return $this->status; }

    public function getCreditLimit(): Money
    {
        return new Money($this->creditLimitAmount, $this->creditLimitCurrency);
    }

    public function setCreditLimit(Money $creditLimit): void
    {
        $this->creditLimitAmount = $creditLimit->amount;
        $this->creditLimitCurrency = $creditLimit->currency;
    }

    public function suspend(): void { $this->status = CustomerStatus::Suspended; }
    public function activate(): void { $this->status = CustomerStatus::Active; }
    public function blacklist(): void { $this->status = CustomerStatus::Blacklisted; }

    /** @return Collection<int, Address> */
    public function getAddresses(): Collection { return $this->addresses; }

    /** @return Collection<int, Order> */
    public function getOrders(): Collection { return $this->orders; }

    public function getTier(): ?CustomerTier { return $this->tier; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
