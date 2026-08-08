<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This setup script can only be run from the command line.');
}

require __DIR__ . '/setup_clean_demo_accounts.php';
