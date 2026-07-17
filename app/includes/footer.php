<?php
declare(strict_types=1);
/** @var array $config */
?>
    </main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div class="footer-brand">
                <a class="brand" href="<?= e(page_url('home')) ?>">
                    <span class="brand-mark" aria-hidden="true">SC</span>
                    <span class="brand-name">Sow<span>Coder</span></span>
                </a>
                <p>Votre partenaire digital pour concevoir, lancer et faire grandir vos projets numériques.</p>
            </div>

            <div>
                <h2 class="footer-title">Navigation</h2>
                <ul class="footer-list">
                    <li><a href="<?= e(page_url('home')) ?>">Accueil</a></li>
                    <li><a href="<?= e(page_url('services')) ?>">Services</a></li>
                    <li><a href="<?= e(page_url('about')) ?>">À propos</a></li>
                    <li><a href="<?= e(page_url('contact')) ?>">Contact</a></li>
                    <li><a href="<?= e(page_url('newsletter')) ?>">Newsletter</a></li>
                    <li><a href="<?= e(page_url('login')) ?>">Connexion</a></li>
                    <li><a href="<?= e(page_url('register')) ?>">Inscription</a></li>
                </ul>
            </div>

            <div>
                <h2 class="footer-title">Contact</h2>
                <ul class="footer-list">
                    <li><?= e($config['address']) ?></li>
                    <li><a href="mailto:<?= e($config['email']) ?>"><?= e($config['email']) ?></a></li>
                    <?php foreach ($config['phones'] as $phone): ?>
                        <li>
                            <a href="tel:<?= e($phone['tel']) ?>">
                                <?= e($phone['label']) ?> · <?= e($phone['number']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="container footer-bottom">
            <p>&copy; <?= date('Y') ?> SowCoder. Tous droits réservés.</p>
            <div class="footer-social">
                <?php foreach ($config['whatsapp'] as $wa): ?>
                    <a href="<?= e($wa['url']) ?>" target="_blank" rel="noopener noreferrer"><?= e($wa['label']) ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </footer>

    <script src="<?= e(asset('js/main.js')) ?>" defer></script>
</body>
</html>
