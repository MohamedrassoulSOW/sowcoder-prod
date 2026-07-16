<?php

declare(strict_types=1);

/**
 * Importe / réinitialise toutes les infos du site dans MySQL (sowcoder).
 * Usage : php scripts/seed-site.php
 */

$_SERVER['SCRIPT_NAME'] = '/mon-site-vitrine/index.php';

$config = require dirname(__DIR__) . '/app/config.php';
require_once dirname(__DIR__) . '/app/helpers.php';
require_once dirname(__DIR__) . '/app/db.php';
require_once dirname(__DIR__) . '/app/content_store.php';

echo "Création des tables...\n";
content_ensure_tables();

$defaults = content_defaults();
$seed = content_defaults_from_legacy_json($defaults);

echo "Enregistrement du contenu en base...\n";
content_save($seed);

$loaded = content_load();
echo 'OK — site_name: ' . ($loaded['settings']['site_name'] ?? '') . PHP_EOL;
echo 'OK — services: ' . count($loaded['services'] ?? []) . PHP_EOL;
echo 'OK — projects: ' . count($loaded['projects'] ?? []) . PHP_EOL;
echo 'OK — testimonials: ' . count($loaded['testimonials'] ?? []) . PHP_EOL;
echo 'OK — phones: ' . count($loaded['settings']['phones'] ?? []) . PHP_EOL;
echo "Terminé.\n";
