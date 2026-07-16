<?php
declare(strict_types=1);
/** @var array $config */

$sujet = trim((string) ($_GET['sujet'] ?? ''));
$status = $_GET['status'] ?? '';
$address = (string) ($config['address'] ?? 'Sangalkam, Dakar, Sénégal');
$mapQuery = rawurlencode($address);
$mapEmbed = 'https://www.google.com/maps?q=' . $mapQuery . '&z=15&hl=fr&output=embed';
$mapLink = 'https://www.google.com/maps/search/?api=1&query=' . $mapQuery;
?>

<section class="page-hero">
    <div class="container">
        <p class="eyebrow">Contact</p>
        <h1>Parlons de votre projet</h1>
        <p class="page-lead">Une question, un devis, une formation ? Écrivez-nous — nous vous répondons rapidement.</p>
    </div>
</section>

<section class="section">
    <div class="container contact-layout">
        <aside class="contact-aside reveal" data-reveal>
            <h2>Coordonnées</h2>
            <p><?= e($address) ?></p>
            <p><a href="mailto:<?= e($config['email']) ?>"><?= e($config['email']) ?></a></p>
            <ul class="contact-phones">
                <?php foreach ($config['phones'] as $phone): ?>
                    <li>
                        <a href="tel:<?= e($phone['tel']) ?>">
                            <?= e($phone['label']) ?> · <?= e($phone['number']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="contact-wa">
                <?php foreach ($config['whatsapp'] as $wa): ?>
                    <a class="btn btn-ghost" href="<?= e($wa['url']) ?>" target="_blank" rel="noopener noreferrer">
                        <?= e($wa['label']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <a class="text-link contact-map-link" href="<?= e($mapLink) ?>" target="_blank" rel="noopener noreferrer">
                Voir l’itinéraire
            </a>
        </aside>

        <div class="contact-form-wrap reveal" data-reveal>
            <?php if ($status === 'ok'): ?>
                <div class="alert alert-ok" role="status">
                    Merci ! Votre message a bien été enregistré. Nous vous recontactons bientôt.
                </div>
            <?php elseif ($status === 'error'): ?>
                <div class="alert alert-error" role="alert">
                    Impossible d’envoyer le message. Vérifiez les champs et réessayez.
                </div>
            <?php endif; ?>

            <form class="contact-form" action="<?= e(page_url('contact-submit')) ?>" method="post" novalidate>
                <div class="field">
                    <label for="name">Nom complet</label>
                    <input type="text" id="name" name="name" required autocomplete="name" maxlength="120">
                </div>
                <div class="field-row">
                    <div class="field">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required autocomplete="email" maxlength="180">
                    </div>
                    <div class="field">
                        <label for="phone">Téléphone</label>
                        <input type="tel" id="phone" name="phone" autocomplete="tel" maxlength="40">
                    </div>
                </div>
                <div class="field">
                    <label for="subject">Sujet</label>
                    <input type="text" id="subject" name="subject" value="<?= e($sujet) ?>" maxlength="160">
                </div>
                <div class="field">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="6" required maxlength="4000"></textarea>
                </div>
                <button class="btn btn-primary" type="submit">Envoyer le message</button>
            </form>
        </div>
    </div>
</section>

<section class="section section-tint contact-map-section">
    <div class="container">
        <header class="section-head">
            <p class="eyebrow">Localisation</p>
            <h2>Où nous trouver</h2>
            <p>SowCoder — <?= e($address) ?></p>
        </header>

        <div class="map-card reveal" data-reveal>
            <iframe
                class="map-frame"
                title="Carte — SowCoder à <?= e($address) ?>"
                src="<?= e($mapEmbed) ?>"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                allowfullscreen
            ></iframe>
            <div class="map-card-footer">
                <p><?= e($address) ?></p>
                <a class="btn btn-primary" href="<?= e($mapLink) ?>" target="_blank" rel="noopener noreferrer">
                    Ouvrir dans Google Maps
                </a>
            </div>
        </div>
    </div>
</section>
