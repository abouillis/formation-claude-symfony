<?php

declare(strict_types=1);

namespace App\Billing\Entity;

use App\Billing\Enum\PaymentMethod;
use App\Shared\ValueObject\Money;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'payments')]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Invoice::class, inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false)]
    private Invoice $invoice;

    #[ORM\Column]
    private int $amount;

    #[ORM\Column(length: 3)]
    private string $currency = 'EUR';

    #[ORM\Column(enumType: PaymentMethod::class)]
    private PaymentMethod $method;

    #[ORM\Column]
    private \DateTimeImmutable $paidAt;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $reference = null;

    public function __construct(Invoice $invoice, Money $amount, PaymentMethod $method, ?string $reference = null)
    {
        $this->invoice = $invoice;
        $this->amount = $amount->amount;
        $this->currency = $amount->currency;
        $this->method = $method;
        $this->paidAt = new \DateTimeImmutable();
        $this->reference = $reference;
    }

    public function getId(): ?int { return $this->id; }
    public function getInvoice(): Invoice { return $this->invoice; }
    public function getAmount(): Money { return new Money($this->amount, $this->currency); }
    public function getMethod(): PaymentMethod { return $this->method; }
    public function getPaidAt(): \DateTimeImmutable { return $this->paidAt; }
    public function getReference(): ?string { return $this->reference; }
}
