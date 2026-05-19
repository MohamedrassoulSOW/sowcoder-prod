<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class CartCheckoutPayload
{
    /** @var list<CartItemPayload> */
    #[Assert\NotBlank(message: 'Le panier est vide')]
    #[Assert\Count(min: 1)]
    #[Assert\Valid]
    public array $items;

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
        #[Assert\Length(max: 2000)]
        public ?string $message = null,
        array $items = [],
    ) {
        $this->items = self::normalizeItems($items);
    }

    /** @param list<CartItemPayload|array<string, mixed>> $items */
    private static function normalizeItems(array $items): array
    {
        return array_map(
            static fn (CartItemPayload|array $item): CartItemPayload => $item instanceof CartItemPayload
                ? $item
                : new CartItemPayload(
                    title: (string) ($item['title'] ?? ''),
                    price: (string) ($item['price'] ?? ''),
                    tag: isset($item['tag']) ? (string) $item['tag'] : null,
                ),
            $items
        );
    }
}
