<?php

namespace App\Service;

use App\Entity\Contact;
use App\Entity\Inscription;
use App\Entity\Order;
use App\Repository\ContactRepository;
use App\Repository\InscriptionRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

final class SubmissionService
{
    private const VALID_TYPES = ['contacts', 'orders', 'inscriptions'];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ContactRepository $contacts,
        private readonly OrderRepository $orders,
        private readonly InscriptionRepository $inscriptions,
        private readonly UserRepository $users,
    ) {
    }

    /** @return array{contacts: list<array<string, mixed>>, orders: list<array<string, mixed>>, inscriptions: list<array<string, mixed>>, users: list<array<string, mixed>>} */
    public function getAll(): array
    {
        return [
            'contacts' => array_map(
                static fn (Contact $c) => $c->toArray(),
                $this->contacts->findBy([], ['createdAt' => 'DESC'])
            ),
            'orders' => array_map(
                static fn (Order $o) => $o->toArray(),
                $this->orders->findBy([], ['createdAt' => 'DESC'])
            ),
            'inscriptions' => array_map(
                static fn (Inscription $i) => $i->toArray(),
                $this->inscriptions->findBy([], ['createdAt' => 'DESC'])
            ),
            'users' => array_map(
                static fn ($u) => $u->toPublicArray(),
                $this->users->findBy([], ['createdAt' => 'DESC'])
            ),
        ];
    }

    /** @return array{items: list<array<string, mixed>>, total: int, limit: int, offset: int} */
    public function listByType(string $type, int $limit, int $offset): array
    {
        $this->assertType($type);
        $limit = min(max($limit, 1), 200);
        $offset = max($offset, 0);

        return match ($type) {
            'contacts' => $this->paginateContacts($limit, $offset),
            'orders' => $this->paginateOrders($limit, $offset),
            'inscriptions' => $this->paginateInscriptions($limit, $offset),
        };
    }

    /** @return array<string, mixed> */
    public function getOne(string $type, string $id): array
    {
        $this->assertType($type);

        $entity = match ($type) {
            'contacts' => $this->contacts->find($id),
            'orders' => $this->orders->find($id),
            'inscriptions' => $this->inscriptions->find($id),
        };

        if ($entity === null) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Soumission introuvable');
        }

        return $entity->toArray();
    }

    public function delete(string $type, string $id): void
    {
        $this->assertType($type);

        $entity = match ($type) {
            'contacts' => $this->contacts->find($id),
            'orders' => $this->orders->find($id),
            'inscriptions' => $this->inscriptions->find($id),
        };

        if ($entity === null) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Soumission introuvable');
        }

        $this->em->remove($entity);
        $this->em->flush();
    }

    /** @return array{contacts: int, orders: int, inscriptions: int, users: int, admins: int} */
    public function getStats(): array
    {
        return [
            'contacts' => $this->contacts->count([]),
            'orders' => $this->orders->count([]),
            'inscriptions' => $this->inscriptions->count([]),
            'users' => $this->users->count([]),
            'admins' => $this->users->count(['role' => 'admin']),
        ];
    }

    public function saveContact(array $data): Contact
    {
        $contact = (new Contact())
            ->setId(Uuid::v4()->toRfc4122())
            ->setName($data['name'])
            ->setEmail($data['email'])
            ->setPhone($data['phone'] ?? null)
            ->setSubject($data['subject'] ?? null)
            ->setMessage($data['message'])
            ->setCreatedAt((new \DateTimeImmutable())->format(\DateTimeInterface::ATOM));

        $this->em->persist($contact);
        $this->em->flush();

        return $contact;
    }

    public function saveOrder(array $data, string $type = 'order'): Order
    {
        $order = (new Order())
            ->setId(Uuid::v4()->toRfc4122())
            ->setType($type)
            ->setName($data['name'])
            ->setEmail($data['email'])
            ->setPhone($data['phone'] ?? null)
            ->setProductTitle($data['productTitle'])
            ->setMessage($data['message'] ?? null)
            ->setCreatedAt((new \DateTimeImmutable())->format(\DateTimeInterface::ATOM));

        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }

    public function saveInscription(array $data): Inscription
    {
        $inscription = (new Inscription())
            ->setId(Uuid::v4()->toRfc4122())
            ->setName($data['name'])
            ->setEmail($data['email'])
            ->setPhone($data['phone'] ?? null)
            ->setFormationTitle($data['formationTitle'])
            ->setMessage($data['message'] ?? null)
            ->setCreatedAt((new \DateTimeImmutable())->format(\DateTimeInterface::ATOM));

        $this->em->persist($inscription);
        $this->em->flush();

        return $inscription;
    }

    private function assertType(string $type): void
    {
        if (!in_array($type, self::VALID_TYPES, true)) {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException(
                'Type invalide (contacts, orders, inscriptions)'
            );
        }
    }

    /** @return array{items: list<array<string, mixed>>, total: int, limit: int, offset: int} */
    private function paginateContacts(int $limit, int $offset): array
    {
        return [
            'items' => array_map(
                static fn (Contact $c) => $c->toArray(),
                $this->contacts->findPaginated($limit, $offset)
            ),
            'total' => $this->contacts->count([]),
            'limit' => $limit,
            'offset' => $offset,
        ];
    }

    /** @return array{items: list<array<string, mixed>>, total: int, limit: int, offset: int} */
    private function paginateOrders(int $limit, int $offset): array
    {
        return [
            'items' => array_map(
                static fn (Order $o) => $o->toArray(),
                $this->orders->findPaginated($limit, $offset)
            ),
            'total' => $this->orders->count([]),
            'limit' => $limit,
            'offset' => $offset,
        ];
    }

    /** @return array{items: list<array<string, mixed>>, total: int, limit: int, offset: int} */
    private function paginateInscriptions(int $limit, int $offset): array
    {
        return [
            'items' => array_map(
                static fn (Inscription $i) => $i->toArray(),
                $this->inscriptions->findPaginated($limit, $offset)
            ),
            'total' => $this->inscriptions->count([]),
            'limit' => $limit,
            'offset' => $offset,
        ];
    }
}
