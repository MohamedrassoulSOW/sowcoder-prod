<?php

declare(strict_types=1);

$config = require __DIR__ . '/config.php';
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
    'admin' => 'Dashboard',
    'admin-save' => 'Dashboard',
    '404' => 'Page introuvable',
];

$siteName = (string) ($config['site_name'] ?? 'SowCoder');
$pageTitle = ($pageTitles[$currentPage] ?? $siteName) . ' — ' . $siteName;
$currentUser = auth_user();
