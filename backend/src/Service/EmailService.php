<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class EmailService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly string $contactEmail,
        private readonly ?string $smtpUser,
    ) {
    }

    /** @return array{sent: bool, reason?: string} */
    public function sendNotification(string $subject, string $text, string $html): array
    {
        if ($this->smtpUser === null || $this->smtpUser === '') {
            error_log('[email] SMTP non configuré — notification ignorée: '.$subject);

            return ['sent' => false, 'reason' => 'smtp_not_configured'];
        }

        $email = (new Email())
            ->from(sprintf('"SowCoder Site" <%s>', $this->smtpUser))
            ->to($this->contactEmail)
            ->replyTo($this->contactEmail)
            ->subject($subject)
            ->text($text)
            ->html($html);

        $this->mailer->send($email);

        return ['sent' => true];
    }

    /** @param array<string, string|null> $data */
    public function formatContact(array $data): array
    {
        $subject = sprintf('[Contact] %s — %s', $data['subject'] ?? 'Nouveau message', $data['name']);
        $text = implode("\n", [
            'Nom: '.$data['name'],
            'Email: '.$data['email'],
            'Téléphone: '.($data['phone'] ?? '—'),
            'Sujet: '.($data['subject'] ?? '—'),
            '',
            $data['message'],
        ]);

        $html = sprintf(
            '<h2>Nouveau message de contact</h2><p><strong>Nom:</strong> %s</p><p><strong>Email:</strong> %s</p><p><strong>Téléphone:</strong> %s</p><p><strong>Sujet:</strong> %s</p><hr><p>%s</p>',
            $this->escape($data['name']),
            $this->escape($data['email']),
            $this->escape($data['phone'] ?? '—'),
            $this->escape($data['subject'] ?? '—'),
            nl2br($this->escape($data['message'])),
        );

        return compact('subject', 'text', 'html');
    }

    /** @param array<string, string|null> $data */
    public function formatOrder(array $data): array
    {
        $subject = sprintf('[Commande] %s — %s', $data['productTitle'], $data['name']);
        $text = implode("\n", array_filter([
            'Produit: '.$data['productTitle'],
            'Nom: '.$data['name'],
            'Email: '.$data['email'],
            'Téléphone: '.($data['phone'] ?? '—'),
            !empty($data['message']) ? "\nMessage:\n".$data['message'] : null,
        ]));

        $html = sprintf(
            '<h2>Nouvelle demande de commande</h2><p><strong>Produit:</strong> %s</p><p><strong>Nom:</strong> %s</p><p><strong>Email:</strong> %s</p><p><strong>Téléphone:</strong> %s</p>',
            $this->escape($data['productTitle']),
            $this->escape($data['name']),
            $this->escape($data['email']),
            $this->escape($data['phone'] ?? '—'),
        );

        return compact('subject', 'text', 'html');
    }

    /** @param array<string, string|null> $data */
    public function formatInscription(array $data): array
    {
        $subject = sprintf('[Inscription] %s — %s', $data['formationTitle'], $data['name']);
        $text = implode("\n", array_filter([
            'Formation: '.$data['formationTitle'],
            'Nom: '.$data['name'],
            'Email: '.$data['email'],
            'Téléphone: '.($data['phone'] ?? '—'),
            !empty($data['message']) ? "\nMessage:\n".$data['message'] : null,
        ]));

        $html = sprintf(
            '<h2>Nouvelle inscription formation</h2><p><strong>Formation:</strong> %s</p><p><strong>Nom:</strong> %s</p><p><strong>Email:</strong> %s</p>',
            $this->escape($data['formationTitle']),
            $this->escape($data['name']),
            $this->escape($data['email']),
        );

        return compact('subject', 'text', 'html');
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
