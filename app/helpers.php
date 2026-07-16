<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    global $config;
    $base = rtrim((string) ($config['base_url'] ?? ''), '/');
    $path = '/' . ltrim($path, '/');

    if ($path === '/') {
        return $base === '' ? '/' : $base . '/';
    }

    return ($base === '' ? '' : $base) . $path;
}

function page_url(string $page): string
{
    if ($page === 'home') {
        return url('/');
    }

    return url('/?page=' . rawurlencode($page));
}

function is_active(string $page): bool
{
    global $currentPage;

    return ($currentPage ?? 'home') === $page;
}

function asset(string $path): string
{
    return url('/assets/' . ltrim($path, '/'));
}
