<?php

namespace App\Controller;

use App\Dto\BlogCommentPayload;
use App\Dto\BlogLikePayload;
use App\Entity\User;
use App\Http\ApiResponse;
use App\Service\BlogService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class BlogController extends AbstractController
{
    public function __construct(private readonly BlogService $blog)
    {
    }

    #[Route('/api/blog', name: 'api_blog_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return ApiResponse::ok(['articles' => $this->blog->listArticles()]);
    }

    #[Route('/api/blog/{slug}', name: 'api_blog_show', methods: ['GET'])]
    public function show(string $slug, Request $request, #[CurrentUser] ?User $user = null): JsonResponse
    {
        $visitorId = $request->query->getString('visitorId');
        $likerKey = $visitorId !== '' || $user !== null
            ? BlogService::resolveLikerKey($user, $visitorId)
            : null;

        return ApiResponse::ok([
            'article' => $this->blog->getArticle($slug, $likerKey),
        ]);
    }

    #[Route('/api/blog/{slug}/comments', name: 'api_blog_comments', methods: ['GET'])]
    public function comments(string $slug, Request $request): JsonResponse
    {
        $limit = $request->query->getInt('limit', 50);
        $offset = $request->query->getInt('offset', 0);

        return ApiResponse::ok($this->blog->listComments($slug, $limit, $offset));
    }

    #[Route('/api/blog/{slug}/comments', name: 'api_blog_comments_post', methods: ['POST'])]
    public function postComment(
        string $slug,
        #[MapRequestPayload] BlogCommentPayload $payload,
        #[CurrentUser] ?User $user = null,
    ): JsonResponse {
        $comment = $this->blog->addComment($slug, $payload, $user);

        return ApiResponse::created(
            ['comment' => $comment->toArray()],
            'Commentaire publié'
        );
    }

    #[Route('/api/blog/{slug}/like', name: 'api_blog_like', methods: ['POST'])]
    public function toggleLike(
        string $slug,
        #[MapRequestPayload] BlogLikePayload $payload,
        #[CurrentUser] ?User $user = null,
    ): JsonResponse {
        $likerKey = BlogService::resolveLikerKey($user, $payload->visitorId);
        $result = $this->blog->toggleLike($slug, $likerKey);

        return new JsonResponse([
            'success' => true,
            ...$result,
        ]);
    }
}
