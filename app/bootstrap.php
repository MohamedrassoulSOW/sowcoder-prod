<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';
load_env(dirname(__DIR__));

$config = require __DIR__ . '/config.php';

// Overlay depuis .env (Hostinger / production)
if (isset($_ENV['APP_BASE_URL'])) {
    $config['base_url'] = rtrim((string) $_ENV['APP_BASE_URL'], '/');
}

$dbEnvMap = [
    'DB_HOST' => 'host',
    'DB_PORT' => 'port',
    'DB_NAME' => 'name',
    'DB_USER' => 'user',
    'DB_PASS' => 'pass',
];
foreach ($dbEnvMap as $envKey => $configKey) {
    if (array_key_exists($envKey, $_ENV) && $_ENV[$envKey] !== '') {
        $config['db'][$configKey] = (string) $_ENV[$envKey];
    } elseif (getenv($envKey) !== false && getenv($envKey) !== '') {
        $config['db'][$configKey] = (string) getenv($envKey);
    }
}
// Mot de passe DB peut être volontairement vide en local
if (array_key_exists('DB_PASS', $_ENV)) {
    $config['db']['pass'] = (string) $_ENV['DB_PASS'];
}

if (isset($_ENV['MAILJET_FROM_EMAIL']) && $_ENV['MAILJET_FROM_EMAIL'] !== '') {
    $config['mail']['from'] = (string) $_ENV['MAILJET_FROM_EMAIL'];
}
if (isset($_ENV['MAILJET_FROM_NAME']) && $_ENV['MAILJET_FROM_NAME'] !== '') {
    $config['mail']['from_name'] = (string) $_ENV['MAILJET_FROM_NAME'];
}
if (isset($_ENV['MAIL_DEBUG'])) {
    $config['mail']['debug'] = filter_var($_ENV['MAIL_DEBUG'], FILTER_VALIDATE_BOOLEAN);
}

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/content_store.php';

auth_start();

// Détection automatique de la base URL sous WAMP (sous-dossier)
if ($config['base_url'] === '') {
    $scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    if ($scriptName !== '/' && $scriptName !== '\\' && $scriptName !== '.') {
        $config['base_url'] = rtrim($scriptName, '/');
    }
}

$content = content_load();
apply_content_settings($config, $content);

$allowedPages = [
    'home',
    'services',
    'about',
    'contact',
    'contact-submit',
    'login',
    'register',
    'logout',
    'profile',
    'forgot-password',
    'reset-password',
    'newsletter',
    'admin',
    'admin-save',
];
$currentPage = $_GET['page'] ?? 'home';
$bodyClass = '';
$adminTab = 'overview';

if (!in_array($currentPage, $allowedPages, true)) {
    http_response_code(404);
    $currentPage = '404';
}

$pageFile = __DIR__ . '/pages/' . $currentPage . '.php';

if (!is_file($pageFile)) {
    http_response_code(404);
    $pageFile = __DIR__ . '/pages/404.php';
    $currentPage = '404';
}

$pageTitles = [
    'home' => 'Accueil',
    'services' => 'Services',
    'about' => 'À propos',
    'contact' => 'Contact',
    'contact-submit' => 'Contact',
    'login' => 'Connexion',
    'register' => 'Inscription',
    'logout' => 'Déconnexion',
    'profile' => 'Mon profil',
    'forgot-password' => 'Mot de passe oublié',
    'reset-password' => 'Nouveau mot de passe',
    'newsletter' => 'Newsletter',
    'admin' => 'Dashboard',
    'admin-save' => 'Dashboard',
    '404' => 'Page introuvable',
];

$siteName = (string) ($config['site_name'] ?? 'SowCoder');
$pageTitle = ($pageTitles[$currentPage] ?? $siteName) . ' — ' . $siteName;
$currentUser = auth_user();
