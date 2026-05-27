<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Si Apache envoie les routes SPA vers index.php, renvoie index.html (build React).
 */
final class SpaFallbackSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly string $spaIndexPath,
        private readonly string $kernelEnvironment,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => ['onException', -64]];
    }

    public function onException(ExceptionEvent $event): void
    {
        if ($this->kernelEnvironment === 'dev') {
            return;
        }

        if (!$event->isMainRequest()) {
            return;
        }

        $throwable = $event->getThrowable();
        if (!$throwable instanceof NotFoundHttpException) {
            return;
        }

        $request = $event->getRequest();
        if ($request->getMethod() !== 'GET' && $request->getMethod() !== 'HEAD') {
            return;
        }

        $path = $request->getPathInfo();
        if (str_starts_with($path, '/api') || str_starts_with($path, '/uploads')) {
            return;
        }

        if (!is_file($this->spaIndexPath)) {
            return;
        }

        $html = file_get_contents($this->spaIndexPath);
        if ($html === false) {
            return;
        }

        $event->setResponse(new Response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]));
    }
}
