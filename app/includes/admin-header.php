<?php
declare(strict_types=1);
/** @var array $config */
/** @var string $pageTitle */
/** @var array $currentUser */
/** @var string $adminTab */
$tabs = [
    'overview' => 'Vue d’ensemble',
    'settings' => 'Coordonnées',
    'hero' => 'Accueil',
    'services' => 'Services',
    'projects' => 'Projets',
    'about' => 'À propos',
    'why' => 'Pourquoi nous',
    'testimonials' => 'Témoignages',
    'messages' => 'Messages',
];
$currentTabLabel = $tabs[$adminTab] ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/styles.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
</head>
<body class="page-admin">
    <div class="admin-shell">
        <header class="admin-mobile-bar">
            <a class="brand admin-brand" href="<?= e(page_url('admin')) ?>">
                <span class="brand-mark" aria-hidden="true">SC</span>
                <span class="brand-name">Sow<span>Coder</span></span>
            </a>
            <div class="admin-mobile-actions">
                <a href="<?= e(page_url('profile')) ?>">Profil</a>
                <a href="<?= e(page_url('home')) ?>">Site</a>
                <a href="<?= e(page_url('logout')) ?>">Sortir</a>
            </div>
        </header>

        <aside class="admin-sidebar">
            <a class="brand admin-brand" href="<?= e(page_url('admin')) ?>">
                <span class="brand-mark" aria-hidden="true">SC</span>
                <span class="brand-name">Sow<span>Coder</span></span>
            </a>
            <p class="admin-sidebar-label">Dashboard</p>

            <nav class="admin-nav">
                <?php foreach ($tabs as $key => $label): ?>
                    <a href="<?= e(page_url('admin') . '&tab=' . rawurlencode($key)) ?>" class="<?= $adminTab === $key ? 'is-active' : '' ?>">
                        <?= e($label) ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="admin-sidebar-foot">
                <a href="<?= e(page_url('profile')) ?>">Mon profil</a>
                <a href="<?= e(page_url('home')) ?>">← Voir le site</a>
                <a href="<?= e(page_url('logout')) ?>">Déconnexion</a>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <div>
                    <p class="eyebrow">Administration</p>
                    <h1><?= e($currentTabLabel) ?></h1>
                </div>
                <div class="admin-topbar-user">
                    <span><?= e($currentUser['name'] ?? 'Admin') ?></span>
                    <small><?= e($currentUser['email'] ?? '') ?></small>
                </div>
            </header>

            <div class="admin-mobile-section">
                <p class="admin-mobile-section-label">Section</p>
                <div class="admin-dropdown" data-admin-dropdown>
                    <button
                        type="button"
                        class="admin-dropdown-toggle"
                        data-admin-dropdown-toggle
                        aria-expanded="false"
                        aria-haspopup="listbox"
                    >
                        <span data-admin-dropdown-label><?= e($currentTabLabel) ?></span>
                        <span class="admin-dropdown-caret" aria-hidden="true"></span>
                    </button>
                    <ul class="admin-dropdown-menu" role="listbox" hidden data-admin-dropdown-menu>
                        <?php foreach ($tabs as $key => $label): ?>
                            <li role="option">
                                <a
                                    href="<?= e(page_url('admin') . '&tab=' . rawurlencode($key)) ?>"
                                    class="<?= $adminTab === $key ? 'is-active' : '' ?>"
                                >
                                    <?= e($label) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <main class="admin-content">
