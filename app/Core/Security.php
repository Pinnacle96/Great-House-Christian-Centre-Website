<?php
namespace App\Core;

class Security {
    public static function csrfToken() {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    public static function csrfField() {
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars(self::csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function validateCsrf($token) {
        return is_string($token)
            && isset($_SESSION['_csrf_token'])
            && hash_equals($_SESSION['_csrf_token'], $token);
    }

    public static function requireCsrf() {
        $token = $_POST['_csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        if (!self::validateCsrf($token)) {
            http_response_code(419);
            echo 'Security token expired or invalid. Please go back, refresh the page, and try again.';
            exit;
        }
    }

    public static function rateLimit($key, $maxAttempts, $windowSeconds) {
        $now = time();
        $bucketKey = '_rate_limit_' . preg_replace('/[^a-zA-Z0-9_.:-]/', '_', $key);
        $attempts = $_SESSION[$bucketKey] ?? [];
        $attempts = array_values(array_filter($attempts, function ($timestamp) use ($now, $windowSeconds) {
            return $timestamp > ($now - $windowSeconds);
        }));

        if (count($attempts) >= $maxAttempts) {
            return false;
        }

        $attempts[] = $now;
        $_SESSION[$bucketKey] = $attempts;
        return true;
    }

    public static function clientIp() {
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }

    public static function safeRedirectTarget($fallback = '/admin') {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (!$referer) {
            return APP_URL . $fallback;
        }

        $appHost = parse_url(APP_URL, PHP_URL_HOST);
        $refererHost = parse_url($referer, PHP_URL_HOST);

        if ($appHost && $refererHost && strtolower($appHost) === strtolower($refererHost)) {
            return $referer;
        }

        return APP_URL . $fallback;
    }

    public static function isAllowedImageUpload($file) {
        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return false;
        }

        if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
            return false;
        }

        $allowedMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        return isset($allowedMimeTypes[$mimeType]) ? $allowedMimeTypes[$mimeType] : false;
    }
}
