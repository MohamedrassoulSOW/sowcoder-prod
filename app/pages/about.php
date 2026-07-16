<?php
declare(strict_types=1);
/** @var array $content */
/** @var array $config */
$about = $content['about'];
$why = $content['why'];
?>

<section class="page-hero">
    <div class="container">
        <p class="eyebrow">À propos</p>
        <h1>SowCoder, partenaire de votre transformation digitale</h1>
        <p class="page-lead"><?= e($about['lead']) ?></p>
    </div>
</section>

<section class="section">
    <div class="container split">
        <div class="reveal" data-reveal>
            <p class="eyebrow">Mission</p>
            <h2>Des produits utiles, esthétiques et rentables</h2>
            <p><?= e($about['mission']) ?></p>
            <p class="muted"><?= e($config['address']) ?> · <?= e($config['email']) ?></p>
        </div>
        <div class="value-stack">
            <?php foreach ($about['values'] as $value): ?>
                <article class="value-item reveal" data-reveal>
                    <h3><?= e($value['title']) ?></h3>
                    <p><?= e($value['text']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-tint">
    <div class="container">
        <header class="section-head">
            <p class="eyebrow">Notre approche</p>
            <h2><?= e($why['title']) ?></h2>
            <p><?= e($why['lead']) ?></p>
        </header>
        <ul class="check-list check-list-wide reveal" data-reveal>
            <?php foreach ($why['bullets'] as $bullet): ?>
                <li><?= e($bullet) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
