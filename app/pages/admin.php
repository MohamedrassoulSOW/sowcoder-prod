<?php

declare(strict_types=1);

auth_require_admin();

$adminTab = (string) ($_GET['tab'] ?? 'overview');
$allowedTabs = ['overview', 'settings', 'hero', 'services', 'projects', 'about', 'why', 'testimonials', 'messages'];
if (!in_array($adminTab, $allowedTabs, true)) {
    $adminTab = 'overview';
}

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
    <div class="admin-cards">
        <article class="admin-card">
            <h2>Contenu du site</h2>
            <p>Modifiez textes, services, projets et témoignages sans toucher au code.</p>
            <a class="btn btn-primary" href="<?= e(page_url('admin') . '&tab=hero') ?>">Éditer le contenu</a>
        </article>
        <article class="admin-card">
            <h2>Messages reçus</h2>
            <p><strong><?= (int) $messageCount ?></strong> message(s) dans la boîte de contact.</p>
            <a class="btn btn-primary" href="<?= e(page_url('admin') . '&tab=messages') ?>">Voir les messages</a>
        </article>
        <article class="admin-card">
            <h2>Site public</h2>
            <p>Prévisualisez le site tel que vos visiteurs le voient.</p>
            <a class="btn btn-primary" href="<?= e(page_url('home')) ?>" target="_blank" rel="noopener">Ouvrir le site</a>
        </article>
    </div>
    <div class="admin-hint">
        <p>Astuce : choisissez une section dans le menu, puis cliquez sur <strong>Enregistrer</strong> après vos modifications.</p>
    </div>
<?php endif; ?>

<?php if ($adminTab === 'settings'): ?>
    <form class="admin-form" method="post" action="<?= e(page_url('admin-save')) ?>">
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
                    <button type="button" class="btn-text" data-remove-row>Supprimer</button>
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
                <button type="button" class="btn-text" data-remove-row>Supprimer</button>
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
                    <button type="button" class="btn-text" data-remove-row>Supprimer</button>
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
                <button type="button" class="btn-text" data-remove-row>Supprimer</button>
            </div>
        </template>

        <div class="admin-actions">
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
<?php endif; ?>

<?php if ($adminTab === 'hero'): ?>
    <?php $hero = $content['hero'] ?? []; ?>
    <form class="admin-form" method="post" action="<?= e(page_url('admin-save')) ?>">
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
    <form class="admin-form" method="post" action="<?= e(page_url('admin-save')) ?>">
        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
        <input type="hidden" name="tab" value="services">
        <div class="repeat-list" data-repeat="services">
            <?php foreach (array_values($content['services'] ?? []) as $i => $service): ?>
                <div class="repeat-item" data-repeat-item>
                    <div class="field">
                        <label>Titre</label>
                        <input name="services[<?= (int) $i ?>][title]" value="<?= e((string) ($service['title'] ?? '')) ?>">
                    </div>
                    <div class="field">
                        <label>Description</label>
                        <textarea name="services[<?= (int) $i ?>][description]" rows="3"><?= e((string) ($service['description'] ?? '')) ?></textarea>
                    </div>
                    <input type="hidden" name="services[<?= (int) $i ?>][icon]" value="<?= e((string) ($service['icon'] ?? 'code')) ?>">
                    <button type="button" class="btn-text" data-remove-row>Supprimer</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn nav-btn-ghost" data-add-row data-target="services">+ Ajouter un service</button>
        <template data-template="services">
            <div class="repeat-item" data-repeat-item>
                <div class="field">
                    <label>Titre</label>
                    <input name="services[__INDEX__][title]">
                </div>
                <div class="field">
                    <label>Description</label>
                    <textarea name="services[__INDEX__][description]" rows="3"></textarea>
                </div>
                <input type="hidden" name="services[__INDEX__][icon]" value="code">
                <button type="button" class="btn-text" data-remove-row>Supprimer</button>
            </div>
        </template>
        <div class="admin-actions">
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
<?php endif; ?>

<?php if ($adminTab === 'projects'): ?>
    <form class="admin-form" method="post" action="<?= e(page_url('admin-save')) ?>">
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
                    <button type="button" class="btn-text" data-remove-row>Supprimer</button>
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
                <button type="button" class="btn-text" data-remove-row>Supprimer</button>
            </div>
        </template>
        <div class="admin-actions">
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
<?php endif; ?>

<?php if ($adminTab === 'about'): ?>
    <?php $about = $content['about'] ?? []; ?>
    <form class="admin-form" method="post" action="<?= e(page_url('admin-save')) ?>">
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
                    <button type="button" class="btn-text" data-remove-row>Supprimer</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn nav-btn-ghost" data-add-row data-target="values">+ Ajouter une valeur</button>
        <template data-template="values">
            <div class="repeat-item" data-repeat-item>
                <div class="field"><label>Titre</label><input name="values[__INDEX__][title]"></div>
                <div class="field"><label>Texte</label><textarea name="values[__INDEX__][text]" rows="2"></textarea></div>
                <button type="button" class="btn-text" data-remove-row>Supprimer</button>
            </div>
        </template>
        <div class="admin-actions">
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
<?php endif; ?>

<?php if ($adminTab === 'why'): ?>
    <?php $why = $content['why'] ?? []; ?>
    <form class="admin-form" method="post" action="<?= e(page_url('admin-save')) ?>">
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
                    <button type="button" class="btn-text" data-remove-row>Supprimer</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn nav-btn-ghost" data-add-row data-target="features">+ Ajouter un atout</button>
        <template data-template="features">
            <div class="repeat-item" data-repeat-item>
                <div class="field"><label>Titre</label><input name="features[__INDEX__][title]"></div>
                <div class="field"><label>Description</label><textarea name="features[__INDEX__][description]" rows="2"></textarea></div>
                <button type="button" class="btn-text" data-remove-row>Supprimer</button>
            </div>
        </template>
        <div class="admin-actions">
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
<?php endif; ?>

<?php if ($adminTab === 'testimonials'): ?>
    <form class="admin-form" method="post" action="<?= e(page_url('admin-save')) ?>">
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
                    <button type="button" class="btn-text" data-remove-row>Supprimer</button>
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
                <button type="button" class="btn-text" data-remove-row>Supprimer</button>
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
                    <form method="post" action="<?= e(page_url('admin-save')) ?>" onsubmit="return confirm('Supprimer ce message ?');">
                        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                        <input type="hidden" name="tab" value="messages">
                        <input type="hidden" name="action" value="delete_message">
                        <input type="hidden" name="message_id" value="<?= (int) $msg['id'] ?>">
                        <button type="submit" class="btn-text">Supprimer</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
