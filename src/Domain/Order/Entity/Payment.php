<?php

namespace App\Domain\Order\Entity;

use App\Domain\Order\Entity\Enum\PaymentMethod;
use App\Domain\Order\Entity\Enum\PaymentStatus;
use App\Domain\Order\Repository\PaymentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
#[ORM\Table(name: '`payments`')]
#[ORM\HasLifecycleCallbacks]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $order = null;

    #[ORM\Column(type: 'integer')]
    private ?int $amount = null;

    #[ORM\Column(enumType: PaymentStatus::class, length: 50)]
    private ?PaymentStatus $status = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $paymentDate = null;

    #[ORM\Column(enumType: PaymentMethod::class, length: 50)]
    private ?PaymentMethod $paymentMethod = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'payment')]
    private Collection $transactions;

    public function __construct(Order $order, PaymentMethod $paymentMethod)
    {
        $this->order = $order;
        $this->amount = $order->getTotalAmount();
        $this->status = PaymentStatus::PENDING;
        $this->paymentMethod = $paymentMethod;

        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function getStatus(): ?PaymentStatus
    {
        return $this->status;
    }

    public function setStatus(PaymentStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPaymentDate(): ?\DateTimeImmutable
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(\DateTimeImmutable $paymentDate): static
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    public function getPaymentMethod(): ?PaymentMethod
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(PaymentMethod $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;

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
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setOrder($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getOrder() === $this) {
                $transaction->setOrder(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s %s', $this->id, $this->status->value);
    }
}
