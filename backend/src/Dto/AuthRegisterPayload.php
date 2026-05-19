<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class AuthRegisterPayload
{
    public function __construct(
        #[Assert\NotBlank(message: 'Nom trop court')]
        #[Assert\Length(min: 2, max: 120)]
        public string $name = '',
        #[Assert\NotBlank]
        #[Assert\Email(message: 'Email invalide')]
        #[Assert\Length(max: 200)]
        public string $email = '',
        #[Assert\NotBlank]
        #[Assert\Length(min: 8, max: 128, minMessage: 'Le mot de passe doit contenir au moins 8 caractères')]
        public string $password = '',
    ) {
    }
}
