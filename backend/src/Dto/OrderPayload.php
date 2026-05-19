<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class OrderPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 2, max: 120)]
        public string $name = '',
        #[Assert\NotBlank]
        #[Assert\Email]
        #[Assert\Length(max: 200)]
        public string $email = '',
        #[Assert\Length(max: 30)]
        public ?string $phone = null,
        #[Assert\NotBlank]
        #[Assert\Length(min: 2, max: 200)]
        public string $productTitle = '',
        #[Assert\Length(max: 2000)]
        public ?string $message = null,
    ) {
    }
}
