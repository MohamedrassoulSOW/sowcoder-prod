<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

final class ApiAdminUser implements UserInterface
{
    public function getUserIdentifier(): string
    {
        return 'api-admin';
    }

    /** @return list<string> */
    public function getRoles(): array
    {
        return ['ROLE_ADMIN'];
    }

    public function eraseCredentials(): void
    {
    }
}
