<?php
namespace App\Models;

use App\Core\Model;
use App\Models\Setting;

class Branch extends Model {
    protected $table = 'branches';

    public function active() {
        $stmt = $this->db->prepare("SELECT * FROM branches WHERE is_active = 1 ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findByToken($token) {
        $stmt = $this->db->prepare("SELECT * FROM branches WHERE registration_token = :token AND is_active = 1 LIMIT 1");
        $stmt->execute(['token' => $token]);
        return $stmt->fetch();
    }

    public function findBySlug($slug) {
        $stmt = $this->db->prepare("SELECT * FROM branches WHERE slug = :slug AND is_active = 1 LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch();
    }

    public function publicList() {
        $stmt = $this->db->prepare("
            SELECT b.*, COALESCE(NULLIF(b.pastor_name, ''), u.name) as display_pastor_name
            FROM branches b
            LEFT JOIN users u ON u.id = b.pastor_user_id
            WHERE b.is_active = 1
            ORDER BY b.name ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function headquarters() {
        $stmt = $this->db->prepare("SELECT * FROM branches WHERE is_active = 1 AND is_headquarters = 1 LIMIT 1");
        $stmt->execute();
        $branch = $stmt->fetch();
        if ($branch) {
            return $branch;
        }

        $stmt = $this->db->prepare("SELECT * FROM branches WHERE is_active = 1 ORDER BY id ASC LIMIT 1");
        $stmt->execute();
        return $stmt->fetch();
    }

    public function paymentConfig($branchId) {
        $branch = $this->find($branchId);
        if (!$branch) {
            return [
                'public_key' => Setting::get('paystack_public_key', PAYSTACK_PUBLIC_KEY),
                'secret_key' => Setting::getSecret('paystack_secret_key', PAYSTACK_SECRET_KEY),
            ];
        }

        return [
            'public_key' => $branch['paystack_public_key'] ?: Setting::get('paystack_public_key', PAYSTACK_PUBLIC_KEY),
            'secret_key' => Setting::decryptValue($branch['paystack_secret_key'] ?? '') ?: Setting::getSecret('paystack_secret_key', PAYSTACK_SECRET_KEY),
        ];
    }

    public function smtpConfig($branchId) {
        $branch = $this->find($branchId);
        if (!$branch) {
            return [];
        }

        return [
            'host' => $branch['smtp_host'] ?? '',
            'port' => (int)($branch['smtp_port'] ?? 0),
            'encryption' => $branch['smtp_encryption'] ?? '',
            'user' => $branch['smtp_user'] ?? '',
            'pass' => Setting::decryptValue($branch['smtp_pass'] ?? ''),
            'from' => $branch['email'] ?? '',
            'from_name' => $branch['name'] ?? '',
        ];
    }
}
