<?php

declare(strict_types=1);

$token = trim((string) ($_GET['token'] ?? $_POST['token'] ?? ''));
$errors = [];
$reset = null;

if ($token !== '') {
    try {
        $reset = auth_find_valid_reset($token);
    } catch (Throwable $e) {
        $reset = null;
    }
}

if ($token === '' || $reset === null) {
    $bodyClass = 'page-auth';
    ?>
    <section class="auth-shell">
        <div class="auth-panel" style="grid-column: 1 / -1;">
            <div class="auth-card">
                <p class="eyebrow">Sécurité</p>
                <h1>Lien invalide ou expiré</h1>
                <p class="auth-lead">Demandez un nouveau lien de réinitialisation.</p>
                <p class="auth-switch">
                    <a href="<?= e(page_url('forgot-password')) ?>">Mot de passe oublié</a>
                    ·
                    <a href="<?= e(page_url('login')) ?>">Connexion</a>
                </p>
            </div>
        </div>
    </section>
    <?php
    return;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!csrf_verify($_POST['_csrf'] ?? null)) {
        $errors[] = 'Session expirée. Rechargez la page.';
    } else {
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['password_confirm'] ?? '');

        if (mb_strlen($password) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }

        if ($errors === []) {
            try {
                // Re-vérifier le token
                $fresh = auth_find_valid_reset($token);
                if ($fresh === null) {
                    $errors[] = 'Lien invalide ou expiré.';
                } else {
                    auth_update_password((int) $fresh['user_id'], $password);
                    auth_consume_reset((int) $fresh['id']);

                    $user = auth_find_by_id((int) $fresh['user_id']);
                    if ($user !== null) {
                        auth_login($user);
                    }

                    redirect_to('profile', ['reset' => '1']);
                }
            } catch (Throwable $e) {
                $errors[] = 'Impossible d’enregistrer le nouveau mot de passe.';
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
            <h2>Nouveau mot de passe</h2>
            <p>Choisissez un mot de passe sécurisé pour <?= e((string) $reset['email']) ?>.</p>
        </div>
    </div>

    <div class="auth-panel">
        <div class="auth-card">
            <p class="eyebrow">Sécurité</p>
            <h1>Initialiser le mot de passe</h1>
            <p class="auth-lead">Minimum 8 caractères.</p>

            <?php if ($errors !== []): ?>
                <div class="alert alert-error" role="alert">
                    <?php foreach ($errors as $error): ?>
                        <p><?= e($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="post" action="<?= e(page_url('reset-password')) ?>">
                <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                <input type="hidden" name="token" value="<?= e($token) ?>">
                <div class="field">
                    <label for="password">Nouveau mot de passe</label>
                    <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
                </div>
                <div class="field">
                    <label for="password_confirm">Confirmer</label>
                    <input type="password" id="password_confirm" name="password_confirm" required minlength="8" autocomplete="new-password">
                </div>
                <button class="btn btn-primary btn-block" type="submit">Enregistrer</button>
            </form>
        </div>
    </div>
</section>
