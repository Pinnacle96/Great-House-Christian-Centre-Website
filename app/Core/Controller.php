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
            ob_start();
            require 'app/Views/' . $view . '.php';
            echo $this->injectCsrfFields(ob_get_clean());
        } else {
            die("View does not exist: " . $view);
        }
    }

    private function injectCsrfFields($html) {
        if (stripos($html, '<form') === false || stripos($html, 'method=') === false) {
            return $html;
        }

        $field = Security::csrfField();

        return preg_replace_callback('/<form\b(?=[^>]*\bmethod\s*=\s*["\']?post["\']?)[^>]*>.*?<\/form>/is', function ($matches) use ($field) {
            $form = $matches[0];
            if (preg_match('/name\s*=\s*["\']_csrf_token["\']/i', $form)) {
                return $form;
            }

            return preg_replace('/(<form\b[^>]*>)/i', '$1' . $field, $form, 1);
        }, $html);
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

    protected function requireConfirmedAction($fallback = '/admin') {
        if (($_POST['_confirmed_action'] ?? '') === '1') {
            return;
        }

        $_SESSION['error'] = 'Please confirm this action before continuing.';
        $this->redirectBack($fallback);
    }

    protected function paginationParams($defaultPerPage = 15, array $allowedPerPage = [10, 15, 25, 50]) {
        $defaultPerPage = in_array((int)$defaultPerPage, $allowedPerPage, true) ? (int)$defaultPerPage : 15;
        $page = max(1, (int)($_GET['p'] ?? 1));
        $perPage = (int)($_GET['per_page'] ?? $defaultPerPage);

        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = $defaultPerPage;
        }

        return [
            'page' => $page,
            'per_page' => $perPage,
            'offset' => ($page - 1) * $perPage,
            'allowed_per_page' => $allowedPerPage,
        ];
    }

    protected function paginationMeta($total, array $pagination, $label = 'records') {
        $total = max(0, (int)$total);
        $perPage = max(1, (int)($pagination['per_page'] ?? 15));
        $totalPages = max(1, (int)ceil($total / $perPage));
        $page = min(max(1, (int)($pagination['page'] ?? 1)), $totalPages);
        $offset = ($page - 1) * $perPage;

        return [
            'page' => $page,
            'per_page' => $perPage,
            'offset' => $offset,
            'total' => $total,
            'total_pages' => $totalPages,
            'from' => $total > 0 ? $offset + 1 : 0,
            'to' => min($total, $offset + $perPage),
            'label' => $label,
            'allowed_per_page' => $pagination['allowed_per_page'] ?? [10, 15, 25, 50],
        ];
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
