<?php

namespace App\Controller;

use App\Http\ApiResponse;
use App\Service\SiteContentService;
use App\Service\SubmissionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin')]
#[IsGranted('ROLE_ADMIN')]
final class AdminController extends AbstractController
{
    public function __construct(
        private readonly SiteContentService $content,
        private readonly SubmissionService $submissions,
    ) {
    }

    #[Route('/content', name: 'admin_content_get', methods: ['GET'])]
    public function getContent(): JsonResponse
    {
        return ApiResponse::ok($this->content->load());
    }

    #[Route('/content', name: 'admin_content_put', methods: ['PUT'])]
    public function putContent(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $body */
        $body = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return ApiResponse::ok($this->content->save($body));
    }

    #[Route('/stats', name: 'admin_stats', methods: ['GET'])]
    public function stats(): JsonResponse
    {
        return ApiResponse::ok($this->submissions->getStats());
    }

    #[Route('/submissions', name: 'admin_submissions_all', methods: ['GET'])]
    public function allSubmissions(): JsonResponse
    {
        return ApiResponse::ok($this->submissions->getAll());
    }
}
