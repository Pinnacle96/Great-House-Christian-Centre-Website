<?php
namespace App\Models;

use App\Core\Model;

class AuditLog extends Model {
    protected $table = 'audit_logs';

    public static function record($method, $path) {
        try {
            $model = new self();
            $model->create([
                'user_id' => $_SESSION['user_id'] ?? null,
                'method' => $method,
                'path' => substr($path, 0, 255),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            ]);
        } catch (\Throwable $e) {
            error_log('Audit log failed: ' . $e->getMessage());
        }
    }
}
