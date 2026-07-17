<?php

declare(strict_types=1);

$next = (string) ($_GET['next'] ?? $_POST['next'] ?? '');
$allowedNext = ['admin', 'home', 'profile'];
if (!in_array($next, $allowedNext, true)) {
    $next = '';
}

if (auth_check()) {
    if ($next === 'profile') {
        redirect_to('profile');
    }
    if (auth_is_admin() && $next === 'admin') {
        redirect_to('admin');
    }
    redirect_to(auth_is_admin() ? 'admin' : 'home');
}

$errors = [];
$email = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!csrf_verify($_POST['_csrf'] ?? null)) {
        $errors[] = 'Session expirée. Rechargez la page.';
    } else {
        $email = mb_strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');

        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'Indiquez une adresse e-mail valide.';
        }
        if ($password === '') {
            $errors[] = 'Le mot de passe est requis.';
        }

        if ($errors === []) {
            try {
                $user = auth_find_by_email($email);
                if ($user === null || !password_verify($password, (string) $user['password'])) {
                    $errors[] = 'E-mail ou mot de passe incorrect.';
                } else {
                    auth_login($user);
                    if (($user['role'] ?? '') === 'admin' && ($next === 'admin' || $next === '')) {
                        redirect_to('admin');
                    }
                    redirect_to('home', ['welcome' => '1']);
                }
            } catch (Throwable $e) {
                $errors[] = 'Connexion impossible pour le moment. Réessayez.';
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
            <h2>Bienvenue dans votre espace digital</h2>
            <p>Connectez-vous pour suivre vos échanges et démarrer vos projets plus vite.</p>
        </div>
    </div>

    <div class="auth-panel">
        <div class="auth-card">
            <p class="eyebrow">Connexion</p>
            <h1>Se connecter</h1>
            <p class="auth-lead">Accédez à votre compte SowCoder.</p>

            <?php if ($errors !== []): ?>
                <div class="alert alert-error" role="alert">
                    <?php foreach ($errors as $error): ?>
                        <p><?= e($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (($_GET['registered'] ?? '') === '1'): ?>
                <div class="alert alert-ok" role="status">Compte créé. Vous pouvez vous connecter.</div>
            <?php endif; ?>

            <form class="auth-form" method="post" action="<?= e(page_url('login')) ?>" novalidate>
                <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                <?php if ($next !== ''): ?>
                    <input type="hidden" name="next" value="<?= e($next) ?>">
                <?php endif; ?>
                <div class="field">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required autocomplete="email" value="<?= e($email) ?>">
                </div>
                <div class="field">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password" minlength="8">
                </div>
                <p class="auth-forgot">
                    <a href="<?= e(page_url('forgot-password')) ?>">Mot de passe oublié ?</a>
                </p>
                <button class="btn btn-primary btn-block" type="submit">Se connecter</button>
            </form>

            <p class="auth-switch">
                Pas encore de compte ?
                <a href="<?= e(page_url('register')) ?>">Créer un compte</a>
            </p>
        </div>
    </div>
</section>
