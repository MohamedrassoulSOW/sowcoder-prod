<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class BlogCommentPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 2, max: 120)]
        public string $authorName = '',
        #[Assert\Email]
        #[Assert\Length(max: 200)]
        public ?string $authorEmail = null,
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 3000)]
        public string $body = '',
        #[Assert\Length(max: 36)]
        public ?string $visitorId = null,
    ) {
    }
}
