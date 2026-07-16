<?php

declare(strict_types=1);

/**
 * Configuration technique uniquement.
 * Les infos du site (textes, contacts, services…) sont en base MySQL `sowcoder`.
 */
return [
    'base_url' => '', // laisser vide en local WAMP
    'db' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'name' => 'sowcoder',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
];
