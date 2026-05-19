<?php

/**
 * Routeur pour le serveur PHP intégré (php -S).
 */
$publicDir = __DIR__;
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = $publicDir.$path;

if ($path !== '/' && is_file($file)) {
    return false;
}

require $publicDir.'/index.php';
