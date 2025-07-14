<?php

namespace App\Bundle\CartBundle\Entity;

use App\Bundle\CartBundle\Repository\ShoppingCartRepository;
use App\Bundle\CoreBundle\Entity\User;
use App\Bundle\ProductBundle\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShoppingCartRepository::class)]
#[ORM\Table(name: '`shopping_carts`')]
#[ORM\HasLifecycleCallbacks]
class ShoppingCart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'shoppingCart', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, CartItem>
     */
    #[ORM\OneToMany(targetEntity: CartItem::class, mappedBy: 'cart')]
    private Collection $cartItems;

    public function __construct()
    {
        $this->cartItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, CartItem>
     */
    public function getCartItems(): Collection
    {
        return $this->cartItems;
    }

    public function increaseNumberOfProducts(Product $product): CartItem
    {
        $cartItem = null;

        foreach ($this->getCartItems() as $item) {
            if ($item->getProduct()->getId() === $product->getId()) {
                $cartItem = $item;
                break;
            }
        }

        if ($cartItem) {
            $cartItem->setQuantity($cartItem->getQuantity() + 1);
        } else {
            $cartItem = new CartItem();
            $cartItem->setProduct($product);
            $cartItem->setPrice($product->getPrice());
            $cartItem->setCart($this);
            $cartItem->setQuantity(1);
            $this->addCartItem($cartItem);
        }

        return $cartItem;
    }

    public function decreaseNumberOfProducts(Product $product): CartItem
    {
        $cartItem = null;

        foreach ($this->getCartItems() as $item) {
            if ($item->getProduct()->getId() === $product->getId()) {
                $cartItem = $item;
                break;
            }
        }

        if (!$cartItem) {
            throw new \RuntimeException('Товар не в корзине');
        }

        $cartItem->setQuantity($cartItem->getQuantity() - 1);

        if ($cartItem->getQuantity() === 0) {
            $this->removeCartItem($cartItem);
        }

        return $cartItem;
    }

    private function addCartItem(CartItem $cartItem): static
    {
        if (!$this->cartItems->contains($cartItem)) {
            $this->cartItems->add($cartItem);
            $cartItem->setCart($this);
        }

        return $this;
    }

    public function removeCartItem(CartItem $cartItem): static
    {
        if ($this->cartItems->removeElement($cartItem)) {
            // set the owning side to null (unless already changed)
            if ($cartItem->getCart() === $this) {
                $cartItem->setCart(null);
            }
        }

        return $this;
    }

    public function getTotalAmount(): int
    {
        $total = 0;

        foreach ($this->cartItems as $item) {
            $total += $item->getPrice() * $item->getQuantity();
        }

        return $total;
    }
}
