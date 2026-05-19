<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class CartItemPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 200)]
        public string $title = '',
        #[Assert\NotBlank]
        #[Assert\Length(max: 50)]
        public string $price = '',
        #[Assert\Length(max: 50)]
        public ?string $tag = null,
    ) {
    }
}
