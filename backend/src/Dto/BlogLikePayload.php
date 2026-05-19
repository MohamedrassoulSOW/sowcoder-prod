<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class BlogLikePayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 8, max: 80)]
        public string $visitorId = '',
    ) {
    }
}
