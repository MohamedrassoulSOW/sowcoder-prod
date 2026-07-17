<?php

declare(strict_types=1);

$articles = blog_load_articles();
$slug = trim((string) ($_GET['slug'] ?? ''));
$article = null;

if ($slug !== '') {
    foreach ($articles as $row) {
        if (($row['slug'] ?? '') === $slug) {
            $article = $row;
            break;
        }
    }
    if ($article === null) {
        http_response_code(404);
    }
}

$formatDate = static function (string $date): string {
    $ts = strtotime($date);
    if ($ts === false) {
        return $date;
    }
    $months = [
        1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril',
        5 => 'mai', 6 => 'juin', 7 => 'juillet', 8 => 'août',
        9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre',
    ];
    $m = (int) date('n', $ts);

    return (int) date('j', $ts) . ' ' . ($months[$m] ?? '') . ' ' . date('Y', $ts);
};

if ($article !== null):
    $pageTitle = ((string) $article['title']) . ' — Blog — ' . (string) ($config['site_name'] ?? 'SowCoder');
    $image = trim((string) ($article['image'] ?? ''));
    $imageUrl = $image !== '' ? media_url($image) : '';
    ?>
<section class="page-hero blog-hero">
    <div class="container">
        <p class="eyebrow">Blog · <?= e((string) $article['category']) ?></p>
        <h1><?= e((string) $article['title']) ?></h1>
        <p class="page-lead"><?= e((string) $article['excerpt']) ?></p>
        <p class="blog-meta"><?= e($formatDate((string) $article['date'])) ?></p>
    </div>
</section>

<article class="section">
    <div class="container blog-article">
        <?php if ($imageUrl !== ''): ?>
            <div class="blog-article-media reveal" data-reveal>
                <img src="<?= e($imageUrl) ?>" alt="<?= e((string) $article['title']) ?>" width="1200" height="700" loading="eager">
            </div>
        <?php endif; ?>

        <div class="blog-article-body reveal" data-reveal>
            <?php foreach (($article['body'] ?? []) as $paragraph): ?>
                <p><?= e((string) $paragraph) ?></p>
            <?php endforeach; ?>
        </div>

        <p class="blog-back">
            <a class="text-link" href="<?= e(page_url('blog')) ?>">← Retour au blog</a>
        </p>
    </div>
</article>
<?php else: ?>
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">Blog</p>
        <h1>Conseils & actualités digitales</h1>
        <p class="page-lead">Web, marketing, sécurité et formation — des articles concrets pour faire avancer vos projets.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if ($slug !== '' && $article === null): ?>
            <div class="alert alert-error" role="alert">
                <p>Article introuvable. Voici les dernières publications.</p>
            </div>
        <?php endif; ?>

        <div class="blog-grid">
            <?php foreach ($articles as $i => $post): ?>
                <?php
                $postImage = trim((string) ($post['image'] ?? ''));
                $postImageUrl = $postImage !== '' ? media_url($postImage) : '';
                $href = page_url('blog') . '&slug=' . rawurlencode((string) $post['slug']);
                ?>
                <article class="blog-card reveal" data-reveal style="--i: <?= (int) $i ?>">
                    <?php if ($postImageUrl !== ''): ?>
                        <a class="blog-card-media" href="<?= e($href) ?>">
                            <img src="<?= e($postImageUrl) ?>" alt="<?= e((string) $post['title']) ?>" loading="lazy" width="640" height="400">
                        </a>
                    <?php endif; ?>
                    <div class="blog-card-body">
                        <div class="blog-card-meta">
                            <span class="chip"><?= e((string) $post['category']) ?></span>
                            <time datetime="<?= e((string) $post['date']) ?>"><?= e($formatDate((string) $post['date'])) ?></time>
                        </div>
                        <h2><a href="<?= e($href) ?>"><?= e((string) $post['title']) ?></a></h2>
                        <p><?= e((string) $post['excerpt']) ?></p>
                        <a class="text-link" href="<?= e($href) ?>">Lire l’article</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
