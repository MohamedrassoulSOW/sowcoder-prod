<?php
declare(strict_types=1);
/** @var array $config */
/** @var array|null $currentUser */
?>
    </main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div class="footer-brand">
                <a class="brand" href="<?= e(page_url('home')) ?>">
                    <img class="brand-logo" src="<?= e(asset('images/logo-sc.png')) ?>" alt="" width="40" height="40">
                    <span class="brand-name">Sow<span>Coder</span></span>
                </a>
                <p class="footer-brand-lead">
                    <?= e((string) ($config['tagline'] ?? 'L’innovation digitale au service de votre croissance')) ?>
                </p>
                <p class="footer-brand-text">
                    Basés à Sangalkam (Dakar), nous concevons des sites, applications et stratégies digitales qui aident les PME, institutions et entrepreneurs à se démarquer, attirer plus de clients et accélérer leur transformation numérique.
                </p>
            </div>

            <div>
                <h2 class="footer-title">Explorer</h2>
                <ul class="footer-list">
                    <li><a href="<?= e(page_url('home')) ?>">Accueil</a></li>
                    <li><a href="<?= e(page_url('services')) ?>">Services</a></li>
                    <li><a href="<?= e(page_url('blog')) ?>">Blog</a></li>
                    <li><a href="<?= e(page_url('about')) ?>">À propos</a></li>
                    <li><a href="<?= e(page_url('contact')) ?>">Contact</a></li>
                </ul>
            </div>

            <div>
                <h2 class="footer-title">Compte</h2>
                <ul class="footer-list">
                    <?php if ($currentUser): ?>
                        <li><a href="<?= e(page_url('profile')) ?>">Mon profil</a></li>
                        <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                            <li><a href="<?= e(page_url('admin')) ?>">Dashboard</a></li>
                        <?php endif; ?>
                        <li><a href="<?= e(page_url('newsletter')) ?>">Newsletter</a></li>
                        <li><a href="<?= e(page_url('logout')) ?>">Déconnexion</a></li>
                    <?php else: ?>
                        <li><a href="<?= e(page_url('login')) ?>">Connexion</a></li>
                        <li><a href="<?= e(page_url('register')) ?>">Inscription</a></li>
                        <li><a href="<?= e(page_url('newsletter')) ?>">Newsletter</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div>
                <h2 class="footer-title">Contact</h2>
                <ul class="footer-list">
                    <li><?= e((string) ($config['address'] ?? '')) ?></li>
                    <li><a href="mailto:<?= e((string) ($config['email'] ?? '')) ?>"><?= e((string) ($config['email'] ?? '')) ?></a></li>
                    <?php foreach (($config['phones'] ?? []) as $phone): ?>
                        <?php if (!is_array($phone)) {
                            continue;
                        } ?>
                        <li>
                            <a href="tel:<?= e((string) ($phone['tel'] ?? '')) ?>">
                                <?= e((string) ($phone['label'] ?? '')) ?> · <?= e((string) ($phone['number'] ?? '')) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php if (!empty($config['whatsapp']) && is_array($config['whatsapp'])): ?>
                    <div class="footer-whatsapp">
                        <?php foreach ($config['whatsapp'] as $wa): ?>
                            <?php if (!is_array($wa) || trim((string) ($wa['url'] ?? '')) === '') {
                                continue;
                            } ?>
                            <a class="footer-wa-btn" href="<?= e((string) $wa['url']) ?>" target="_blank" rel="noopener noreferrer">
                                <?= e((string) ($wa['label'] ?? 'WhatsApp')) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="container footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= e((string) ($config['site_name'] ?? 'SowCoder')) ?>. Tous droits réservés.</p>
            <p class="footer-bottom-note">Créateurs d’expériences digitales · Dakar &amp; au-delà</p>
        </div>
    </footer>

    <script src="<?= e(asset('js/main.js')) ?>?v=<?= e((string) @filemtime(__DIR__ . '/../../assets/js/main.js')) ?>" defer></script>
</body>
</html>
