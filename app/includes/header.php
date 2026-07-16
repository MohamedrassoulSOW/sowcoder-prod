<?php
declare(strict_types=1);
/** @var array $config */
/** @var string $pageTitle */
/** @var string $currentPage */
/** @var string $bodyClass */
/** @var array|null $currentUser */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e((string) ($config['meta_description'] ?? $config['tagline'] ?? 'SowCoder')) ?>">
    <title><?= e($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/styles.css')) ?>">
</head>
<body class="<?= e($bodyClass) ?>">
    <a class="skip-link" href="#main">Aller au contenu</a>

    <header class="site-header" data-header>
        <div class="container header-inner">
            <a class="brand" href="<?= e(page_url('home')) ?>" aria-label="SowCoder — Accueil">
                <span class="brand-mark" aria-hidden="true">SC</span>
                <span class="brand-name">Sow<span>Coder</span></span>
            </a>

            <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" data-nav-toggle>
                <span class="sr-only">Menu</span>
                <span></span><span></span>
            </button>

            <nav class="site-nav" id="site-nav" data-nav>
                <a href="<?= e(page_url('home')) ?>" class="<?= is_active('home') ? 'is-active' : '' ?>">Accueil</a>
                <a href="<?= e(page_url('services')) ?>" class="<?= is_active('services') ? 'is-active' : '' ?>">Services</a>
                <a href="<?= e(page_url('about')) ?>" class="<?= is_active('about') ? 'is-active' : '' ?>">À propos</a>
                <a href="<?= e(page_url('contact')) ?>" class="<?= is_active('contact') ? 'is-active' : '' ?>">Contact</a>

                <span class="nav-divider" aria-hidden="true"></span>

                <?php if ($currentUser): ?>
                    <span class="nav-user">Bonjour, <?= e($currentUser['name']) ?></span>
                    <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                        <a class="nav-btn nav-btn-solid" href="<?= e(page_url('admin')) ?>">Dashboard</a>
                    <?php endif; ?>
                    <a class="nav-btn nav-btn-ghost" href="<?= e(page_url('logout')) ?>">Déconnexion</a>
                <?php else: ?>
                    <a class="nav-btn nav-btn-ghost <?= is_active('login') ? 'is-active' : '' ?>" href="<?= e(page_url('login')) ?>">Connexion</a>
                    <a class="nav-btn nav-btn-solid <?= is_active('register') ? 'is-active' : '' ?>" href="<?= e(page_url('register')) ?>">Inscription</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main id="main">
