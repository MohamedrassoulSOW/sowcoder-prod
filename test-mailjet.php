<?php

declare(strict_types=1);

/**
 * Script de test Mailjet (à lancer en CLI ou via le navigateur en local uniquement).
 *
 * CLI :
 *   php test-mailjet.php reset vous@exemple.com "Votre Nom"
 *   php test-mailjet.php newsletter-add vous@exemple.com
 *   php test-mailjet.php newsletter-unsub vous@exemple.com
 */

require __DIR__ . '/app/env.php';

load_env(__DIR__);

use App\MailjetService;

// Sécurité : CLI uniquement (bloqué aussi via .htaccess en HTTP)
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Accès refusé. Utilisez : php test-mailjet.php …\n";
    exit(1);
}

$action = $argv[1] ?? ($_GET['action'] ?? 'help');
$email = $argv[2] ?? ($_GET['email'] ?? '');
$name = $argv[3] ?? ($_GET['name'] ?? 'Utilisateur test');

try {
    $mailjet = new MailjetService();
    $listId = (int) ($_ENV['MAILJET_NEWSLETTER_LIST_ID'] ?? getenv('MAILJET_NEWSLETTER_LIST_ID') ?: 0);

    switch ($action) {
        case 'reset':
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException('E-mail invalide. Usage : php test-mailjet.php reset email@exemple.com "Nom"');
            }
            $link = 'http://localhost/mon-site-vitrine/?page=reset-password&token=TEST_TOKEN_DEMO';
            $ok = $mailjet->sendPasswordResetEmail($email, (string) $name, $link);
            echo $ok
                ? "OK — e-mail de reset envoyé à {$email}\n"
                : "ÉCHEC — vérifiez les clés Mailjet et l’expéditeur validé.\n";
            break;

        case 'newsletter-add':
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException('E-mail invalide.');
            }
            $ok = $mailjet->manageNewsletterSubscription($email, $listId, 'addforce');
            echo $ok
                ? "OK — {$email} inscrit à la liste {$listId}\n"
                : "ÉCHEC — inscription newsletter.\n";
            break;

        case 'newsletter-unsub':
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException('E-mail invalide.');
            }
            $ok = $mailjet->manageNewsletterSubscription($email, $listId, 'unsub');
            echo $ok
                ? "OK — {$email} désinscrit de la liste {$listId}\n"
                : "ÉCHEC — désinscription newsletter.\n";
            break;

        default:
            echo "Actions disponibles :\n"
                . "  php test-mailjet.php reset email@exemple.com \"Nom\"\n"
                . "  php test-mailjet.php newsletter-add email@exemple.com\n"
                . "  php test-mailjet.php newsletter-unsub email@exemple.com\n";
            break;
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Erreur : ' . $e->getMessage() . "\n";
    exit(1);
}
