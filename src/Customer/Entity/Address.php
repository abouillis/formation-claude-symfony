<?php

declare(strict_types=1);

namespace App\Customer\Entity;

use App\Customer\Enum\AddressType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'addresses')]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: false)]
    private Customer $customer;

    #[ORM\Column(length: 255)]
    private string $street;

    #[ORM\Column(length: 100)]
    private string $city;

    #[ORM\Column(length: 10)]
    private string $postalCode;

    #[ORM\Column(length: 2)]
    private string $country = 'FR';

    #[ORM\Column(enumType: AddressType::class)]
    private AddressType $type;

    #[ORM\Column]
    private bool $isDefault = false;

    public function __construct(Customer $customer, string $street, string $city, string $postalCode, AddressType $type)
    {
        $this->customer = $customer;
        $this->street = $street;
        $this->city = $city;
        $this->postalCode = $postalCode;
        $this->type = $type;
    }

    public function getId(): ?int { return $this->id; }
    public function getCustomer(): Customer { return $this->customer; }
    public function getStreet(): string { return $this->street; }
    public function getCity(): string { return $this->city; }
    public function getPostalCode(): string { return $this->postalCode; }
    public function getCountry(): string { return $this->country; }
    public function getType(): AddressType { return $this->type; }
    public function isDefault(): bool { return $this->isDefault; }
    public function setDefault(bool $isDefault): void { $this->isDefault = $isDefault; }
}
