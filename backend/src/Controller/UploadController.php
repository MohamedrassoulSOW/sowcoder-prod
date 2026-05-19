<?php

namespace App\Controller;

use App\Http\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

final class UploadController extends AbstractController
{
    private const ALLOWED_MIME = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ];

    public function __construct(private readonly string $uploadsDir)
    {
    }

    #[Route('/api/admin/upload', name: 'admin_upload', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function upload(Request $request): JsonResponse
    {
        /** @var UploadedFile|null $file */
        $file = $request->files->get('image');

        if ($file === null) {
            return ApiResponse::error('Aucun fichier reçu', 400);
        }

        if (!in_array($file->getMimeType(), self::ALLOWED_MIME, true)) {
            return ApiResponse::error('Format accepté : JPEG, PNG, WebP ou GIF (max 5 Mo)', 400);
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            return ApiResponse::error('Fichier trop volumineux (max 5 Mo)', 400);
        }

        if (!is_dir($this->uploadsDir) && !mkdir($this->uploadsDir, 0775, true)) {
            return ApiResponse::error('Impossible de créer le dossier uploads', 500);
        }

        $ext = strtolower($file->guessExtension() ?: 'jpg');
        $filename = Uuid::v4()->toRfc4122().'.'.$ext;
        $file->move($this->uploadsDir, $filename);

        $url = '/uploads/'.$filename;

        return ApiResponse::ok(['url' => $url, 'filename' => $filename]);
    }

}
