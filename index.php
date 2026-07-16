<?php

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

// Pages de traitement (sans layout)
if (in_array($currentPage, ['contact-submit', 'logout', 'admin-save'], true)) {
    require $pageFile;
    exit;
}

// Les pages définissent éventuellement $bodyClass / $adminTab avant le HTML
ob_start();
require $pageFile;
$pageContent = ob_get_clean();

if ($currentPage === 'admin') {
    require __DIR__ . '/app/includes/admin-header.php';
    echo $pageContent;
    require __DIR__ . '/app/includes/admin-footer.php';
    exit;
}

require __DIR__ . '/app/includes/header.php';
echo $pageContent;
require __DIR__ . '/app/includes/footer.php';
