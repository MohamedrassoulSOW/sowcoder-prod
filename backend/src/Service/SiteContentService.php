<?php

namespace App\Service;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class SiteContentService
{
    public function __construct(
        private readonly string $contentPath,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /** @return array<string, mixed> */
    public function load(): array
    {
        if (!is_file($this->contentPath)) {
            throw new \RuntimeException('Fichier de contenu introuvable');
        }

        $raw = file_get_contents($this->contentPath);
        if ($raw === false) {
            throw new \RuntimeException('Impossible de lire le contenu');
        }

        /** @var array<string, mixed> $data */
        $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);

        return $data;
    }

    /** @param array<string, mixed> $content */
    public function save(array $content): array
    {
        $this->validate($content);

        $json = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new \RuntimeException('Encodage JSON impossible');
        }

        if (file_put_contents($this->contentPath, $json."\n") === false) {
            throw new \RuntimeException('Impossible d\'enregistrer le contenu');
        }

        return $content;
    }

    /** @param array<string, mixed> $content */
    private function validate(array $content): void
    {
        $violations = $this->validator->validate($content, new Assert\Collection(
            fields: [
                'hero' => new Assert\Required(),
                'stats' => new Assert\Required(),
                'services' => new Assert\Required(),
                'projects' => new Assert\Required(),
                'whyUs' => new Assert\Required(),
                'testimonials' => new Assert\Required(),
                'formations' => new Assert\Required(),
                'blogPosts' => new Assert\Required(),
                'boutiqueProducts' => new Assert\Required(),
                'navLinks' => new Assert\Required(),
                'footerLinks' => new Assert\Required(),
                'socialLinks' => new Assert\Required(),
                'legalPages' => new Assert\Required(),
            ],
            allowExtraFields: false,
            allowMissingFields: false,
        ));

        if (count($violations) > 0) {
            throw new \Symfony\Component\Validator\Exception\ValidationFailedException($content, $violations);
        }
    }
}
