<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\Customer\Entity\Address;
use App\Customer\Entity\Customer;
use App\Order\Enum\OrderStatus;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private Customer $customer;

    #[ORM\Column(enumType: OrderStatus::class)]
    private OrderStatus $status = OrderStatus::Draft;

    #[ORM\OneToMany(targetEntity: OrderLine::class, mappedBy: 'order', cascade: ['persist', 'remove'])]
    private Collection $lines;

    #[ORM\ManyToOne(targetEntity: Address::class)]
    private ?Address $shippingAddress = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $note = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $confirmedAt = null;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
        $this->createdAt = new \DateTimeImmutable();
        $this->lines = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getCustomer(): Customer { return $this->customer; }
    public function getStatus(): OrderStatus { return $this->status; }
    public function getShippingAddress(): ?Address { return $this->shippingAddress; }
    public function setShippingAddress(?Address $address): void { $this->shippingAddress = $address; }
    public function getNote(): ?string { return $this->note; }
    public function setNote(?string $note): void { $this->note = $note; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getConfirmedAt(): ?\DateTimeImmutable { return $this->confirmedAt; }

    /** @return Collection<int, OrderLine> */
    public function getLines(): Collection { return $this->lines; }

    public function addLine(OrderLine $line): void { $this->lines->add($line); }

    public function confirm(): void
    {
        $this->status = OrderStatus::Confirmed;
        $this->confirmedAt = new \DateTimeImmutable();
    }

    public function ship(): void { $this->status = OrderStatus::Shipped; }
    public function deliver(): void { $this->status = OrderStatus::Delivered; }
    public function cancel(): void { $this->status = OrderStatus::Cancelled; }
}
