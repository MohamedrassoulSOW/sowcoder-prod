<?php

namespace App\Controller;

use App\Dto\CartCheckoutPayload;
use App\Http\ApiResponse;
use App\Service\EmailService;
use App\Service\SubmissionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CartController extends AbstractController
{
    public function __construct(
        private readonly SubmissionService $submissions,
        private readonly EmailService $email,
    ) {
    }

    #[Route('/api/cart/checkout', name: 'api_cart_checkout', methods: ['POST'])]
    public function checkout(#[MapRequestPayload] CartCheckoutPayload $payload): JsonResponse
    {
        $itemsList = implode("\n", array_map(
            static fn ($i) => '• '.$i->title.' — '.$i->price,
            $payload->items
        ));

        $count = count($payload->items);
        $data = [
            'name' => trim($payload->name),
            'email' => trim($payload->email),
            'phone' => $payload->phone ? trim($payload->phone) : null,
            'productTitle' => sprintf('Panier (%d article%s)', $count, $count > 1 ? 's' : ''),
            'message' => trim(implode("\n", array_filter([
                $payload->message,
                '',
                'Articles:',
                $itemsList,
            ]))),
        ];

        $record = $this->submissions->saveOrder($data, 'cart_order');

        $subject = sprintf('[Panier] Commande de %s', $data['name']);
        $this->email->sendNotification(
            $subject,
            $data['message'] ?? '',
            '<h2>Commande panier</h2><pre>'.htmlspecialchars($itemsList).'</pre>'
        );

        return ApiResponse::created(
            ['id' => $record->getId()],
            'Commande envoyée avec succès'
        );
    }

    #[Route('/api/cart/sync', name: 'api_cart_sync', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function sync(): JsonResponse
    {
        return new JsonResponse(['success' => true, 'message' => 'Session active']);
    }
}
