<?php
if (file_exists(__DIR__ . '/../.env')) {
    $envLines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envLines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        if ($key !== '' && getenv($key) === false) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
        }
    }
}

function env_value($key, $default = null) {
    $value = getenv($key);
    return $value === false ? $default : $value;
}

// Base URL Detection
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$base_url = env_value('APP_URL', $protocol . "://" . $host . ($dir == '/' ? '' : $dir));
define('APP_URL', rtrim($base_url, '/'));

// Database Configuration
define('DB_HOST', env_value('DB_HOST', 'localhost'));
define('DB_NAME', env_value('DB_NAME', 'ghcc_db'));
define('DB_USER', env_value('DB_USER', 'root'));
define('DB_PASS', env_value('DB_PASS', ''));

// App Info
define('APP_NAME', env_value('APP_NAME', 'Great House Christian Centre'));

// Mail Configuration
define('MAIL_HOST', env_value('MAIL_HOST', 'smtp.gmail.com'));
define('MAIL_PORT', (int)env_value('MAIL_PORT', 587));
define('MAIL_USER', env_value('MAIL_USER', ''));
define('MAIL_PASS', env_value('MAIL_PASS', ''));
define('MAIL_FROM', env_value('MAIL_FROM', 'noreply@ghcc.org'));
define('MAIL_FROM_NAME', env_value('MAIL_FROM_NAME', 'GHCC Events'));

// Payment Configuration
define('PAYSTACK_SECRET_KEY', env_value('PAYSTACK_SECRET_KEY', ''));
define('PAYSTACK_PUBLIC_KEY', env_value('PAYSTACK_PUBLIC_KEY', ''));
define('APP_SECRET_KEY', env_value('APP_SECRET_KEY', ''));

// Error Reporting
error_reporting(E_ALL);
$isLocal = env_value('APP_ENV', 'local') === 'local';
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('log_errors', '1');
