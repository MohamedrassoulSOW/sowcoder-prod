<?php

namespace App\Controller;

use App\Dto\AuthLoginPayload;
use App\Dto\AuthRegisterPayload;
use App\Entity\User;
use App\Http\ApiResponse;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

final class AuthController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly JWTTokenManagerInterface $jwt,
        private readonly string $adminEmail,
    ) {
    }

    #[Route('/api/auth/register', name: 'api_auth_register', methods: ['POST'])]
    public function register(#[MapRequestPayload] AuthRegisterPayload $payload): JsonResponse
    {
        $email = strtolower($payload->email);

        if ($this->adminEmail !== '' && strcasecmp($email, $this->adminEmail) === 0) {
            return ApiResponse::error('Cet email est réservé à l\'administration', 403);
        }

        if ($this->users->findByEmail($email) !== null) {
            return ApiResponse::error('Un compte existe déjà avec cet email', 409);
        }

        $user = (new User())
            ->setId(Uuid::v4()->toRfc4122())
            ->setName(trim($payload->name))
            ->setEmail($email)
            ->setRole('user')
            ->setCreatedAt((new \DateTimeImmutable())->format(\DateTimeInterface::ATOM));

        $user->setPasswordHash($this->hasher->hashPassword($user, $payload->password));

        $this->em->persist($user);
        $this->em->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Compte créé avec succès',
            'token' => $this->jwt->create($user),
            'user' => $user->toPublicArray(),
        ], 201);
    }

    #[Route('/api/auth/login', name: 'api_auth_login', methods: ['POST'])]
    public function login(#[MapRequestPayload] AuthLoginPayload $payload): JsonResponse
    {
        $user = $this->users->findByEmail($payload->email);

        if ($user === null || !$this->hasher->isPasswordValid($user, $payload->password)) {
            return ApiResponse::error('Email ou mot de passe incorrect', 401);
        }

        return new JsonResponse([
            'success' => true,
            'message' => 'Connexion réussie',
            'token' => $this->jwt->create($user),
            'user' => $user->toPublicArray(),
        ]);
    }

    #[Route('/api/auth/me', name: 'api_auth_me', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function me(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return ApiResponse::error('Session invalide', 401);
        }

        return new JsonResponse([
            'success' => true,
            'user' => $user->toPublicArray(),
        ]);
    }
}
