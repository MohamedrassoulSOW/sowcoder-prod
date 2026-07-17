<?php
declare(strict_types=1);
/** @var array $config */
/** @var string $pageTitle */
/** @var array $currentUser */
/** @var string $adminTab */

$navGroups = [
    'général' => [
        'overview' => 'Vue d’ensemble',
        'settings' => 'Coordonnées',
        'messages' => 'Messages',
    ],
    'contenu' => [
        'hero' => 'Accueil (Hero)',
        'services' => 'Services',
        'projects' => 'Projets',
        'blog' => 'Blog',
        'about' => 'À propos',
        'why' => 'Pourquoi nous',
        'testimonials' => 'Témoignages',
    ],
];

$tabs = [];
foreach ($navGroups as $groupTabs) {
    foreach ($groupTabs as $key => $label) {
        $tabs[$key] = $label;
    }
}

$tabHints = [
    'overview' => 'Pilot rapide et accès aux sections du site',
    'settings' => 'Identité, contacts et informations publiques',
    'hero' => 'Bannière principale de la page d’accueil',
    'services' => 'Offres affichées sur le site',
    'projects' => 'Réalisations mises en avant',
    'blog' => 'Articles publiés sur /?page=blog',
    'about' => 'Mission et valeurs',
    'why' => 'Arguments différenciants',
    'testimonials' => 'Avis clients',
    'messages' => 'Demandes reçues via le formulaire contact',
];

$currentTabLabel = $tabs[$adminTab] ?? 'Dashboard';
$currentTabHint = $tabHints[$adminTab] ?? '';
$messageBadge = 0;
try {
    $messageBadge = (int) db()->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();
} catch (Throwable $e) {
    $messageBadge = 0;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="icon" href="<?= e(asset('images/logo-sc.png')) ?>" type="image/png">
    <link rel="apple-touch-icon" href="<?= e(asset('images/logo-sc.png')) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/styles.css')) ?>?v=<?= e((string) @filemtime(__DIR__ . '/../../assets/css/styles.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>?v=<?= e((string) @filemtime(__DIR__ . '/../../assets/css/admin.css')) ?>">
</head>
<body class="page-admin">
    <div class="admin-shell">
        <header class="admin-mobile-bar">
            <a class="brand admin-brand" href="<?= e(page_url('admin')) ?>">
                <img class="brand-logo" src="<?= e(asset('images/logo-sc.png')) ?>" alt="" width="40" height="40">
                <span class="brand-name">Sow<span>Coder</span></span>
            </a>
            <div class="admin-mobile-actions">
                <a href="<?= e(page_url('home')) ?>" target="_blank" rel="noopener">Site</a>
                <a href="<?= e(page_url('logout')) ?>">Sortir</a>
            </div>
        </header>

        <aside class="admin-sidebar">
            <a class="brand admin-brand" href="<?= e(page_url('admin')) ?>">
                <img class="brand-logo" src="<?= e(asset('images/logo-sc.png')) ?>" alt="" width="40" height="40">
                <span class="brand-name">Sow<span>Coder</span></span>
            </a>
            <p class="admin-sidebar-label">Espace admin</p>

            <nav class="admin-nav" aria-label="Navigation dashboard">
                <?php foreach ($navGroups as $groupLabel => $groupTabs): ?>
                    <p class="admin-nav-group"><?= e(ucfirst($groupLabel)) ?></p>
                    <?php foreach ($groupTabs as $key => $label): ?>
                        <a
                            href="<?= e(page_url('admin') . '&tab=' . rawurlencode($key)) ?>"
                            class="<?= $adminTab === $key ? 'is-active' : '' ?>"
                        >
                            <span><?= e($label) ?></span>
                            <?php if ($key === 'messages' && $messageBadge > 0): ?>
                                <span class="admin-nav-badge"><?= (int) $messageBadge ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </nav>

            <div class="admin-sidebar-foot">
                <a href="<?= e(page_url('profile')) ?>">Mon profil</a>
                <a href="<?= e(page_url('home')) ?>" target="_blank" rel="noopener">Voir le site ↗</a>
                <a href="<?= e(page_url('logout')) ?>">Déconnexion</a>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <div class="admin-topbar-copy">
                    <p class="eyebrow">Administration</p>
                    <h1><?= e($currentTabLabel) ?></h1>
                    <?php if ($currentTabHint !== ''): ?>
                        <p class="admin-topbar-hint"><?= e($currentTabHint) ?></p>
                    <?php endif; ?>
                </div>
                <div class="admin-topbar-aside">
                    <div class="admin-topbar-user">
                        <span><?= e($currentUser['name'] ?? 'Admin') ?></span>
                        <small><?= e($currentUser['email'] ?? '') ?></small>
                    </div>
                    <a class="btn nav-btn-ghost admin-topbar-btn" href="<?= e(page_url('home')) ?>" target="_blank" rel="noopener">Voir le site</a>
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
                        <?php foreach ($navGroups as $groupLabel => $groupTabs): ?>
                            <li class="admin-dropdown-group" aria-hidden="true"><?= e(ucfirst($groupLabel)) ?></li>
                            <?php foreach ($groupTabs as $key => $label): ?>
                                <li role="option">
                                    <a
                                        href="<?= e(page_url('admin') . '&tab=' . rawurlencode($key)) ?>"
                                        class="<?= $adminTab === $key ? 'is-active' : '' ?>"
                                    >
                                        <?= e($label) ?>
                                        <?php if ($key === 'messages' && $messageBadge > 0): ?>
                                            (<?= (int) $messageBadge ?>)
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <main class="admin-content">
