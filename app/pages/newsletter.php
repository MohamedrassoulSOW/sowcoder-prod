<?php

declare(strict_types=1);

$errors = [];
$info = '';
$email = '';
$action = 'addforce';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!csrf_verify($_POST['_csrf'] ?? null)) {
        $errors[] = 'Session expirée. Rechargez la page.';
    } else {
        $email = mb_strtolower(trim((string) ($_POST['email'] ?? '')));
        $action = (string) ($_POST['action'] ?? 'addforce');

        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'Indiquez une adresse e-mail valide.';
        }

        if (!in_array($action, ['addforce', 'unsub'], true)) {
            $errors[] = 'Action invalide.';
        }

        if ($errors === []) {
            try {
                $listId = (int) ($_ENV['MAILJET_NEWSLETTER_LIST_ID'] ?? getenv('MAILJET_NEWSLETTER_LIST_ID') ?: 0);
                $ok = mailjet()->manageNewsletterSubscription($email, $listId, $action);
                if ($ok) {
                    $info = $action === 'unsub'
                        ? 'Vous êtes désinscrit(e) de la newsletter.'
                        : 'Merci ! Votre inscription à la newsletter est confirmée.';
                } else {
                    $errors[] = 'Impossible de mettre à jour votre abonnement. Réessayez plus tard.';
                }
            } catch (Throwable $e) {
                error_log('Newsletter Mailjet : ' . $e->getMessage());
                $errors[] = 'Service newsletter indisponible pour le moment.';
            }
        }
    }
}

$csrf = csrf_token();
?>

<section class="page-hero">
    <div class="container">
        <p class="eyebrow">Newsletter</p>
        <h1>Restez informé</h1>
        <p class="page-lead">Recevez nos actualités, conseils et offres — ou désinscrivez-vous en un clic.</p>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width: 32rem;">
        <?php if ($info !== ''): ?>
            <div class="alert alert-ok" role="status">
                <p><?= e($info) ?></p>
            </div>
        <?php endif; ?>

        <?php if ($errors !== []): ?>
            <div class="alert alert-error" role="alert">
                <?php foreach ($errors as $error): ?>
                    <p><?= e($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form class="contact-form" method="post" action="<?= e(page_url('newsletter')) ?>">
            <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
            <div class="field">
                <label for="email">Adresse e-mail</label>
                <input type="email" id="email" name="email" required autocomplete="email" value="<?= e($email) ?>">
            </div>
            <div class="field">
                <label for="action">Action</label>
                <select id="action" name="action" required>
                    <option value="addforce" <?= $action === 'addforce' ? 'selected' : '' ?>>S’inscrire</option>
                    <option value="unsub" <?= $action === 'unsub' ? 'selected' : '' ?>>Se désinscrire</option>
                </select>
            </div>
            <button class="btn btn-primary" type="submit">Confirmer</button>
        </form>
    </div>
</section>
