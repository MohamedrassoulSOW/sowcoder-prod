<?php
declare(strict_types=1);
/** @var array $content */
/** @var array|null $currentUser */
$hero = $content['hero'];
$services = array_slice($content['services'], 0, 3);
$projects = $content['projects'];
$why = $content['why'];
$testimonials = $content['testimonials'];
?>

<?php if (($_GET['welcome'] ?? '') === '1' && $currentUser): ?>
    <div class="flash-banner">
        <div class="container">
            Bienvenue, <?= e($currentUser['name']) ?> — votre compte SowCoder est prêt.
        </div>
    </div>
<?php endif; ?>

<section class="hero" aria-label="Présentation SowCoder">
    <div class="hero-media" aria-hidden="true">
        <img
            src="<?= e($hero['image']) ?>"
            alt=""
            width="1920"
            height="1080"
            fetchpriority="high"
        >
        <div class="hero-scrim"></div>
        <div class="hero-grid"></div>
    </div>

    <div class="hero-content">
        <p class="hero-brand reveal" data-reveal>SowCoder</p>
        <p class="hero-eyebrow reveal" data-reveal><?= e($hero['eyebrow']) ?></p>
        <h1 class="hero-title reveal" data-reveal><?= e($hero['title']) ?></h1>
        <p class="hero-lead reveal" data-reveal><?= e($hero['lead']) ?></p>
        <div class="hero-actions reveal" data-reveal>
            <a class="btn btn-primary" href="<?= e(page_url('contact')) ?>"><?= e($hero['cta_primary']) ?></a>
            <a class="btn btn-ghost" href="<?= e(page_url('services')) ?>"><?= e($hero['cta_secondary']) ?></a>
            <?php if (!$currentUser): ?>
                <a class="btn btn-ghost" href="<?= e(page_url('register')) ?>">Créer un compte</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <header class="section-head">
            <p class="eyebrow">Expertises</p>
            <h2>Ce que nous faisons le mieux</h2>
            <p>Du web au design, en passant par le marketing et la formation — une offre claire pour accélérer votre présence digitale.</p>
        </header>

        <div class="bento-services">
            <?php foreach ($services as $index => $service): ?>
                <article class="bento-item reveal" data-reveal style="--i: <?= (int) $index ?>">
                    <div class="service-meta">
                        <span class="service-icon" aria-hidden="true"><?= service_icon_svg((string) ($service['icon'] ?? 'code')) ?></span>
                        <span class="service-index">0<?= $index + 1 ?></span>
                    </div>
                    <h3><?= e($service['title']) ?></h3>
                    <p><?= e($service['description']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>

        <p class="section-cta">
            <a class="text-link" href="<?= e(page_url('services')) ?>">Voir tous les services</a>
        </p>
    </div>
</section>

<section class="section section-tint">
    <div class="container">
        <header class="section-head">
            <p class="eyebrow">Réalisations</p>
            <h2>Des projets concrets, livrés</h2>
            <p>Quelques exemples de missions menées pour des cabinets, PME et centres de formation.</p>
        </header>

        <div class="project-list">
            <?php foreach ($projects as $project): ?>
                <?php
                $projectImage = trim((string) ($project['image'] ?? ''));
                $projectImageUrl = $projectImage !== '' ? media_url($projectImage) : '';
                ?>
                <article class="project-card reveal" data-reveal>
                    <?php if ($projectImageUrl !== ''): ?>
                        <div class="project-media">
                            <img src="<?= e($projectImageUrl) ?>" alt="<?= e((string) ($project['title'] ?? '')) ?>" loading="lazy" width="640" height="400">
                        </div>
                    <?php endif; ?>
                    <div class="project-body">
                        <div class="project-meta">
                            <span class="chip"><?= e($project['category']) ?></span>
                            <span><?= e($project['year']) ?></span>
                        </div>
                        <h3><?= e($project['title']) ?></h3>
                        <p><?= e($project['description']) ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container why-grid">
        <div class="why-copy reveal" data-reveal>
            <p class="eyebrow">Pourquoi SowCoder</p>
            <h2><?= e($why['title']) ?></h2>
            <p><?= e($why['lead']) ?></p>
            <ul class="check-list">
                <?php foreach ($why['bullets'] as $bullet): ?>
                    <li><?= e($bullet) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="why-features">
            <?php foreach ($why['features'] as $feature): ?>
                <article class="feature-tile reveal" data-reveal>
                    <h3><?= e($feature['title']) ?></h3>
                    <p><?= e($feature['description']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-tint">
    <div class="container">
        <header class="section-head">
            <p class="eyebrow">Témoignages</p>
            <h2>Ils nous font confiance</h2>
        </header>
        <div class="quote-list">
            <?php foreach ($testimonials as $item): ?>
                <blockquote class="quote-card reveal" data-reveal>
                    <p>« <?= e($item['text']) ?> »</p>
                    <footer>
                        <strong><?= e($item['name']) ?></strong>
                        <span><?= e($item['role']) ?></span>
                    </footer>
                </blockquote>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="cta-band">
    <div class="container cta-band-inner reveal" data-reveal>
        <div>
            <h2>Prêt à lancer votre prochain projet ?</h2>
            <p>Créez un compte ou contactez-nous — nous vous répondons rapidement.</p>
        </div>
        <div class="cta-actions">
            <?php if (!$currentUser): ?>
                <a class="btn btn-primary" href="<?= e(page_url('register')) ?>">S’inscrire</a>
            <?php endif; ?>
            <a class="btn btn-ghost" href="<?= e(page_url('contact')) ?>">Nous contacter</a>
        </div>
    </div>
</section>
