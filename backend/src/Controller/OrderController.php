<?php

namespace App\Controller;

use App\Dto\OrderPayload;
use App\Http\ApiResponse;
use App\Service\EmailService;
use App\Service\SubmissionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    public function __construct(
        private readonly SubmissionService $submissions,
        private readonly EmailService $email,
    ) {
    }

    #[Route('/api/orders', name: 'api_orders', methods: ['POST'])]
    public function post(#[MapRequestPayload] OrderPayload $payload): JsonResponse
    {
        $data = [
            'name' => trim($payload->name),
            'email' => trim($payload->email),
            'phone' => $payload->phone ? trim($payload->phone) : null,
            'productTitle' => trim($payload->productTitle),
            'message' => $payload->message ? trim($payload->message) : null,
        ];

        $record = $this->submissions->saveOrder($data);
        $mail = $this->email->formatOrder($data);
        $emailResult = $this->email->sendNotification($mail['subject'], $mail['text'], $mail['html']);

        return ApiResponse::created(
            ['id' => $record->getId(), 'emailSent' => $emailResult['sent']],
            'Votre demande de commande a été enregistrée.'
        );
    }
}
