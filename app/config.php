<?php

declare(strict_types=1);

/**
 * Configuration technique uniquement.
 * Les infos du site (textes, contacts, services…) sont en base MySQL.
 *
 * En production Hostinger : renseigner DB_* et MAIL_* dans le fichier .env.
 * Les valeurs ci-dessous servent de repli (développement local WAMP).
 */
return [
    'base_url' => '', // laisser vide à la racine du domaine ; ex. '/sous-dossier' si besoin
    'db' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'name' => 'sowcoder',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'mail' => [
        'from' => 'contact@sowcoder.com',
        'from_name' => 'SowCoser',
        // false en production — true uniquement en local pour afficher le lien de reset
        'debug' => false,
    ],
];
