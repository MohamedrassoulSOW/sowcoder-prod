<?php

namespace App\Service;

use App\Dto\BlogCommentPayload;
use App\Entity\BlogComment;
use App\Entity\BlogLike;
use App\Entity\User;
use App\Repository\BlogCommentRepository;
use App\Repository\BlogLikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

final class BlogService
{
    public function __construct(
        private readonly SiteContentService $content,
        private readonly BlogCommentRepository $comments,
        private readonly BlogLikeRepository $likes,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /** @return list<array<string, mixed>> */
    public function listArticles(): array
    {
        $posts = $this->getPosts();
        $slugs = array_map(static fn (array $p) => (string) $p['slug'], $posts);
        $likeCounts = $this->likes->countBySlugs($slugs);
        $commentCounts = $this->commentCountsBySlugs($slugs);

        return array_map(
            fn (array $post) => $this->enrichPost($post, $likeCounts, $commentCounts),
            $posts
        );
    }

    /** @return array<string, mixed> */
    public function getArticle(string $slug, ?string $likerKey = null): array
    {
        $post = $this->findPost($slug);
        $likeCount = $this->likes->countByArticle($slug);
        $commentCount = $this->comments->countByArticle($slug);
        $liked = $likerKey !== null && $this->likes->findOneByArticleAndLiker($slug, $likerKey) !== null;

        return array_merge($post, [
            'likeCount' => $likeCount,
            'commentCount' => $commentCount,
            'liked' => $liked,
        ]);
    }

    /** @return array{items: list<array<string, mixed>>, total: int, limit: int, offset: int} */
    public function listComments(string $slug, int $limit, int $offset): array
    {
        $this->findPost($slug);
        $limit = min(max($limit, 1), 100);
        $offset = max($offset, 0);

        return [
            'items' => array_map(
                static fn (BlogComment $c) => $c->toArray(),
                $this->comments->findByArticle($slug, $limit, $offset)
            ),
            'total' => $this->comments->countByArticle($slug),
            'limit' => $limit,
            'offset' => $offset,
        ];
    }

    public function addComment(string $slug, BlogCommentPayload $payload, ?User $user): BlogComment
    {
        $this->findPost($slug);

        $comment = (new BlogComment())
            ->setId(Uuid::v4()->toRfc4122())
            ->setArticleSlug($slug)
            ->setAuthorName($user !== null ? $user->getName() : trim($payload->authorName))
            ->setAuthorEmail($user !== null ? $user->getEmail() : ($payload->authorEmail ? trim($payload->authorEmail) : null))
            ->setUserId($user?->getId())
            ->setBody(trim($payload->body))
            ->setCreatedAt((new \DateTimeImmutable())->format(\DateTimeInterface::ATOM));

        $this->em->persist($comment);
        $this->em->flush();

        return $comment;
    }

    /** @return array{liked: bool, likeCount: int} */
    public function toggleLike(string $slug, string $likerKey): array
    {
        $this->findPost($slug);

        $existing = $this->likes->findOneByArticleAndLiker($slug, $likerKey);

        if ($existing !== null) {
            $this->em->remove($existing);
            $this->em->flush();

            return [
                'liked' => false,
                'likeCount' => $this->likes->countByArticle($slug),
            ];
        }

        $like = (new BlogLike())
            ->setId(Uuid::v4()->toRfc4122())
            ->setArticleSlug($slug)
            ->setLikerKey($likerKey)
            ->setCreatedAt((new \DateTimeImmutable())->format(\DateTimeInterface::ATOM));

        $this->em->persist($like);
        $this->em->flush();

        return [
            'liked' => true,
            'likeCount' => $this->likes->countByArticle($slug),
        ];
    }

    public static function resolveLikerKey(?User $user, string $visitorId): string
    {
        if ($user !== null) {
            return 'user:'.$user->getId();
        }

        return 'visitor:'.trim($visitorId);
    }

    /** @return list<array<string, mixed>> */
    private function getPosts(): array
    {
        $data = $this->content->load();
        /** @var list<array<string, mixed>> $posts */
        $posts = $data['blogPosts'] ?? [];

        return $posts;
    }

    /** @return array<string, mixed> */
    private function findPost(string $slug): array
    {
        foreach ($this->getPosts() as $post) {
            if (($post['slug'] ?? '') === $slug) {
                return $post;
            }
        }

        throw new NotFoundHttpException('Article introuvable');
    }

    /**
     * @param array<string, int> $likeCounts
     * @param array<string, int> $commentCounts
     *
     * @return array<string, mixed>
     */
    private function enrichPost(array $post, array $likeCounts, array $commentCounts): array
    {
        $slug = (string) $post['slug'];

        return array_merge($post, [
            'likeCount' => $likeCounts[$slug] ?? 0,
            'commentCount' => $commentCounts[$slug] ?? 0,
        ]);
    }

    /** @param list<string> $slugs */
    /** @return array<string, int> */
    private function commentCountsBySlugs(array $slugs): array
    {
        $counts = [];
        foreach ($slugs as $slug) {
            $counts[$slug] = $this->comments->countByArticle($slug);
        }

        return $counts;
    }
}
