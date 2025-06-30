<?php

namespace App\Bundle\OrderBundle\Entity\Enum;

interface OrderStatusTransitions
{
    public const GRAPH = 'order_state';
    public const TRANSITION_PAY = 'pay';
    public const TRANSITION_SHIP = 'ship';
    public const TRANSITION_DELIVER = 'deliver';
    public const TRANSITION_CANCEL = 'cancel';
}