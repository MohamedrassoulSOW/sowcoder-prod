<?php

declare(strict_types=1);

/**
 * Charge le fichier .env à la racine du projet (via vlucas/phpdotenv).
 */
function load_env(?string $basePath = null): void
{
    static $loaded = false;
    if ($loaded) {
        return;
    }

    $basePath = $basePath ?? dirname(__DIR__);
    $autoload = $basePath . '/vendor/autoload.php';

    if (!is_file($autoload)) {
        throw new RuntimeException('Dépendances manquantes. Exécutez : composer install');
    }

    require_once $autoload;

    if (is_file($basePath . '/.env')) {
        $dotenv = Dotenv\Dotenv::createImmutable($basePath);
        $dotenv->safeLoad();
    }

    $loaded = true;
}

/**
 * Instance unique du service Mailjet.
 */
function mailjet(): App\MailjetService
{
    static $service = null;

    if ($service instanceof App\MailjetService) {
        return $service;
    }

    load_env();
    $service = new App\MailjetService();

    return $service;
}
