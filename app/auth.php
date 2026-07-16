<?php

declare(strict_types=1);

function auth_start(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function auth_user(): ?array
{
    auth_start();

    if (empty($_SESSION['user']) || !is_array($_SESSION['user'])) {
        return null;
    }

    return $_SESSION['user'];
}

function auth_check(): bool
{
    return auth_user() !== null;
}

function auth_is_admin(): bool
{
    $user = auth_user();
    return $user !== null && ($user['role'] ?? '') === 'admin';
}

function auth_require_admin(): void
{
    if (!auth_check()) {
        redirect_to('login', ['next' => 'admin']);
    }

    if (!auth_is_admin()) {
        http_response_code(403);
        redirect_to('home', ['error' => 'forbidden']);
    }
}

function auth_login(array $user): void
{
    auth_start();
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'name' => (string) $user['name'],
        'email' => (string) $user['email'],
        'role' => (string) ($user['role'] ?? 'user'),
    ];
}

function auth_logout(): void
{
    auth_start();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', (bool) $params['secure'], (bool) $params['httponly']);
    }

    session_destroy();
}

function auth_ensure_role_column(): void
{
    static $done = false;
    if ($done) {
        return;
    }

    try {
        $cols = db()->query("SHOW COLUMNS FROM users LIKE 'role'")->fetch();
        if ($cols === false) {
            db()->exec("ALTER TABLE users ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'user' AFTER password");
        }
    } catch (Throwable $e) {
        // ignore if table missing during early setup
    }

    $done = true;
}

function auth_find_by_email(string $email): ?array
{
    auth_ensure_role_column();
    $stmt = db()->prepare('SELECT id, name, email, password, role FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $row = $stmt->fetch();

    return $row === false ? null : $row;
}

function auth_register(string $name, string $email, string $password): array
{
    auth_ensure_role_column();
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = db()->prepare(
        'INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)'
    );
    $stmt->execute([
        'name' => $name,
        'email' => $email,
        'password' => $hash,
        'role' => 'user',
    ]);

    return [
        'id' => (int) db()->lastInsertId(),
        'name' => $name,
        'email' => $email,
        'role' => 'user',
    ];
}

function csrf_token(): string
{
    auth_start();
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['_csrf'];
}

function csrf_verify(?string $token): bool
{
    auth_start();
    $sessionToken = (string) ($_SESSION['_csrf'] ?? '');

    return $sessionToken !== '' && is_string($token) && hash_equals($sessionToken, $token);
}

function redirect_to(string $page, array $query = []): never
{
    $url = page_url($page);
    if ($query !== []) {
        $sep = str_contains($url, '?') ? '&' : '?';
        $url .= $sep . http_build_query($query);
    }
    header('Location: ' . $url);
    exit;
}
