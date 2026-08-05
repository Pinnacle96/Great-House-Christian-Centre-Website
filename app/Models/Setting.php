<?php
namespace App\Models;

use App\Core\Model;

class Setting extends Model {
    protected $table = 'settings';

    public static function get($key, $default = null) {
        $model = new self();
        $setting = $model->first('setting_key', $key);
        return $setting ? $setting['setting_value'] : $default;
    }

    public static function getAll() {
        $model = new self();
        $stmt = $model->db->query("SELECT setting_key, setting_value FROM settings");
        $results = $stmt->fetchAll();
        
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    public function updateSetting($key, $value) {
        $stmt = $this->db->prepare("
            INSERT INTO settings (setting_key, setting_value)
            VALUES (:key, :value)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
        ");
        return $stmt->execute(['value' => $value, 'key' => $key]);
    }

    public static function getSecret($key, $default = '') {
        $value = self::get($key, $default);
        if (!is_string($value) || strpos($value, 'enc:') !== 0) {
            return $value;
        }

        if (empty(APP_SECRET_KEY)) {
            return '';
        }

        $payload = base64_decode(substr($value, 4), true);
        if (!$payload || strlen($payload) <= 16) {
            return '';
        }

        $iv = substr($payload, 0, 16);
        $ciphertext = substr($payload, 16);
        $keyMaterial = hash('sha256', APP_SECRET_KEY, true);
        $plain = openssl_decrypt($ciphertext, 'AES-256-CBC', $keyMaterial, OPENSSL_RAW_DATA, $iv);

        return $plain === false ? '' : $plain;
    }

    public function updateSecret($key, $value) {
        if ($value === '') {
            return true;
        }

        return $this->updateSetting($key, self::encryptValue($value));
    }

    public static function encryptValue($value) {
        if ($value === '' || empty(APP_SECRET_KEY)) {
            return $value;
        }

        $iv = random_bytes(16);
        $keyMaterial = hash('sha256', APP_SECRET_KEY, true);
        $ciphertext = openssl_encrypt($value, 'AES-256-CBC', $keyMaterial, OPENSSL_RAW_DATA, $iv);

        return $ciphertext === false ? $value : 'enc:' . base64_encode($iv . $ciphertext);
    }

    public static function decryptValue($value) {
        if (!is_string($value) || strpos($value, 'enc:') !== 0) {
            return $value;
        }

        if (empty(APP_SECRET_KEY)) {
            return '';
        }

        $payload = base64_decode(substr($value, 4), true);
        if (!$payload || strlen($payload) <= 16) {
            return '';
        }

        $iv = substr($payload, 0, 16);
        $ciphertext = substr($payload, 16);
        $keyMaterial = hash('sha256', APP_SECRET_KEY, true);
        $plain = openssl_decrypt($ciphertext, 'AES-256-CBC', $keyMaterial, OPENSSL_RAW_DATA, $iv);

        return $plain === false ? '' : $plain;
    }
}
