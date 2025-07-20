<?php

namespace App\Bundle\CoreBundle\Entity;

use App\Bundle\CartBundle\Entity\ShoppingCart;
use App\Bundle\OrderBundle\Entity\Order;
use App\Bundle\OrderBundle\Entity\ShippingAddress;
use App\Bundle\CoreBundle\Entity\Enum\UserRole;
use App\Bundle\CoreBundle\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`users`')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $password = null;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $vkId = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'user')]
    private Collection $orders;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?ShoppingCart $shoppingCart = null;

    /**
     * @var Collection<int, ShippingAddress>
     */
    #[ORM\OneToMany(targetEntity: ShippingAddress::class, mappedBy: 'user')]
    private Collection $shippingAddresses;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->shippingAddresses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = UserRole::USER->toString();

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

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
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

    public function getShoppingCart(): ?ShoppingCart
    {
        return $this->shoppingCart;
    }

    public function setShoppingCart(ShoppingCart $shoppingCart): static
    {
        // set the owning side of the relation if necessary
        if ($shoppingCart->getUser() !== $this) {
            $shoppingCart->setUser($this);
        }

        $this->shoppingCart = $shoppingCart;

        return $this;
    }

    /**
     * @return Collection<int, ShippingAddress>
     */
    public function getShippingAddresses(): Collection
    {
        return $this->shippingAddresses;
    }

    public function addShippingAddress(ShippingAddress $shippingAddress): static
    {
        if (!$this->shippingAddresses->contains($shippingAddress)) {
            $this->shippingAddresses->add($shippingAddress);
            $shippingAddress->setUser($this);
        }

        return $this;
    }

    public function removeShippingAddress(ShippingAddress $shippingAddress): static
    {
        if ($this->shippingAddresses->removeElement($shippingAddress)) {
            // set the owning side to null (unless already changed)
            if ($shippingAddress->getUser() === $this) {
                $shippingAddress->setUser(null);
            }
        }

        return $this;
    }

    public function isAdmin(): bool
    {
        return in_array(UserRole::ADMIN->value, $this->roles, true);
    }

    public function eraseCredentials(): void
    {
        // If you store temporary, sensitive data on the user, clear it here
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function __toString(): string
    {
        return $this->username;
    }

    /**
     * @return string|null
     */
    public function getVkId(): ?string
    {
        return $this->vkId;
    }

    /**
     * @param string|null $vkId
     */
    public function setVkId(?string $vkId): void
    {
        $this->vkId = $vkId;
    }
}
