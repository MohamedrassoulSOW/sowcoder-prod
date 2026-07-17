<?php

declare(strict_types=1);

if (auth_check()) {
    redirect_to('home');
}

$errors = [];
$name = '';
$email = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!csrf_verify($_POST['_csrf'] ?? null)) {
        $errors[] = 'Session expirée. Rechargez la page.';
    } else {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = mb_strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');
        $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

        if ($name === '' || mb_strlen($name) > 120) {
            $errors[] = 'Indiquez votre nom (120 caractères max).';
        }
        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'Indiquez une adresse e-mail valide.';
        }
        if (mb_strlen($password) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }
        if ($password !== $passwordConfirm) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }

        if ($errors === []) {
            try {
                if (auth_find_by_email($email) !== null) {
                    $errors[] = 'Un compte existe déjà avec cet e-mail.';
                } else {
                    $user = auth_register($name, $email, $password);
                    auth_login($user);
                    redirect_to('home', ['welcome' => '1']);
                }
            } catch (Throwable $e) {
                $errors[] = 'Inscription impossible pour le moment. Réessayez.';
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
            <h2>Créez votre compte en quelques secondes</h2>
            <p>Rejoignez SowCoder pour lancer vos projets web, design et marketing.</p>
        </div>
    </div>

    <div class="auth-panel">
        <div class="auth-card">
            <p class="eyebrow">Inscription</p>
            <h1>Créer un compte</h1>
            <p class="auth-lead">Gratuit, rapide et sécurisé.</p>

            <?php if ($errors !== []): ?>
                <div class="alert alert-error" role="alert">
                    <?php foreach ($errors as $error): ?>
                        <p><?= e($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="post" action="<?= e(page_url('register')) ?>" novalidate>
                <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                <div class="field">
                    <label for="name">Nom complet</label>
                    <input type="text" id="name" name="name" required autocomplete="name" maxlength="120" value="<?= e($name) ?>">
                </div>
                <div class="field">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required autocomplete="email" value="<?= e($email) ?>">
                </div>
                <div class="field-row">
                    <div class="field">
                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" required autocomplete="new-password" minlength="8">
                    </div>
                    <div class="field">
                        <label for="password_confirm">Confirmer</label>
                        <input type="password" id="password_confirm" name="password_confirm" required autocomplete="new-password" minlength="8">
                    </div>
                </div>
                <button class="btn btn-primary btn-block" type="submit">S’inscrire</button>
            </form>

            <p class="auth-switch">
                Déjà inscrit ?
                <a href="<?= e(page_url('login')) ?>">Se connecter</a>
            </p>
        </div>
    </div>
</section>
