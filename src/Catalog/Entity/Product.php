<?php

declare(strict_types=1);

namespace App\Catalog\Entity;

use App\Catalog\Enum\TaxRate;
use App\Shared\ValueObject\Money;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private string $reference;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private int $priceAmount;

    #[ORM\Column(length: 3)]
    private string $priceCurrency = 'EUR';

    #[ORM\Column(enumType: TaxRate::class)]
    private TaxRate $taxRate = TaxRate::Standard;

    #[ORM\Column]
    private int $stockQuantity = 0;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    private ?Category $category = null;

    #[ORM\Column]
    private bool $isActive = true;

    public function __construct(string $reference, string $name, Money $price, TaxRate $taxRate = TaxRate::Standard)
    {
        $this->reference = $reference;
        $this->name = $name;
        $this->priceAmount = $price->amount;
        $this->priceCurrency = $price->currency;
        $this->taxRate = $taxRate;
    }

    public function getId(): ?int { return $this->id; }
    public function getReference(): string { return $this->reference; }
    public function getName(): string { return $this->name; }
    public function getDescription(): ?string { return $this->description; }
    public function getPrice(): Money { return new Money($this->priceAmount, $this->priceCurrency); }
    public function getTaxRate(): TaxRate { return $this->taxRate; }
    public function getStockQuantity(): int { return $this->stockQuantity; }
    public function getCategory(): ?Category { return $this->category; }
    public function setCategory(?Category $category): void { $this->category = $category; }
    public function isActive(): bool { return $this->isActive; }
    public function isInStock(): bool { return $this->stockQuantity > 0; }
    public function decrementStock(int $quantity): void
    {
        if ($quantity > $this->stockQuantity) {
            throw new \LogicException("Stock insuffisant pour le produit {$this->reference}.");
        }
        $this->stockQuantity -= $quantity;
    }
}
