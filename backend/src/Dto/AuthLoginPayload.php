<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class AuthLoginPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email(message: 'Email invalide')]
        public string $email = '',
        #[Assert\NotBlank(message: 'Mot de passe requis')]
        public string $password = '',
    ) {
    }
}
