<?php

namespace App\EventSubscriber;

use App\Security\ApiAdminUser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

final class AdminApiKeySubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly string $adminApiKey,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onRequest', 9]];
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!str_starts_with($request->getPathInfo(), '/api/admin')) {
            return;
        }

        if ($this->adminApiKey === '') {
            return;
        }

        $key = $request->headers->get('x-api-key');
        if ($key === null || !hash_equals($this->adminApiKey, $key)) {
            return;
        }

        $this->tokenStorage->setToken(
            new UsernamePasswordToken(new ApiAdminUser(), 'api', ['ROLE_ADMIN'])
        );
    }
}
