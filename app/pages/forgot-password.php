<?php

declare(strict_types=1);

if (auth_check()) {
    redirect_to('profile');
}

$errors = [];
$info = '';
$debugLink = '';
$email = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!csrf_verify($_POST['_csrf'] ?? null)) {
        $errors[] = 'Session expirée. Rechargez la page.';
    } else {
        $email = mb_strtolower(trim((string) ($_POST['email'] ?? '')));

        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'Indiquez une adresse e-mail valide.';
        }

        if ($errors === []) {
            try {
                $user = auth_find_by_email($email);
                // Message générique (ne pas révéler si le compte existe)
                $info = 'Si un compte existe pour cet e-mail, un lien de réinitialisation a été préparé.';

                if ($user !== null) {
                    $token = auth_create_password_reset((int) $user['id']);
                    $mailResult = auth_send_password_reset_email(
                        (string) $user['email'],
                        (string) $user['name'],
                        $token
                    );

                    global $config;
                    $debug = (bool) ($config['mail']['debug'] ?? false);
                    if (!$mailResult['sent'] && $debug) {
                        $debugLink = (string) $mailResult['reset_url'];
                        $info = 'L’envoi e-mail n’a pas fonctionné en local. Utilisez le lien ci-dessous pour réinitialiser votre mot de passe.';
                    } elseif ($mailResult['sent']) {
                        $info = 'Un e-mail de réinitialisation vous a été envoyé. Vérifiez votre boîte de réception.';
                    }
                }
            } catch (Throwable $e) {
                $errors[] = 'Impossible de traiter la demande pour le moment.';
            }
        }
    }
}

$bodyClass = 'page-auth';
$csrf = csrf_token();
?>

<section class="auth-shell">
    <div class="auth-visual" aria-hidden="true">
        <div class="auth-visual-inner">
            <p class="auth-brand">SowCoder</p>
            <h2>Mot de passe oublié</h2>
            <p>Recevez un lien pour réinitialiser ou initialiser votre mot de passe.</p>
        </div>
    </div>

    <div class="auth-panel">
        <div class="auth-card">
            <p class="eyebrow">Sécurité</p>
            <h1>Réinitialiser le mot de passe</h1>
            <p class="auth-lead">Indiquez l’e-mail de votre compte.</p>

            <?php if ($info !== ''): ?>
                <div class="alert alert-ok" role="status">
                    <p><?= e($info) ?></p>
                    <?php if ($debugLink !== ''): ?>
                        <p><a href="<?= e($debugLink) ?>">Ouvrir le lien de réinitialisation</a></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($errors !== []): ?>
                <div class="alert alert-error" role="alert">
                    <?php foreach ($errors as $error): ?>
                        <p><?= e($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="post" action="<?= e(page_url('forgot-password')) ?>">
                <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                <div class="field">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required autocomplete="email" value="<?= e($email) ?>">
                </div>
                <button class="btn btn-primary btn-block" type="submit">Envoyer le lien</button>
            </form>

            <p class="auth-switch">
                <a href="<?= e(page_url('login')) ?>">Retour à la connexion</a>
            </p>
        </div>
    </div>
</section>
