<?php

declare(strict_types=1);

auth_require_login();

$user = auth_find_by_id((int) (auth_user()['id'] ?? 0));
if ($user === null) {
    auth_logout();
    redirect_to('login');
}

$errors = [];
$success = '';
$name = (string) $user['name'];
$email = (string) $user['email'];
$activeForm = (string) ($_POST['form'] ?? 'profile');
$avatarUrl = auth_avatar_url($user);
$avatarInitials = auth_avatar_initials($user);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!csrf_verify($_POST['_csrf'] ?? null)) {
        $errors[] = 'Session expirée. Rechargez la page.';
    } elseif ($activeForm === 'avatar') {
        try {
            if (empty($_FILES['avatar']) || !is_array($_FILES['avatar'])) {
                throw new RuntimeException('Choisissez une image.');
            }
            auth_update_avatar((int) $user['id'], $_FILES['avatar']);
            $user = auth_find_by_id((int) $user['id']) ?? $user;
            $avatarUrl = auth_avatar_url($user);
            $success = 'Photo de profil mise à jour.';
        } catch (Throwable $e) {
            $errors[] = $e->getMessage();
        }
    } elseif ($activeForm === 'profile') {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = mb_strtolower(trim((string) ($_POST['email'] ?? '')));

        if ($name === '' || mb_strlen($name) > 120) {
            $errors[] = 'Indiquez un nom valide (120 caractères max).';
        }
        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'Indiquez une adresse e-mail valide.';
        } elseif (auth_email_exists($email, (int) $user['id'])) {
            $errors[] = 'Cet e-mail est déjà utilisé par un autre compte.';
        }

        if ($errors === []) {
            try {
                auth_update_profile((int) $user['id'], $name, $email);
                $success = 'Profil mis à jour.';
                $user = auth_find_by_id((int) $user['id']) ?? $user;
                $avatarInitials = auth_avatar_initials($user);
            } catch (Throwable $e) {
                $errors[] = 'Impossible d’enregistrer le profil.';
            }
        }
    } elseif ($activeForm === 'password') {
        $current = (string) ($_POST['current_password'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['password_confirm'] ?? '');

        if ($current === '' || !password_verify($current, (string) $user['password'])) {
            $errors[] = 'Mot de passe actuel incorrect.';
        }
        if (mb_strlen($password) < 8) {
            $errors[] = 'Le nouveau mot de passe doit contenir au moins 8 caractères.';
        }
        if ($password !== $confirm) {
            $errors[] = 'La confirmation ne correspond pas.';
        }

        if ($errors === []) {
            try {
                auth_update_password((int) $user['id'], $password);
                $success = 'Mot de passe modifié.';
                $activeForm = 'password';
            } catch (Throwable $e) {
                $errors[] = 'Impossible de changer le mot de passe.';
            }
        }
    }
}

$bodyClass = 'page-auth';
$csrf = csrf_token();
?>

<section class="auth-shell auth-shell-wide">
    <div class="auth-visual" aria-hidden="true">
        <div class="auth-visual-inner">
            <p class="auth-brand">SowCoder</p>
            <h2>Votre profil</h2>
            <p>Gérez vos informations et sécurisez votre compte.</p>
        </div>
    </div>

    <div class="auth-panel">
        <div class="auth-card auth-card-wide auth-card-profile">
            <form class="profile-avatar-form" method="post" action="<?= e(page_url('profile')) ?>" enctype="multipart/form-data">
                <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                <input type="hidden" name="form" value="avatar">
                <label class="profile-avatar" title="Changer la photo">
                    <span class="profile-avatar-media" data-avatar-preview>
                        <?php if ($avatarUrl !== ''): ?>
                            <img src="<?= e($avatarUrl) ?>" alt="Photo de profil de <?= e((string) $user['name']) ?>">
                        <?php else: ?>
                            <span class="profile-avatar-initials"><?= e($avatarInitials) ?></span>
                        <?php endif; ?>
                    </span>
                    <span class="profile-avatar-badge" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16V8"/><path d="M8.5 11.5 12 8l3.5 3.5"/><path d="M4 19h16"/></svg>
                    </span>
                    <input class="sr-only" type="file" name="avatar" accept="image/jpeg,image/png,image/webp,image/gif" data-avatar-input>
                </label>
            </form>

            <p class="eyebrow">Compte</p>
            <h1>Mon profil</h1>
            <p class="auth-lead"><?= e((string) $user['email']) ?></p>

            <?php if (($_GET['reset'] ?? '') === '1'): ?>
                <div class="alert alert-ok" role="status">Mot de passe initialisé. Votre compte est à jour.</div>
            <?php endif; ?>

            <?php if ($success !== ''): ?>
                <div class="alert alert-ok" role="status"><?= e($success) ?></div>
            <?php endif; ?>

            <?php if ($errors !== []): ?>
                <div class="alert alert-error" role="alert">
                    <?php foreach ($errors as $error): ?>
                        <p><?= e($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <h2 class="auth-section-title">Informations</h2>
            <form class="auth-form" method="post" action="<?= e(page_url('profile')) ?>">
                <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                <input type="hidden" name="form" value="profile">
                <div class="field">
                    <label for="name">Nom complet</label>
                    <input type="text" id="name" name="name" required maxlength="120" value="<?= e($name) ?>">
                </div>
                <div class="field">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required value="<?= e($email) ?>">
                </div>
                <button class="btn btn-primary btn-block" type="submit">Enregistrer le profil</button>
            </form>

            <h2 class="auth-section-title">Mot de passe</h2>
            <form class="auth-form" method="post" action="<?= e(page_url('profile')) ?>">
                <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                <input type="hidden" name="form" value="password">
                <div class="field">
                    <label for="current_password">Mot de passe actuel</label>
                    <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
                </div>
                <div class="field">
                    <label for="password">Nouveau mot de passe</label>
                    <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
                </div>
                <div class="field">
                    <label for="password_confirm">Confirmer</label>
                    <input type="password" id="password_confirm" name="password_confirm" required minlength="8" autocomplete="new-password">
                </div>
                <button class="btn btn-primary btn-block" type="submit">Changer le mot de passe</button>
            </form>

            <p class="auth-switch">
                <a href="<?= e(page_url('forgot-password')) ?>">Réinitialiser via e-mail</a>
                ·
                <a href="<?= e(page_url('logout')) ?>">Déconnexion</a>
            </p>
        </div>
    </div>
</section>
