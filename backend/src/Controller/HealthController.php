<?php

namespace App\Controller;

use App\Http\ApiResponse;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class HealthController extends AbstractController
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $uploadsDir,
    ) {
    }

    #[Route('/api/health', name: 'api_health', methods: ['GET'])]
    public function health(): JsonResponse
    {
        $database = 'ok';
        try {
            $this->connection->executeQuery('SELECT 1');
        } catch (\Throwable) {
            $database = 'error';
        }

        return new JsonResponse([
            'success' => true,
            'status' => $database === 'ok' ? 'ok' : 'degraded',
            'timestamp' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
            'services' => [
                'database' => $database,
                'uploads' => is_dir($this->uploadsDir) ? 'ok' : 'error',
            ],
        ]);
    }
}
