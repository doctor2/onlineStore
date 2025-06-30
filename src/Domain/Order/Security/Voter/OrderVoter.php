<?php

namespace App\Domain\Order\Security\Voter;

use App\Domain\Cart\Service\GetCartService;
use App\Domain\Order\Entity\Enum\OrderStatusTransitions;
use App\Domain\Order\Entity\Order;
use App\Entity\User;
use App\StateMachine\StateMachineInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class OrderVoter extends Voter
{
    public function __construct(private GetCartService $getCartService, private StateMachineInterface $stateMachine)
    {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, ['NEW_ORDER', 'EDIT_ORDER'], true);
    }

    protected function voteOnAttribute(string $attribute, mixed $order, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            'NEW_ORDER' => $this->canCreateOrder($user),
            'EDIT_ORDER' => $this->canEditOrder($user, $order),
            default => false,
        };

    }

    private function canCreateOrder(User $user): bool
    {
        $cart = $this->getCartService->getCart($user);

        return $cart->getTotalAmount() > 0;
    }

    private function canEditOrder(User $user, ?Order $order): bool
    {
        return $order && $order->getUser() === $user
            && $this->stateMachine->can($order, OrderStatusTransitions::GRAPH, OrderStatusTransitions::TRANSITION_PAY);
    }
}