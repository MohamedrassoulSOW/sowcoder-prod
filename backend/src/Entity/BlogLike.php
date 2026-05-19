<?php

namespace App\Entity;

use App\Repository\BlogLikeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlogLikeRepository::class)]
#[ORM\Table(name: 'blog_likes')]
#[ORM\UniqueConstraint(name: 'uniq_blog_like', columns: ['article_slug', 'liker_key'])]
class BlogLike
{
    #[ORM\Id]
    #[ORM\Column(length: 36)]
    private string $id;

    #[ORM\Column(name: 'article_slug', length: 120)]
    private string $articleSlug;

    #[ORM\Column(name: 'liker_key', length: 80)]
    private string $likerKey;

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

    public function getLikerKey(): string
    {
        return $this->likerKey;
    }

    public function setLikerKey(string $likerKey): static
    {
        $this->likerKey = $likerKey;

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
}
