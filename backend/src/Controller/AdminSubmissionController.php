<?php

namespace App\Controller;

use App\Http\ApiResponse;
use App\Service\SubmissionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin/submissions')]
#[IsGranted('ROLE_ADMIN')]
final class AdminSubmissionController extends AbstractController
{
    public function __construct(private readonly SubmissionService $submissions)
    {
    }

    #[Route('/{type}', name: 'admin_submissions_list', methods: ['GET'])]
    public function list(string $type, Request $request): JsonResponse
    {
        $data = $this->submissions->listByType(
            $type,
            (int) $request->query->get('limit', 50),
            (int) $request->query->get('offset', 0),
        );

        return ApiResponse::ok($data);
    }

    #[Route('/{type}/{id}', name: 'admin_submissions_get', methods: ['GET'])]
    public function get(string $type, string $id): JsonResponse
    {
        return ApiResponse::ok($this->submissions->getOne($type, $id));
    }

    #[Route('/{type}/{id}', name: 'admin_submissions_delete', methods: ['DELETE'])]
    public function delete(string $type, string $id): JsonResponse
    {
        $this->submissions->delete($type, $id);

        return ApiResponse::ok(null, 200, 'Supprimé');
    }
}
