<?php
declare(strict_types=1);
/** @var array $content */
$services = $content['services'];
?>

<section class="page-hero">
    <div class="container">
        <p class="eyebrow">Services</p>
        <h1>Une offre digitale complète</h1>
        <p class="page-lead">De l’audit initial à la maintenance évolutive, nous pilotons chaque aspect de votre présence numérique.</p>
    </div>
</section>

<section class="section">
    <div class="container service-grid">
        <?php foreach ($services as $index => $service): ?>
            <article class="service-panel reveal" data-reveal>
                <div class="service-meta">
                    <span class="service-icon" aria-hidden="true"><?= service_icon_svg((string) ($service['icon'] ?? 'code')) ?></span>
                    <span class="service-index">0<?= $index + 1 ?></span>
                </div>
                <h2><?= e($service['title']) ?></h2>
                <p><?= e($service['description']) ?></p>
                <a class="text-link" href="<?= e(page_url('contact')) ?>&sujet=<?= e(rawurlencode($service['title'])) ?>">Demander un devis</a>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="cta-band">
    <div class="container cta-band-inner">
        <div>
            <h2>Un besoin précis ?</h2>
            <p>Décrivez votre projet : nous vous orientons vers la bonne solution.</p>
        </div>
        <a class="btn btn-primary" href="<?= e(page_url('contact')) ?>">Obtenir un devis</a>
    </div>
</section>
