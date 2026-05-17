<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\Catalog\Enum\TaxRate;
use App\Catalog\Entity\Product;
use App\Shared\ValueObject\Money;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_lines')]
class OrderLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'lines')]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Product $product;

    #[ORM\Column]
    private int $quantity;

    #[ORM\Column]
    private int $unitPriceAmount;

    #[ORM\Column(length: 3)]
    private string $unitPriceCurrency = 'EUR';

    #[ORM\Column(enumType: TaxRate::class)]
    private TaxRate $taxRate;

    public function __construct(Order $order, Product $product, int $quantity, Money $unitPrice, TaxRate $taxRate)
    {
        $this->order = $order;
        $this->product = $product;
        $this->quantity = $quantity;
        $this->unitPriceAmount = $unitPrice->amount;
        $this->unitPriceCurrency = $unitPrice->currency;
        $this->taxRate = $taxRate;
    }

    public function getId(): ?int { return $this->id; }
    public function getOrder(): Order { return $this->order; }
    public function getProduct(): Product { return $this->product; }
    public function getQuantity(): int { return $this->quantity; }
    public function getUnitPrice(): Money { return new Money($this->unitPriceAmount, $this->unitPriceCurrency); }
    public function getTaxRate(): TaxRate { return $this->taxRate; }

    public function getLineTotal(): Money
    {
        return $this->getUnitPrice()->multiply($this->quantity);
    }

    public function getTaxAmount(): Money
    {
        return $this->getLineTotal()->multiply($this->taxRate->toFloat());
    }
}
