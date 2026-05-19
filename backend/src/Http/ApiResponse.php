<?php

namespace App\Http;

use Symfony\Component\HttpFoundation\JsonResponse;

final class ApiResponse
{
    /** @param array<string, mixed>|null $data */
    public static function ok(?array $data = null, int $status = 200, ?string $message = null): JsonResponse
    {
        $body = ['success' => true];
        if ($message !== null) {
            $body['message'] = $message;
        }
        if ($data !== null) {
            $body['data'] = $data;
        }

        return new JsonResponse($body, $status);
    }

    public static function created(?array $data = null, ?string $message = null): JsonResponse
    {
        return self::ok($data, 201, $message);
    }

    /** @param list<array{field?: string, message: string}>|null $errors */
    public static function error(
        string $error,
        int $status = 400,
        ?array $errors = null,
    ): JsonResponse {
        $body = ['success' => false, 'error' => $error];
        if ($errors !== null) {
            $body['errors'] = $errors;
        }

        return new JsonResponse($body, $status);
    }
}
