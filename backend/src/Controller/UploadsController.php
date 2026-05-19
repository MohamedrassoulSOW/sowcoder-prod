<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UploadsController extends AbstractController
{
    public function __construct(private readonly string $uploadsDir)
    {
    }

    #[Route('/uploads/{filename}', name: 'uploads_file', requirements: ['filename' => '.+'], methods: ['GET'])]
    public function serveFile(string $filename): Response
    {
        $path = realpath($this->uploadsDir.DIRECTORY_SEPARATOR.$filename);
        $base = realpath($this->uploadsDir);

        if ($path === false || $base === false || !str_starts_with($path, $base) || !is_file($path)) {
            throw $this->createNotFoundException();
        }

        return new BinaryFileResponse($path);
    }
}
