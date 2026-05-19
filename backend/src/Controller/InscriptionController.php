<?php

namespace App\Controller;

use App\Dto\InscriptionPayload;
use App\Http\ApiResponse;
use App\Service\EmailService;
use App\Service\SubmissionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class InscriptionController extends AbstractController
{
    public function __construct(
        private readonly SubmissionService $submissions,
        private readonly EmailService $email,
    ) {
    }

    #[Route('/api/inscriptions', name: 'api_inscriptions', methods: ['POST'])]
    public function post(#[MapRequestPayload] InscriptionPayload $payload): JsonResponse
    {
        $data = [
            'name' => trim($payload->name),
            'email' => trim($payload->email),
            'phone' => $payload->phone ? trim($payload->phone) : null,
            'formationTitle' => trim($payload->formationTitle),
            'message' => $payload->message ? trim($payload->message) : null,
        ];

        $record = $this->submissions->saveInscription($data);
        $mail = $this->email->formatInscription($data);
        $emailResult = $this->email->sendNotification($mail['subject'], $mail['text'], $mail['html']);

        return ApiResponse::created(
            ['id' => $record->getId(), 'emailSent' => $emailResult['sent']],
            'Votre inscription a bien été enregistrée.'
        );
    }
}
