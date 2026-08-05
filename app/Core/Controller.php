<?php
namespace App\Core;

class Controller {
    
    public function view($view, $data = []) {
        // Load Global Settings
        $settings = \App\Models\Setting::getAll();
        $data['settings'] = $settings;
        $data['csrf_token'] = Security::csrfToken();
        
        extract($data);
        
        // Check if view file exists
        if (file_exists('app/Views/' . $view . '.php')) {
            require_once 'app/Views/' . $view . '.php';
        } else {
            die("View does not exist: " . $view);
        }
    }

    public function redirect($url, $absolute = false) {
        if ($absolute) {
            header("Location: " . $url);
        } else {
            header("Location: " . APP_URL . $url);
        }
        exit;
    }

    protected function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
    }

    protected function requireRoles(array $roleIds) {
        $this->requireAuth();
        if (!in_array((int)($_SESSION['role_id'] ?? 0), $roleIds, true)) {
            $_SESSION['error'] = 'Access denied.';
            $this->redirect('/admin');
        }
    }

    protected function redirectBack($fallback = '/admin') {
        header('Location: ' . Security::safeRedirectTarget($fallback));
        exit;
    }

    protected function storeImageUpload($file, $targetDir, $prefix = 'upload') {
        $extension = Security::isAllowedImageUpload($file);
        if (!$extension) {
            return null;
        }

        $targetDir = rtrim($targetDir, '/\\') . '/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $filename = $prefix . '_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $extension;
        $targetFile = $targetDir . $filename;

        return move_uploaded_file($file['tmp_name'], $targetFile) ? $targetFile : null;
    }
}
