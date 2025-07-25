<?php

namespace App\Bundle\CartBundle\Entity;

use App\Bundle\ProductBundle\Entity\Product;
use App\Bundle\CartBundle\Repository\CartItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CartItemRepository::class)]
#[ORM\Table(name: '`cart_items`')]
#[ORM\HasLifecycleCallbacks]
class CartItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'cartItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ShoppingCart $cart = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['cart'])]
    private ?Product $product = null;

    #[ORM\Column]
    #[Groups(['cart'])]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['cart'])]
    private ?int $price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCart(): ?ShoppingCart
    {
        return $this->cart;
    }

    public function setCart(?ShoppingCart $cart): static
    {
        $this->cart = $cart;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }
}
