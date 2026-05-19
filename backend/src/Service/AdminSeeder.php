<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

final class AdminSeeder
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserRepository $users,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly string $adminEmail,
        private readonly string $adminPassword,
        private readonly string $adminName,
    ) {
    }

    public function seed(): void
    {
        if ($this->adminEmail === '' || $this->adminPassword === '') {
            return;
        }

        $email = strtolower($this->adminEmail);
        $existing = $this->users->findByEmail($email);

        if ($existing !== null) {
            if ($existing->getRole() !== 'admin') {
                $existing->setRole('admin');
            }
            $existing->setPasswordHash(
                $this->hasher->hashPassword($existing, $this->adminPassword)
            );
            $this->em->flush();

            return;
        }

        $user = (new User())
            ->setId(Uuid::v4()->toRfc4122())
            ->setName($this->adminName)
            ->setEmail($email)
            ->setRole('admin')
            ->setCreatedAt((new \DateTimeImmutable())->format(\DateTimeInterface::ATOM));

        $user->setPasswordHash($this->hasher->hashPassword($user, $this->adminPassword));

        $this->em->persist($user);
        $this->em->flush();
    }
}
