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
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ((string) ($_SERVER['SERVER_PORT'] ?? '') === '443')
            || ((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'),
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

function auth_require_login(): void
{
    if (!auth_check()) {
        redirect_to('login', ['next' => 'profile']);
    }
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

function auth_find_by_id(int $id): ?array
{
    auth_ensure_role_column();
    $stmt = db()->prepare('SELECT id, name, email, password, role FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();

    return $row === false ? null : $row;
}

function auth_update_profile(int $userId, string $name, string $email): void
{
    $stmt = db()->prepare('UPDATE users SET name = :name, email = :email WHERE id = :id');
    $stmt->execute([
        'name' => $name,
        'email' => $email,
        'id' => $userId,
    ]);

    $user = auth_user();
    if ($user !== null && (int) $user['id'] === $userId) {
        auth_start();
        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['email'] = $email;
    }
}

function auth_update_password(int $userId, string $password): void
{
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = db()->prepare('UPDATE users SET password = :password WHERE id = :id');
    $stmt->execute([
        'password' => $hash,
        'id' => $userId,
    ]);
}

function auth_email_exists(string $email, ?int $exceptUserId = null): bool
{
    if ($exceptUserId === null) {
        return auth_find_by_email($email) !== null;
    }

    $stmt = db()->prepare('SELECT id FROM users WHERE email = :email AND id <> :id LIMIT 1');
    $stmt->execute(['email' => $email, 'id' => $exceptUserId]);

    return $stmt->fetch() !== false;
}

function auth_ensure_password_resets_table(): void
{
    static $ready = false;
    if ($ready) {
        return;
    }

    db()->exec(
        'CREATE TABLE IF NOT EXISTS password_resets (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id INT UNSIGNED NOT NULL,
            token_hash VARCHAR(64) NOT NULL,
            expires_at DATETIME NOT NULL,
            used_at DATETIME DEFAULT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_password_resets_user (user_id),
            KEY idx_password_resets_expires (expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $ready = true;
}

/**
 * Crée un token de reset et retourne le token en clair (à envoyer par e-mail).
 */
function auth_create_password_reset(int $userId): string
{
    auth_ensure_password_resets_table();

    // Invalider les anciens tokens non utilisés
    $invalidate = db()->prepare(
        'UPDATE password_resets SET used_at = NOW()
         WHERE user_id = :user_id AND used_at IS NULL'
    );
    $invalidate->execute(['user_id' => $userId]);

    $token = bin2hex(random_bytes(32));
    $hash = hash('sha256', $token);

    $stmt = db()->prepare(
        'INSERT INTO password_resets (user_id, token_hash, expires_at)
         VALUES (:user_id, :token_hash, DATE_ADD(NOW(), INTERVAL 1 HOUR))'
    );
    $stmt->execute([
        'user_id' => $userId,
        'token_hash' => $hash,
    ]);

    return $token;
}

function auth_find_valid_reset(string $token): ?array
{
    auth_ensure_password_resets_table();
    $hash = hash('sha256', $token);

    $stmt = db()->prepare(
        'SELECT pr.id, pr.user_id, u.email, u.name
         FROM password_resets pr
         INNER JOIN users u ON u.id = pr.user_id
         WHERE pr.token_hash = :token_hash
           AND pr.used_at IS NULL
           AND pr.expires_at > NOW()
         LIMIT 1'
    );
    $stmt->execute(['token_hash' => $hash]);
    $row = $stmt->fetch();

    return $row === false ? null : $row;
}

function auth_consume_reset(int $resetId): void
{
    $stmt = db()->prepare('UPDATE password_resets SET used_at = NOW() WHERE id = :id');
    $stmt->execute(['id' => $resetId]);
}

function absolute_page_url(string $page, array $query = []): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
    $path = page_url($page);

    if ($query !== []) {
        $sep = str_contains($path, '?') ? '&' : '?';
        $path .= $sep . http_build_query($query);
    }

    return $scheme . '://' . $host . $path;
}

function auth_send_password_reset_email(string $email, string $name, string $token): array
{
    $resetUrl = absolute_page_url('reset-password', ['token' => $token]);

    try {
        $sent = mailjet()->sendPasswordResetEmail($email, $name, $resetUrl);
    } catch (Throwable $e) {
        error_log('Mailjet reset password : ' . $e->getMessage());
        $sent = false;
    }

    return [
        'sent' => (bool) $sent,
        'reset_url' => $resetUrl,
    ];
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
