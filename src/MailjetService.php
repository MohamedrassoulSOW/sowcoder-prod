<?php

declare(strict_types=1);

namespace App;

use Mailjet\Client;
use Mailjet\Resources;

/**
 * Service Mailjet réutilisable (envoi d’e-mails + gestion newsletter).
 */
final class MailjetService
{
    private string $apiKey;
    private string $apiSecret;
    private string $fromEmail;
    private string $fromName;
    private Client $client;

    public function __construct(
        ?string $apiKey = null,
        ?string $apiSecret = null,
        ?string $fromEmail = null,
        ?string $fromName = null
    ) {
        $this->apiKey = $apiKey ?? (string) ($_ENV['MAILJET_API_KEY'] ?? getenv('MAILJET_API_KEY') ?: '');
        $this->apiSecret = $apiSecret ?? (string) ($_ENV['MAILJET_API_SECRET'] ?? getenv('MAILJET_API_SECRET') ?: '');
        $this->fromEmail = $fromEmail ?? (string) ($_ENV['MAILJET_FROM_EMAIL'] ?? getenv('MAILJET_FROM_EMAIL') ?: 'contact@sowcoder.com');
        $this->fromName = $fromName ?? (string) ($_ENV['MAILJET_FROM_NAME'] ?? getenv('MAILJET_FROM_NAME') ?: 'SowCoser');

        if ($this->apiKey === '' || $this->apiSecret === '') {
            throw new \RuntimeException('Clés Mailjet manquantes. Vérifiez le fichier .env.');
        }

        // true = version API v3.1 pour l’envoi d’e-mails
        $this->client = new Client($this->apiKey, $this->apiSecret, true, ['version' => 'v3.1']);
    }

    /**
     * Envoie un e-mail transactionnel de réinitialisation de mot de passe.
     */
    public function sendPasswordResetEmail(string $toEmail, string $toName, string $resetLink): bool
    {
        $html = $this->buildPasswordResetHtml($toName, $resetLink);
        $text = "Bonjour {$toName},\n\n"
            . "Pour réinitialiser votre mot de passe, ouvrez ce lien (valable 1 heure) :\n"
            . "{$resetLink}\n\n"
            . "Si vous n’êtes pas à l’origine de cette demande, ignorez cet e-mail.\n"
            . "— {$this->fromName}";

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $this->fromEmail,
                        'Name' => $this->fromName,
                    ],
                    'To' => [
                        [
                            'Email' => $toEmail,
                            'Name' => $toName !== '' ? $toName : $toEmail,
                        ],
                    ],
                    'Subject' => 'Réinitialisation de votre mot de passe — ' . $this->fromName,
                    'TextPart' => $text,
                    'HTMLPart' => $html,
                ],
            ],
        ];

        $response = $this->client->post(Resources::$Email, ['body' => $body]);

        return $response->success();
    }

    /**
     * Inscrit ou désinscrit un contact d’une liste Mailjet.
     *
     * @param string $action 'addforce' (inscription) ou 'unsub' (désinscription)
     */
    public function manageNewsletterSubscription(string $email, int $listId, string $action): bool
    {
        $action = strtolower(trim($action));
        if (!in_array($action, ['addforce', 'unsub'], true)) {
            throw new \InvalidArgumentException("Action invalide : utilisez 'addforce' ou 'unsub'.");
        }

        if ($listId <= 0) {
            throw new \InvalidArgumentException('MAILJET_NEWSLETTER_LIST_ID invalide.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Adresse e-mail invalide.');
        }

        // API Contacts / Listes = v3 (pas v3.1)
        $contactsClient = new Client($this->apiKey, $this->apiSecret, true, ['version' => 'v3']);

        $body = [
            'Action' => $action,
            'Email' => $email,
        ];

        $response = $contactsClient->post(
            Resources::$ContactslistManagecontact,
            [
                'id' => $listId,
                'body' => $body,
            ]
        );

        return $response->success();
    }

    private function buildPasswordResetHtml(string $toName, string $resetLink): string
    {
        $safeName = htmlspecialchars($toName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $safeLink = htmlspecialchars($resetLink, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $brand = htmlspecialchars($this->fromName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Réinitialisation</title></head>
<body style="font-family:Arial,sans-serif;line-height:1.5;color:#12263a;">
  <p>Bonjour {$safeName},</p>
  <p>Vous avez demandé la réinitialisation (ou l’initialisation) de votre mot de passe.</p>
  <p>
    <a href="{$safeLink}" style="display:inline-block;padding:12px 18px;background:#0d9488;color:#fff;text-decoration:none;border-radius:8px;">
      Réinitialiser mon mot de passe
    </a>
  </p>
  <p>Ou copiez ce lien dans votre navigateur :<br>{$safeLink}</p>
  <p>Ce lien est valable <strong>1 heure</strong>.</p>
  <p>Si vous n’êtes pas à l’origine de cette demande, ignorez cet e-mail.</p>
  <p>— {$brand}</p>
</body>
</html>
HTML;
    }
}
