<?php

declare(strict_types=1);

namespace App\StateMachine;

use App\StateMachine\Exception\StateMachineExecutionException;

interface StateMachineInterface
{
    /**
     * @throws StateMachineExecutionException
     */
    public function can(object $subject, string $graphName, string $transition): bool;

    /**
     * @param array<string, mixed> $context
     *
     * @throws StateMachineExecutionException
     */
    public function apply(object $subject, string $graphName, string $transition, array $context = []): void;
}
