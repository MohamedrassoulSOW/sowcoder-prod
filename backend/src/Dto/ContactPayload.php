<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class ContactPayload
{
    public function __construct(
        #[Assert\NotBlank(message: 'Nom trop court')]
        #[Assert\Length(min: 2, max: 120)]
        public string $name = '',
        #[Assert\NotBlank]
        #[Assert\Email(message: 'Email invalide')]
        #[Assert\Length(max: 200)]
        public string $email = '',
        #[Assert\Length(max: 30)]
        public ?string $phone = null,
        #[Assert\Length(max: 200)]
        public ?string $subject = null,
        #[Assert\NotBlank(message: 'Message trop court')]
        #[Assert\Length(min: 10, max: 5000)]
        public string $message = '',
    ) {
    }
}
