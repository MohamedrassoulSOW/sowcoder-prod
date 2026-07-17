<?php

declare(strict_types=1);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: ' . page_url('contact'));
    exit;
}

if (!csrf_verify($_POST['_csrf'] ?? null)) {
    header('Location: ' . page_url('contact') . '&status=error');
    exit;
}

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$subject = trim((string) ($_POST['subject'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

$valid = $name !== ''
    && $message !== ''
    && filter_var($email, FILTER_VALIDATE_EMAIL) !== false
    && mb_strlen($name) <= 120
    && mb_strlen($message) <= 4000;

if (!$valid) {
    header('Location: ' . page_url('contact') . '&status=error');
    exit;
}

try {
    $stmt = db()->prepare(
        'INSERT INTO contact_messages (name, email, phone, subject, message, ip)
         VALUES (:name, :email, :phone, :subject, :message, :ip)'
    );

    $stmt->execute([
        'name' => $name,
        'email' => $email,
        'phone' => $phone !== '' ? $phone : null,
        'subject' => $subject !== '' ? $subject : 'Demande depuis le site',
        'message' => $message,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
    ]);
} catch (Throwable $e) {
    header('Location: ' . page_url('contact') . '&status=error');
    exit;
}

header('Location: ' . page_url('contact') . '&status=ok');
exit;
