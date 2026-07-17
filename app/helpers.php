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

/**
 * URL d’un média : chemin assets/… ou URL absolue (https://…).
 */
function media_url(string $path): string
{
    $path = trim($path);
    if ($path === '') {
        return '';
    }

    if (preg_match('#^(https?:)?//#i', $path) === 1) {
        return $path;
    }

    return asset(ltrim($path, '/'));
}

/** @return array<string, string> */
function service_icon_options(): array
{
    return [
        'code' => 'Développement / code',
        'megaphone' => 'Marketing / communication',
        'palette' => 'Design / créa',
        'graduation' => 'Formation',
        'building' => 'Entreprises / institutions',
        'wrench' => 'Maintenance / technique',
    ];
}

function service_icon_svg(string $icon, int $size = 28): string
{
    $icon = preg_replace('/[^a-z0-9_-]/i', '', $icon) ?: 'code';
    $allowed = array_keys(service_icon_options());
    if (!in_array($icon, $allowed, true)) {
        $icon = 'code';
    }

    $paths = [
        'code' => '<polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>',
        'megaphone' => '<path d="M3 11v2a1 1 0 0 0 1 1h2l5 4V6L6 10H4a1 1 0 0 0-1 1z"/><path d="M15.5 8.5a5 5 0 0 1 0 7"/><path d="M18 6a9 9 0 0 1 0 12"/>',
        'palette' => '<circle cx="13.5" cy="6.5" r="1.5"/><circle cx="17.5" cy="10.5" r="1.5"/><circle cx="8.5" cy="7.5" r="1.5"/><circle cx="6.5" cy="12.5" r="1.5"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.9 0 1.7-.6 1.7-1.5 0-.4-.1-.8-.4-1.1-.3-.3-.4-.7-.4-1.1 0-.9.7-1.6 1.6-1.6H16c3.3 0 6-2.7 6-6 0-5-4.5-8.7-10-8.7z"/>',
        'graduation' => '<path d="M22 10 12 5 2 10l10 5 10-5z"/><path d="M6 12v5c0 1.7 2.7 3 6 3s6-1.3 6-3v-5"/>',
        'building' => '<rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01M16 6h.01M8 10h.01M16 10h.01M8 14h.01M16 14h.01"/>',
        'wrench' => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.8-3.8a6 6 0 0 1-7.9 7.9l-6.9 6.9a2.1 2.1 0 0 1-3-3l6.9-6.9a6 6 0 0 1 7.9-7.9l-3.8 3.8z"/>',
    ];

    $inner = $paths[$icon] ?? $paths['code'];
    $sizeAttr = (int) $size;

    return '<svg class="service-icon-svg" width="' . $sizeAttr . '" height="' . $sizeAttr . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $inner . '</svg>';
}
