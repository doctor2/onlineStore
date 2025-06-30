<?php

namespace App\Domain\Order\Entity\Enum;

interface PaymentStatusTransitions
{
    public const GRAPH = 'payment_state';
    public const TRANSITION_PROCESS = 'process';
    public const TRANSITION_COMPLETE = 'complete';
    public const TRANSITION_FAIL = 'fail';
}