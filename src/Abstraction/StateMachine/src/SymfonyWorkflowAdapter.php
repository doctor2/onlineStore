<?php

declare(strict_types=1);

namespace App\Abstraction\StateMachine;

use App\StateMachine\Exception\StateMachineExecutionException;
use Symfony\Component\Workflow\Exception\ExceptionInterface as WorkflowExceptionInterface;
use Symfony\Component\Workflow\Registry;

final class SymfonyWorkflowAdapter implements StateMachineInterface
{
    public function __construct(private Registry $symfonyWorkflowRegistry)
    {
    }

    public function can(object $subject, string $graphName, string $transition): bool
    {
        try {
            return $this->symfonyWorkflowRegistry->get($subject, $graphName)->can($subject, $transition);
        } catch (WorkflowExceptionInterface $exception) {
            throw new StateMachineExecutionException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    public function apply(object $subject, string $graphName, string $transition, array $context = []): void
    {
        try {
            $this->symfonyWorkflowRegistry->get($subject, $graphName)->apply($subject, $transition, $context);
        } catch (WorkflowExceptionInterface $exception) {
            throw new StateMachineExecutionException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
