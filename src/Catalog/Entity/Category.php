<?php

declare(strict_types=1);

namespace App\Catalog\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'categories')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 255, unique: true)]
    private string $slug;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    private ?Category $parent = null;

    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
    private Collection $children;

    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'category')]
    private Collection $products;

    #[ORM\Column]
    private bool $isActive = true;

    public function __construct(string $name, string $slug, ?Category $parent = null)
    {
        $this->name = $name;
        $this->slug = $slug;
        $this->parent = $parent;
        $this->children = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getSlug(): string { return $this->slug; }
    public function getParent(): ?Category { return $this->parent; }
    /** @return Collection<int, Category> */
    public function getChildren(): Collection { return $this->children; }
    /** @return Collection<int, Product> */
    public function getProducts(): Collection { return $this->products; }
    public function isActive(): bool { return $this->isActive; }
}
