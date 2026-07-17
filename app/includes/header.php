<?php
declare(strict_types=1);
/** @var array $config */
/** @var string $pageTitle */
/** @var string $currentPage */
/** @var string $bodyClass */
/** @var array|null $currentUser */
$userRole = (string) ($currentUser['role'] ?? 'user');
$isAdmin = $userRole === 'admin';
$roleLabel = $isAdmin ? 'Administrateur' : 'Utilisateur';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e((string) ($config['meta_description'] ?? $config['tagline'] ?? 'SowCoder')) ?>">
    <title><?= e($pageTitle) ?></title>
    <link rel="icon" href="<?= e(asset('images/logo-sc.png')) ?>" type="image/png">
    <link rel="apple-touch-icon" href="<?= e(asset('images/logo-sc.png')) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/styles.css')) ?>?v=<?= e((string) @filemtime(__DIR__ . '/../../assets/css/styles.css')) ?>">
</head>
<body class="<?= e($bodyClass) ?>">
    <a class="skip-link" href="#main">Aller au contenu</a>

    <header class="site-header" data-header>
        <div class="container header-inner">
            <a class="brand" href="<?= e(page_url('home')) ?>" aria-label="SowCoder — Accueil">
                <img class="brand-logo" src="<?= e(asset('images/logo-sc.png')) ?>" alt="" width="40" height="40">
                <span class="brand-name">Sow<span>Coder</span></span>
            </a>

            <nav class="site-nav" id="site-nav" data-nav>
                <a href="<?= e(page_url('home')) ?>" class="<?= is_active('home') ? 'is-active' : '' ?>">Accueil</a>
                <a href="<?= e(page_url('services')) ?>" class="<?= is_active('services') ? 'is-active' : '' ?>">Services</a>
                <a href="<?= e(page_url('blog')) ?>" class="<?= is_active('blog') ? 'is-active' : '' ?>">Blog</a>
                <a href="<?= e(page_url('about')) ?>" class="<?= is_active('about') ? 'is-active' : '' ?>">À propos</a>
                <a href="<?= e(page_url('contact')) ?>" class="<?= is_active('contact') ? 'is-active' : '' ?>">Contact</a>
                <?php if (!$currentUser): ?>
                    <a class="nav-btn nav-btn-ghost nav-auth-mobile <?= is_active('login') ? 'is-active' : '' ?>" href="<?= e(page_url('login')) ?>">Connexion</a>
                    <a class="nav-btn nav-btn-solid nav-auth-mobile <?= is_active('register') ? 'is-active' : '' ?>" href="<?= e(page_url('register')) ?>">Inscription</a>
                <?php endif; ?>
            </nav>

            <div class="header-end">
                <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" data-nav-toggle>
                    <span class="sr-only">Menu</span>
                    <span></span><span></span>
                </button>

                <?php if ($currentUser): ?>
                    <?php
                    $navAvatarUrl = auth_avatar_url($currentUser);
                    $navInitials = auth_avatar_initials($currentUser);
                    ?>
                    <div class="user-menu" data-user-menu>
                        <button
                            class="user-menu-toggle"
                            type="button"
                            aria-expanded="false"
                            aria-haspopup="menu"
                            aria-controls="user-menu-panel"
                            data-user-menu-toggle
                        >
                            <span class="nav-avatar" aria-hidden="true">
                                <?php if ($navAvatarUrl !== ''): ?>
                                    <img src="<?= e($navAvatarUrl) ?>" alt="">
                                <?php else: ?>
                                    <span><?= e($navInitials) ?></span>
                                <?php endif; ?>
                            </span>
                            <span class="sr-only">Ouvrir le menu compte</span>
                        </button>

                        <div class="user-menu-panel" id="user-menu-panel" role="menu" data-user-menu-panel>
                            <div class="user-menu-head">
                                <div class="user-menu-avatar" aria-hidden="true">
                                    <?php if ($navAvatarUrl !== ''): ?>
                                        <img src="<?= e($navAvatarUrl) ?>" alt="">
                                    <?php else: ?>
                                        <span><?= e($navInitials) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="user-menu-meta">
                                    <p class="user-menu-name"><?= e((string) $currentUser['name']) ?></p>
                                    <p class="user-menu-email"><?= e((string) $currentUser['email']) ?></p>
                                    <span class="user-menu-role <?= $isAdmin ? 'is-admin' : 'is-user' ?>"><?= e($roleLabel) ?></span>
                                </div>
                            </div>

                            <div class="user-menu-links">
                                <a href="<?= e(page_url('profile')) ?>" class="<?= is_active('profile') ? 'is-active' : '' ?>" role="menuitem">Mon profil</a>
                                <?php if ($isAdmin): ?>
                                    <a href="<?= e(page_url('admin')) ?>" class="<?= is_active('admin') ? 'is-active' : '' ?>" role="menuitem">Dashboard admin</a>
                                <?php endif; ?>
                                <a href="<?= e(page_url('newsletter')) ?>" role="menuitem">Newsletter</a>
                                <a href="<?= e(page_url('forgot-password')) ?>" role="menuitem">Mot de passe oublié</a>
                            </div>

                            <div class="user-menu-footer">
                                <a class="user-menu-logout" href="<?= e(page_url('logout')) ?>" role="menuitem">Déconnexion</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="header-auth">
                        <a class="nav-btn nav-btn-ghost <?= is_active('login') ? 'is-active' : '' ?>" href="<?= e(page_url('login')) ?>">Connexion</a>
                        <a class="nav-btn nav-btn-solid <?= is_active('register') ? 'is-active' : '' ?>" href="<?= e(page_url('register')) ?>">Inscription</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main id="main">
