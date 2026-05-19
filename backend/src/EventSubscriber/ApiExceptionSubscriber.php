<?php

namespace App\EventSubscriber;

use App\Http\ApiResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly bool $kernelDebug,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => 'onException'];
    }

    public function onException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();
        if (!str_starts_with($request->getPathInfo(), '/api')) {
            return;
        }

        $throwable = $event->getThrowable();

        if ($throwable instanceof ValidationFailedException) {
            $errors = [];
            foreach ($throwable->getViolations() as $violation) {
                $errors[] = [
                    'field' => $violation->getPropertyPath(),
                    'message' => (string) $violation->getMessage(),
                ];
            }
            $event->setResponse(ApiResponse::error('Validation échouée', 400, $errors));

            return;
        }

        $status = 500;
        $message = 'Erreur serveur interne';

        if ($throwable instanceof HttpExceptionInterface) {
            $status = $throwable->getStatusCode();
            $message = $throwable->getMessage() ?: $message;
        }

        if ($status >= 500) {
            error_log('[api] '.$throwable->getMessage()."\n".$throwable->getTraceAsString());
            if ($this->kernelDebug) {
                $message = $throwable->getMessage();
            }
        }

        $event->setResponse(ApiResponse::error($message, $status));
    }
}
