<?php

declare(strict_types=1);

auth_require_admin();

// Enregistrement sur la même URL (même onglet) — pas de page intermédiaire
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    require __DIR__ . '/admin-save.php';
    exit;
}

$adminTab = (string) ($_GET['tab'] ?? 'overview');
$allowedTabs = ['overview', 'settings', 'hero', 'services', 'projects', 'blog', 'about', 'why', 'testimonials', 'messages'];
if (!in_array($adminTab, $allowedTabs, true)) {
    $adminTab = 'overview';
}

$adminFormAction = page_url('admin') . '&tab=' . rawurlencode($adminTab);

$content = content_load();
$settings = $content['settings'] ?? [];
$status = (string) ($_GET['status'] ?? '');
$csrf = csrf_token();

$messages = [];
$messageCount = 0;
try {
    $messageCount = (int) db()->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();
    if ($adminTab === 'messages') {
        $messages = db()->query(
            'SELECT id, name, email, phone, subject, message, created_at
             FROM contact_messages ORDER BY created_at DESC LIMIT 100'
        )->fetchAll();
    }
} catch (Throwable $e) {
    $messages = [];
}

$stats = [
    'services' => count($content['services'] ?? []),
    'projects' => count($content['projects'] ?? []),
    'blog' => count($content['blog'] ?? []),
    'testimonials' => count($content['testimonials'] ?? []),
    'messages' => $messageCount,
];

$bodyClass = 'page-admin';
?>

<?php if ($status === 'saved'): ?>
    <div class="alert alert-ok">Modifications enregistrées. Le site public est à jour.</div>
<?php elseif ($status === 'deleted'): ?>
    <div class="alert alert-ok">Message supprimé.</div>
<?php elseif ($status === 'error'): ?>
    <div class="alert alert-error">Une erreur est survenue. Réessayez.</div>
<?php elseif ($status === 'csrf'): ?>
    <div class="alert alert-error">Session expirée. Rechargez la page et réessayez.</div>
<?php endif; ?>

<?php if ($adminTab === 'overview'): ?>
    <section class="admin-overview">
        <div class="admin-stat-grid">
            <article class="admin-stat">
                <p class="admin-stat-label">Services</p>
                <p class="admin-stat-value"><?= (int) $stats['services'] ?></p>
                <a href="<?= e(page_url('admin') . '&tab=services') ?>">Gérer →</a>
            </article>
            <article class="admin-stat">
                <p class="admin-stat-label">Projets</p>
                <p class="admin-stat-value"><?= (int) $stats['projects'] ?></p>
                <a href="<?= e(page_url('admin') . '&tab=projects') ?>">Gérer →</a>
            </article>
            <article class="admin-stat">
                <p class="admin-stat-label">Articles blog</p>
                <p class="admin-stat-value"><?= (int) $stats['blog'] ?></p>
                <a href="<?= e(page_url('admin') . '&tab=blog') ?>">Gérer →</a>
            </article>
            <article class="admin-stat <?= $stats['messages'] > 0 ? 'is-accent' : '' ?>">
                <p class="admin-stat-label">Messages</p>
                <p class="admin-stat-value"><?= (int) $stats['messages'] ?></p>
                <a href="<?= e(page_url('admin') . '&tab=messages') ?>">Ouvrir →</a>
            </article>
        </div>

        <div class="admin-section-block">
            <header class="admin-section-head">
                <h2>Raccourcis contenu</h2>
                <p>Modifiez chaque zone du site sans toucher au code.</p>
            </header>
            <div class="admin-cards admin-cards-dense">
                <a class="admin-card-link" href="<?= e(page_url('admin') . '&tab=hero') ?>">
                    <h3>Accueil</h3>
                    <p>Titre, texte et image du hero</p>
                </a>
                <a class="admin-card-link" href="<?= e(page_url('admin') . '&tab=services') ?>">
                    <h3>Services</h3>
                    <p><?= (int) $stats['services'] ?> service(s)</p>
                </a>
                <a class="admin-card-link" href="<?= e(page_url('admin') . '&tab=projects') ?>">
                    <h3>Projets</h3>
                    <p><?= (int) $stats['projects'] ?> réalisation(s)</p>
                </a>
                <a class="admin-card-link" href="<?= e(page_url('admin') . '&tab=blog') ?>">
                    <h3>Blog</h3>
                    <p><?= (int) $stats['blog'] ?> article(s)</p>
                </a>
                <a class="admin-card-link" href="<?= e(page_url('admin') . '&tab=about') ?>">
                    <h3>À propos</h3>
                    <p>Mission et valeurs</p>
                </a>
                <a class="admin-card-link" href="<?= e(page_url('admin') . '&tab=testimonials') ?>">
                    <h3>Témoignages</h3>
                    <p><?= (int) $stats['testimonials'] ?> avis</p>
                </a>
            </div>
        </div>

        <div class="admin-section-block">
            <header class="admin-section-head">
                <h2>Paramètres &amp; contact</h2>
                <p>Informations visibles sur tout le site.</p>
            </header>
            <div class="admin-cards">
                <article class="admin-card">
                    <h2>Coordonnées</h2>
                    <p><?= e((string) ($settings['email'] ?? '—')) ?><br><?= e((string) ($settings['address'] ?? '')) ?></p>
                    <a class="btn btn-primary" href="<?= e(page_url('admin') . '&tab=settings') ?>">Modifier</a>
                </article>
                <article class="admin-card">
                    <h2>Messages contact</h2>
                    <p><strong><?= (int) $messageCount ?></strong> message(s) reçus via le formulaire.</p>
                    <a class="btn btn-primary" href="<?= e(page_url('admin') . '&tab=messages') ?>">Boîte de réception</a>
                </article>
                <article class="admin-card">
                    <h2>Prévisualisation</h2>
                    <p>Vérifiez le rendu public après chaque modification.</p>
                    <a class="btn btn-primary" href="<?= e(page_url('home')) ?>" target="_blank" rel="noopener">Ouvrir le site</a>
                </article>
            </div>
        </div>

        <div class="admin-hint">
            <p>Astuce : après chaque édition, cliquez sur <strong>Enregistrer</strong>, puis ouvrez le site public pour contrôler le résultat.</p>
        </div>
    </section>
<?php endif; ?>

<?php if ($adminTab === 'settings'): ?>
    <form class="admin-form" method="post" action="<?= e($adminFormAction) ?>">
        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
        <input type="hidden" name="tab" value="settings">

        <div class="field">
            <label for="site_name">Nom du site</label>
            <input id="site_name" name="site_name" value="<?= e((string) ($settings['site_name'] ?? '')) ?>" required>
        </div>
        <div class="field">
            <label for="tagline">Slogan</label>
            <input id="tagline" name="tagline" value="<?= e((string) ($settings['tagline'] ?? '')) ?>">
        </div>
        <div class="field">
            <label for="meta_description">Description SEO (meta)</label>
            <textarea id="meta_description" name="meta_description" rows="2"><?= e((string) ($settings['meta_description'] ?? '')) ?></textarea>
        </div>
        <div class="field-row">
            <div class="field">
                <label for="email">E-mail public</label>
                <input id="email" type="email" name="email" value="<?= e((string) ($settings['email'] ?? '')) ?>">
            </div>
            <div class="field">
                <label for="address">Adresse</label>
                <input id="address" name="address" value="<?= e((string) ($settings['address'] ?? '')) ?>">
            </div>
        </div>

        <h2 class="admin-subtitle">Téléphones</h2>
        <div class="repeat-list" data-repeat="phones">
            <?php
            $phones = array_values($settings['phones'] ?? []);
            if ($phones === []) {
                $phones = [['label' => '', 'number' => '']];
            }
            foreach ($phones as $i => $phone):
            ?>
                <div class="repeat-item" data-repeat-item>
                    <div class="field-row">
                        <div class="field">
                            <label>Label</label>
                            <input name="phones[<?= (int) $i ?>][label]" value="<?= e((string) ($phone['label'] ?? '')) ?>" placeholder="Sénégal">
                        </div>
                        <div class="field">
                            <label>Numéro</label>
                            <input name="phones[<?= (int) $i ?>][number]" value="<?= e((string) ($phone['number'] ?? '')) ?>" placeholder="+221 ...">
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" data-remove-row>Supprimer</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn nav-btn-ghost" data-add-row data-target="phones">+ Ajouter un téléphone</button>

        <template data-template="phones">
            <div class="repeat-item" data-repeat-item>
                <div class="field-row">
                    <div class="field">
                        <label>Label</label>
                        <input name="phones[__INDEX__][label]" placeholder="Sénégal">
                    </div>
                    <div class="field">
                        <label>Numéro</label>
                        <input name="phones[__INDEX__][number]" placeholder="+221 ...">
                    </div>
                </div>
                <button type="button" class="btn btn-danger btn-sm" data-remove-row>Supprimer</button>
            </div>
        </template>

        <h2 class="admin-subtitle">WhatsApp</h2>
        <div class="repeat-list" data-repeat="whatsapp">
            <?php
            $whatsapp = array_values($settings['whatsapp'] ?? []);
            if ($whatsapp === []) {
                $whatsapp = [['label' => '', 'url' => '']];
            }
            foreach ($whatsapp as $i => $wa):
            ?>
                <div class="repeat-item" data-repeat-item>
                    <div class="field-row">
                        <div class="field">
                            <label>Label</label>
                            <input name="whatsapp[<?= (int) $i ?>][label]" value="<?= e((string) ($wa['label'] ?? '')) ?>">
                        </div>
                        <div class="field">
                            <label>Lien WhatsApp</label>
                            <input name="whatsapp[<?= (int) $i ?>][url]" value="<?= e((string) ($wa['url'] ?? '')) ?>" placeholder="https://wa.me/...">
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" data-remove-row>Supprimer</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn nav-btn-ghost" data-add-row data-target="whatsapp">+ Ajouter WhatsApp</button>

        <template data-template="whatsapp">
            <div class="repeat-item" data-repeat-item>
                <div class="field-row">
                    <div class="field">
                        <label>Label</label>
                        <input name="whatsapp[__INDEX__][label]">
                    </div>
                    <div class="field">
                        <label>Lien WhatsApp</label>
                        <input name="whatsapp[__INDEX__][url]" placeholder="https://wa.me/...">
                    </div>
                </div>
                <button type="button" class="btn btn-danger btn-sm" data-remove-row>Supprimer</button>
            </div>
        </template>

        <div class="admin-actions">
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
<?php endif; ?>

<?php if ($adminTab === 'hero'): ?>
    <?php $hero = $content['hero'] ?? []; ?>
    <form class="admin-form" method="post" action="<?= e($adminFormAction) ?>">
        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
        <input type="hidden" name="tab" value="hero">
        <div class="field">
            <label for="eyebrow">Sur-titre</label>
            <input id="eyebrow" name="eyebrow" value="<?= e((string) ($hero['eyebrow'] ?? '')) ?>">
        </div>
        <div class="field">
            <label for="title">Titre principal</label>
            <input id="title" name="title" value="<?= e((string) ($hero['title'] ?? '')) ?>" required>
        </div>
        <div class="field">
            <label for="lead">Texte d’introduction</label>
            <textarea id="lead" name="lead" rows="4"><?= e((string) ($hero['lead'] ?? '')) ?></textarea>
        </div>
        <div class="field-row">
            <div class="field">
                <label for="cta_primary">Bouton principal</label>
                <input id="cta_primary" name="cta_primary" value="<?= e((string) ($hero['cta_primary'] ?? '')) ?>">
            </div>
            <div class="field">
                <label for="cta_secondary">Bouton secondaire</label>
                <input id="cta_secondary" name="cta_secondary" value="<?= e((string) ($hero['cta_secondary'] ?? '')) ?>">
            </div>
        </div>
        <div class="field">
            <label for="image">URL de l’image hero</label>
            <input id="image" name="image" value="<?= e((string) ($hero['image'] ?? '')) ?>" placeholder="https://...">
        </div>
        <div class="field">
            <label for="image_alt">Texte alternatif image</label>
            <input id="image_alt" name="image_alt" value="<?= e((string) ($hero['image_alt'] ?? '')) ?>">
        </div>
        <div class="admin-actions">
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
<?php endif; ?>

<?php if ($adminTab === 'services'): ?>
    <?php $iconOptions = service_icon_options(); ?>
    <form class="admin-form" method="post" action="<?= e($adminFormAction) ?>">
        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
        <input type="hidden" name="tab" value="services">
        <div class="repeat-list" data-repeat="services">
            <?php foreach (array_values($content['services'] ?? []) as $i => $service): ?>
                <?php $currentIcon = (string) ($service['icon'] ?? 'code'); ?>
                <div class="repeat-item is-locked" data-repeat-item data-editable-item>
                    <div class="repeat-item-head">
                        <p class="repeat-item-label">Service <?= (int) ($i + 1) ?></p>
                        <div class="repeat-item-actions">
                            <button type="button" class="btn nav-btn-ghost btn-sm" data-edit-row>Modifier</button>
                            <button type="submit" class="btn btn-primary btn-sm" data-save-row>Enregistrer</button>
                            <button type="button" class="btn btn-danger btn-sm" data-remove-row>Supprimer</button>
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label>Titre</label>
                            <input name="services[<?= (int) $i ?>][title]" value="<?= e((string) ($service['title'] ?? '')) ?>">
                        </div>
                        <div class="field">
                            <label>Icône</label>
                            <select name="services[<?= (int) $i ?>][icon]">
                                <?php foreach ($iconOptions as $value => $label): ?>
                                    <option value="<?= e($value) ?>" <?= $currentIcon === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <label>Description</label>
                        <textarea name="services[<?= (int) $i ?>][description]" rows="3"><?= e((string) ($service['description'] ?? '')) ?></textarea>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn nav-btn-ghost" data-add-row data-target="services">+ Ajouter un service</button>
        <template data-template="services">
            <div class="repeat-item" data-repeat-item data-editable-item>
                <div class="repeat-item-head">
                    <p class="repeat-item-label">Nouveau service</p>
                    <div class="repeat-item-actions">
                        <button type="button" class="btn nav-btn-ghost btn-sm" data-edit-row>Modifier</button>
                        <button type="submit" class="btn btn-primary btn-sm" data-save-row>Enregistrer</button>
                        <button type="button" class="btn btn-danger btn-sm" data-remove-row>Supprimer</button>
                    </div>
                </div>
                <div class="field-row">
                    <div class="field">
                        <label>Titre</label>
                        <input name="services[__INDEX__][title]">
                    </div>
                    <div class="field">
                        <label>Icône</label>
                        <select name="services[__INDEX__][icon]">
                            <?php foreach ($iconOptions as $value => $label): ?>
                                <option value="<?= e($value) ?>" <?= $value === 'code' ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="field">
                    <label>Description</label>
                    <textarea name="services[__INDEX__][description]" rows="3"></textarea>
                </div>
            </div>
        </template>
        <div class="admin-actions admin-actions-sticky">
            <button class="btn btn-primary" type="submit">Enregistrer tous les services</button>
        </div>
    </form>
<?php endif; ?>

<?php if ($adminTab === 'projects'): ?>
    <form class="admin-form" method="post" action="<?= e($adminFormAction) ?>">
        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
        <input type="hidden" name="tab" value="projects">
        <div class="repeat-list" data-repeat="projects">
            <?php foreach (array_values($content['projects'] ?? []) as $i => $project): ?>
                <div class="repeat-item" data-repeat-item>
                    <div class="field">
                        <label>Titre</label>
                        <input name="projects[<?= (int) $i ?>][title]" value="<?= e((string) ($project['title'] ?? '')) ?>">
                    </div>
                    <div class="field">
                        <label>Description</label>
                        <textarea name="projects[<?= (int) $i ?>][description]" rows="3"><?= e((string) ($project['description'] ?? '')) ?></textarea>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label>Catégorie</label>
                            <input name="projects[<?= (int) $i ?>][category]" value="<?= e((string) ($project['category'] ?? '')) ?>">
                        </div>
                        <div class="field">
                            <label>Année</label>
                            <input name="projects[<?= (int) $i ?>][year]" value="<?= e((string) ($project['year'] ?? '')) ?>">
                        </div>
                    </div>
                    <div class="field">
                        <label>Image (URL ou chemin assets)</label>
                        <input name="projects[<?= (int) $i ?>][image]" value="<?= e((string) ($project['image'] ?? '')) ?>" placeholder="https://… ou images/projet.jpg">
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" data-remove-row>Supprimer</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn nav-btn-ghost" data-add-row data-target="projects">+ Ajouter un projet</button>
        <template data-template="projects">
            <div class="repeat-item" data-repeat-item>
                <div class="field"><label>Titre</label><input name="projects[__INDEX__][title]"></div>
                <div class="field"><label>Description</label><textarea name="projects[__INDEX__][description]" rows="3"></textarea></div>
                <div class="field-row">
                    <div class="field"><label>Catégorie</label><input name="projects[__INDEX__][category]"></div>
                    <div class="field"><label>Année</label><input name="projects[__INDEX__][year]"></div>
                </div>
                <div class="field"><label>Image (URL ou chemin assets)</label><input name="projects[__INDEX__][image]" placeholder="https://… ou images/projet.jpg"></div>
                <button type="button" class="btn btn-danger btn-sm" data-remove-row>Supprimer</button>
            </div>
        </template>
        <div class="admin-actions">
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
<?php endif; ?>

<?php if ($adminTab === 'blog'): ?>
    <?php
    $blogPosts = [];
    foreach (array_values($content['blog'] ?? []) as $post) {
        $blogPosts[] = blog_normalize_row(is_array($post) ? $post : []);
    }
    ?>
    <form class="admin-form" method="post" action="<?= e($adminFormAction) ?>">
        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
        <input type="hidden" name="tab" value="blog">
        <p class="admin-hint" style="margin-top:0;">Ajoutez ou modifiez les articles. Le slug est généré automatiquement s’il est vide. Séparez les paragraphes du contenu par une ligne vide.</p>
        <div class="repeat-list" data-repeat="blog">
            <?php foreach ($blogPosts as $i => $post): ?>
                <div class="repeat-item" data-repeat-item>
                    <div class="field">
                        <label>Titre</label>
                        <input name="blog[<?= (int) $i ?>][title]" value="<?= e((string) ($post['title'] ?? '')) ?>" required>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label>Slug (URL)</label>
                            <input name="blog[<?= (int) $i ?>][slug]" value="<?= e((string) ($post['slug'] ?? '')) ?>" placeholder="mon-article">
                        </div>
                        <div class="field">
                            <label>Catégorie</label>
                            <input name="blog[<?= (int) $i ?>][category]" value="<?= e((string) ($post['category'] ?? '')) ?>">
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label>Date</label>
                            <input type="date" name="blog[<?= (int) $i ?>][date]" value="<?= e((string) ($post['date'] ?? date('Y-m-d'))) ?>">
                        </div>
                        <div class="field">
                            <label>Image (URL)</label>
                            <input name="blog[<?= (int) $i ?>][image]" value="<?= e((string) ($post['image'] ?? '')) ?>" placeholder="https://…">
                        </div>
                    </div>
                    <div class="field">
                        <label>Extrait</label>
                        <textarea name="blog[<?= (int) $i ?>][excerpt]" rows="2"><?= e((string) ($post['excerpt'] ?? '')) ?></textarea>
                    </div>
                    <div class="field">
                        <label>Contenu (paragraphes séparés par une ligne vide)</label>
                        <textarea name="blog[<?= (int) $i ?>][body]" rows="8"><?= e((string) ($post['body_text'] ?? '')) ?></textarea>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" data-remove-row>Supprimer</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn nav-btn-ghost" data-add-row data-target="blog">+ Ajouter un article</button>
        <template data-template="blog">
            <div class="repeat-item" data-repeat-item>
                <div class="field"><label>Titre</label><input name="blog[__INDEX__][title]"></div>
                <div class="field-row">
                    <div class="field"><label>Slug (URL)</label><input name="blog[__INDEX__][slug]" placeholder="mon-article"></div>
                    <div class="field"><label>Catégorie</label><input name="blog[__INDEX__][category]"></div>
                </div>
                <div class="field-row">
                    <div class="field"><label>Date</label><input type="date" name="blog[__INDEX__][date]" value="<?= e(date('Y-m-d')) ?>"></div>
                    <div class="field"><label>Image (URL)</label><input name="blog[__INDEX__][image]" placeholder="https://…"></div>
                </div>
                <div class="field"><label>Extrait</label><textarea name="blog[__INDEX__][excerpt]" rows="2"></textarea></div>
                <div class="field"><label>Contenu (paragraphes séparés par une ligne vide)</label><textarea name="blog[__INDEX__][body]" rows="8"></textarea></div>
                <button type="button" class="btn btn-danger btn-sm" data-remove-row>Supprimer</button>
            </div>
        </template>
        <div class="admin-actions">
            <button class="btn btn-primary" type="submit">Enregistrer</button>
            <a class="btn nav-btn-ghost" href="<?= e(page_url('blog')) ?>" target="_blank" rel="noopener">Voir le blog</a>
        </div>
    </form>
<?php endif; ?>

<?php if ($adminTab === 'about'): ?>
    <?php $about = $content['about'] ?? []; ?>
    <form class="admin-form" method="post" action="<?= e($adminFormAction) ?>">
        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
        <input type="hidden" name="tab" value="about">
        <div class="field">
            <label for="lead">Présentation</label>
            <textarea id="lead" name="lead" rows="4"><?= e((string) ($about['lead'] ?? '')) ?></textarea>
        </div>
        <div class="field">
            <label for="mission">Mission</label>
            <textarea id="mission" name="mission" rows="3"><?= e((string) ($about['mission'] ?? '')) ?></textarea>
        </div>
        <h2 class="admin-subtitle">Valeurs</h2>
        <div class="repeat-list" data-repeat="values">
            <?php foreach (array_values($about['values'] ?? []) as $i => $value): ?>
                <div class="repeat-item" data-repeat-item>
                    <div class="field">
                        <label>Titre</label>
                        <input name="values[<?= (int) $i ?>][title]" value="<?= e((string) ($value['title'] ?? '')) ?>">
                    </div>
                    <div class="field">
                        <label>Texte</label>
                        <textarea name="values[<?= (int) $i ?>][text]" rows="2"><?= e((string) ($value['text'] ?? '')) ?></textarea>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" data-remove-row>Supprimer</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn nav-btn-ghost" data-add-row data-target="values">+ Ajouter une valeur</button>
        <template data-template="values">
            <div class="repeat-item" data-repeat-item>
                <div class="field"><label>Titre</label><input name="values[__INDEX__][title]"></div>
                <div class="field"><label>Texte</label><textarea name="values[__INDEX__][text]" rows="2"></textarea></div>
                <button type="button" class="btn btn-danger btn-sm" data-remove-row>Supprimer</button>
            </div>
        </template>
        <div class="admin-actions">
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
<?php endif; ?>

<?php if ($adminTab === 'why'): ?>
    <?php $why = $content['why'] ?? []; ?>
    <form class="admin-form" method="post" action="<?= e($adminFormAction) ?>">
        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
        <input type="hidden" name="tab" value="why">
        <div class="field">
            <label for="title">Titre</label>
            <input id="title" name="title" value="<?= e((string) ($why['title'] ?? '')) ?>">
        </div>
        <div class="field">
            <label for="lead">Introduction</label>
            <textarea id="lead" name="lead" rows="3"><?= e((string) ($why['lead'] ?? '')) ?></textarea>
        </div>
        <div class="field">
            <label for="bullets">Points forts (un par ligne)</label>
            <textarea id="bullets" name="bullets" rows="5"><?= e(implode("\n", $why['bullets'] ?? [])) ?></textarea>
        </div>
        <h2 class="admin-subtitle">Atouts</h2>
        <div class="repeat-list" data-repeat="features">
            <?php foreach (array_values($why['features'] ?? []) as $i => $feature): ?>
                <div class="repeat-item" data-repeat-item>
                    <div class="field">
                        <label>Titre</label>
                        <input name="features[<?= (int) $i ?>][title]" value="<?= e((string) ($feature['title'] ?? '')) ?>">
                    </div>
                    <div class="field">
                        <label>Description</label>
                        <textarea name="features[<?= (int) $i ?>][description]" rows="2"><?= e((string) ($feature['description'] ?? '')) ?></textarea>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" data-remove-row>Supprimer</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn nav-btn-ghost" data-add-row data-target="features">+ Ajouter un atout</button>
        <template data-template="features">
            <div class="repeat-item" data-repeat-item>
                <div class="field"><label>Titre</label><input name="features[__INDEX__][title]"></div>
                <div class="field"><label>Description</label><textarea name="features[__INDEX__][description]" rows="2"></textarea></div>
                <button type="button" class="btn btn-danger btn-sm" data-remove-row>Supprimer</button>
            </div>
        </template>
        <div class="admin-actions">
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
<?php endif; ?>

<?php if ($adminTab === 'testimonials'): ?>
    <form class="admin-form" method="post" action="<?= e($adminFormAction) ?>">
        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
        <input type="hidden" name="tab" value="testimonials">
        <div class="repeat-list" data-repeat="testimonials">
            <?php foreach (array_values($content['testimonials'] ?? []) as $i => $item): ?>
                <div class="repeat-item" data-repeat-item>
                    <div class="field">
                        <label>Témoignage</label>
                        <textarea name="testimonials[<?= (int) $i ?>][text]" rows="3"><?= e((string) ($item['text'] ?? '')) ?></textarea>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label>Nom</label>
                            <input name="testimonials[<?= (int) $i ?>][name]" value="<?= e((string) ($item['name'] ?? '')) ?>">
                        </div>
                        <div class="field">
                            <label>Rôle / entreprise</label>
                            <input name="testimonials[<?= (int) $i ?>][role]" value="<?= e((string) ($item['role'] ?? '')) ?>">
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" data-remove-row>Supprimer</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn nav-btn-ghost" data-add-row data-target="testimonials">+ Ajouter un témoignage</button>
        <template data-template="testimonials">
            <div class="repeat-item" data-repeat-item>
                <div class="field"><label>Témoignage</label><textarea name="testimonials[__INDEX__][text]" rows="3"></textarea></div>
                <div class="field-row">
                    <div class="field"><label>Nom</label><input name="testimonials[__INDEX__][name]"></div>
                    <div class="field"><label>Rôle / entreprise</label><input name="testimonials[__INDEX__][role]"></div>
                </div>
                <button type="button" class="btn btn-danger btn-sm" data-remove-row>Supprimer</button>
            </div>
        </template>
        <div class="admin-actions">
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
<?php endif; ?>

<?php if ($adminTab === 'messages'): ?>
    <?php if ($messages === []): ?>
        <div class="admin-hint"><p>Aucun message pour le moment.</p></div>
    <?php else: ?>
        <div class="message-list">
            <?php foreach ($messages as $msg): ?>
                <article class="message-item">
                    <header>
                        <strong><?= e((string) $msg['name']) ?></strong>
                        <span><?= e((string) $msg['created_at']) ?></span>
                    </header>
                    <p class="message-meta">
                        <a href="mailto:<?= e((string) $msg['email']) ?>"><?= e((string) $msg['email']) ?></a>
                        <?php if (!empty($msg['phone'])): ?> · <?= e((string) $msg['phone']) ?><?php endif; ?>
                    </p>
                    <p><strong><?= e((string) ($msg['subject'] ?: 'Sans sujet')) ?></strong></p>
                    <p><?= nl2br(e((string) $msg['message'])) ?></p>
                    <form method="post" action="<?= e($adminFormAction) ?>" onsubmit="return confirm('Supprimer ce message ?');">
                        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                        <input type="hidden" name="tab" value="messages">
                        <input type="hidden" name="action" value="delete_message">
                        <input type="hidden" name="message_id" value="<?= (int) $msg['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
