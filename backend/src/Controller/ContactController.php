<?php

namespace App\Controller;

use App\Dto\ContactPayload;
use App\Http\ApiResponse;
use App\Service\EmailService;
use App\Service\SubmissionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    public function __construct(
        private readonly SubmissionService $submissions,
        private readonly EmailService $email,
    ) {
    }

    #[Route('/api/contact', name: 'api_contact', methods: ['POST'])]
    public function post(#[MapRequestPayload] ContactPayload $payload): JsonResponse
    {
        $data = [
            'name' => trim($payload->name),
            'email' => trim($payload->email),
            'phone' => $payload->phone ? trim($payload->phone) : null,
            'subject' => $payload->subject ? trim($payload->subject) : null,
            'message' => trim($payload->message),
        ];

        $record = $this->submissions->saveContact($data);
        $mail = $this->email->formatContact($data);
        $emailResult = $this->email->sendNotification($mail['subject'], $mail['text'], $mail['html']);

        return ApiResponse::created(
            ['id' => $record->getId(), 'emailSent' => $emailResult['sent']],
            'Votre message a bien été envoyé. Nous vous répondrons sous 24h.'
        );
    }
}
