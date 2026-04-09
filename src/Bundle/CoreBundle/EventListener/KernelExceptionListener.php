<?php

namespace App\Bundle\CoreBundle\EventListener;

use App\Bundle\OrderBundle\Exception\OrderItemNotFoundException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\ValidationFailedException;

final class KernelExceptionListener
{
    private const EXCEPTION_MAP = [
        OrderItemNotFoundException::class => JsonResponse::HTTP_NOT_FOUND,
        ValidationFailedException::class => JsonResponse::HTTP_BAD_REQUEST
    ];

    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HandlerFailedException) {
            $exception = $exception->getPrevious();
        }

        $class = $exception::class;

        if (!array_key_exists($class, self::EXCEPTION_MAP)){
            return;
        }

        if ($exception instanceof ValidationFailedException) {
            $event->setResponse(new JsonResponse([
                'error' => $exception->getViolations()->get(0)->getMessage(),
            ], self::EXCEPTION_MAP[$class]));
        } else {
            $event->setResponse(new JsonResponse([
                'error' => $exception->getMessage(),
            ], self::EXCEPTION_MAP[$class]));
        }
    }
}
