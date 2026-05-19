<?php

namespace App\Controller;

use App\Http\ApiResponse;
use App\Service\SiteContentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ContentController extends AbstractController
{
    public function __construct(private readonly SiteContentService $content)
    {
    }

    #[Route('/api/content', name: 'api_content', methods: ['GET'])]
    public function get(): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'data' => $this->content->load(),
        ]);
    }
}
