<?php

declare(strict_types=1);

namespace App\Billing\Entity;

use App\Billing\Enum\InvoiceStatus;
use App\Order\Entity\Order;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'invoices')]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Order::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    #[ORM\Column(length: 30, unique: true)]
    private string $invoiceNumber;

    #[ORM\Column(enumType: InvoiceStatus::class)]
    private InvoiceStatus $status = InvoiceStatus::Draft;

    #[ORM\Column]
    private \DateTimeImmutable $issuedAt;

    #[ORM\Column]
    private \DateTimeImmutable $dueAt;

    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'invoice', cascade: ['persist'])]
    private Collection $payments;

    public function __construct(Order $order, string $invoiceNumber, \DateTimeImmutable $dueAt)
    {
        $this->order = $order;
        $this->invoiceNumber = $invoiceNumber;
        $this->issuedAt = new \DateTimeImmutable();
        $this->dueAt = $dueAt;
        $this->payments = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getOrder(): Order { return $this->order; }
    public function getInvoiceNumber(): string { return $this->invoiceNumber; }
    public function getStatus(): InvoiceStatus { return $this->status; }
    public function getIssuedAt(): \DateTimeImmutable { return $this->issuedAt; }
    public function getDueAt(): \DateTimeImmutable { return $this->dueAt; }

    /** @return Collection<int, Payment> */
    public function getPayments(): Collection { return $this->payments; }

    public function markAsSent(): void { $this->status = InvoiceStatus::Sent; }
    public function markAsPaid(): void { $this->status = InvoiceStatus::Paid; }
    public function markAsOverdue(): void { $this->status = InvoiceStatus::Overdue; }
    public function cancel(): void { $this->status = InvoiceStatus::Cancelled; }

    public function isOverdue(): bool
    {
        return $this->dueAt < new \DateTimeImmutable() && $this->status !== InvoiceStatus::Paid;
    }
}
