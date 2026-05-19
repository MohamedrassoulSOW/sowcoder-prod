<?php

namespace App\Entity;

use App\Repository\BlogCommentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlogCommentRepository::class)]
#[ORM\Table(name: 'blog_comments')]
#[ORM\Index(columns: ['article_slug', 'created_at'], name: 'idx_blog_comments_slug_created')]
class BlogComment
{
    #[ORM\Id]
    #[ORM\Column(length: 36)]
    private string $id;

    #[ORM\Column(name: 'article_slug', length: 120)]
    private string $articleSlug;

    #[ORM\Column(name: 'author_name', length: 120)]
    private string $authorName;

    #[ORM\Column(name: 'author_email', length: 200, nullable: true)]
    private ?string $authorEmail = null;

    #[ORM\Column(name: 'user_id', length: 36, nullable: true)]
    private ?string $userId = null;

    #[ORM\Column(type: 'text')]
    private string $body;

    #[ORM\Column(name: 'created_at', length: 32)]
    private string $createdAt;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getArticleSlug(): string
    {
        return $this->articleSlug;
    }

    public function setArticleSlug(string $articleSlug): static
    {
        $this->articleSlug = $articleSlug;

        return $this;
    }

    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    public function setAuthorName(string $authorName): static
    {
        $this->authorName = $authorName;

        return $this;
    }

    public function getAuthorEmail(): ?string
    {
        return $this->authorEmail;
    }

    public function setAuthorEmail(?string $authorEmail): static
    {
        $this->authorEmail = $authorEmail;

        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'articleSlug' => $this->articleSlug,
            'authorName' => $this->authorName,
            'authorEmail' => $this->authorEmail,
            'userId' => $this->userId,
            'body' => $this->body,
            'createdAt' => $this->createdAt,
        ];
    }
}
