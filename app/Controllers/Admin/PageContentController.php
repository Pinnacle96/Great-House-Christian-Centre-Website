<?php
namespace App\Controllers\Admin;

use App\Core\BranchScope;
use App\Core\Controller;
use App\Models\PageContent;
use App\Models\Setting;
use App\Services\CommunicationService;
use App\Services\PaystackService;

class PageContentController extends Controller {
    
    public function __construct() {
        $this->requireAuth();
        BranchScope::requireGlobalFrontendAccess();
    }

    public function index() {
        $page = $_GET['page'] ?? 'home';
        $content = PageContent::getPageContent($page);
        
        $this->view('admin/page_content/index', [
            'title' => 'Edit Page Content - ' . ucfirst($page),
            'page' => $page,
            'content' => $content
        ]);
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pageContentModel = new PageContent();
            
            // Handle text/html content
            if (isset($_POST['content'])) {
                foreach ($_POST['content'] as $id => $value) {
                    $pageContentModel->updateContent($id, $value);
                }
            }
            
            // Handle image uploads
            if (!empty($_FILES['images']['name'])) {
                foreach ($_FILES['images']['name'] as $id => $name) {
                    if (!empty($name)) {
                        $file = [
                            'name' => $name,
                            'type' => $_FILES['images']['type'][$id] ?? '',
                            'tmp_name' => $_FILES['images']['tmp_name'][$id],
                            'error' => $_FILES['images']['error'][$id] ?? UPLOAD_ERR_OK,
                            'size' => $_FILES['images']['size'][$id] ?? 0,
                        ];
                        $filename = $this->storeImageUpload($file, 'assets/uploads', 'content_' . $id);
                        if ($filename) {
                            $pageContentModel->updateContent($id, $filename);
                        } else {
                            $_SESSION['error'] = 'One or more content images were invalid. Use JPG, PNG, WebP, or GIF under 2MB.';
                        }
                    }
                }
            }
            
            if (empty($_SESSION['error'])) {
                $_SESSION['success'] = "Page content updated successfully!";
            }
            $this->redirect('/admin/page-content?page=' . ($_POST['page_name'] ?? 'home'));
        }
    }

    public function settings() {
        $this->requireRoles([1]);
        $settings = Setting::getAll();
        $this->view('admin/page_content/settings', [
            'title' => 'Global Settings',
            'settings' => $settings
        ]);
    }

    public function updateSettings() {
        $this->requireRoles([1]);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $settingModel = new Setting();
            
            // Handle text settings
            if (isset($_POST['settings'])) {
                $allowedSettings = [
                    'site_name',
                    'site_email',
                    'paystack_public_key',
                    'smtp_host',
                    'smtp_port',
                    'smtp_encryption',
                    'smtp_user',
                ];
                $secretSettings = [
                    'paystack_secret_key',
                    'smtp_pass',
                ];

                foreach ($_POST['settings'] as $key => $value) {
                    if (in_array($key, $secretSettings, true)) {
                        $value = trim((string)$value);
                        if ($value !== '') {
                            $settingModel->updateSecret($key, $value);
                        }
                        continue;
                    }

                    if (!in_array($key, $allowedSettings, true)) {
                        continue;
                    }

                    if ($key === 'smtp_port') {
                        $value = (string)max(1, min(65535, (int)$value));
                    }

                    if ($key === 'smtp_encryption' && !in_array($value, ['tls', 'ssl'], true)) {
                        $value = 'tls';
                    }

                    $settingModel->updateSetting($key, trim((string)$value));
                }
            }
            
            // Handle file uploads (logo, favicon)
            if (!empty($_FILES['files']['name'])) {
                foreach ($_FILES['files']['name'] as $key => $name) {
                    if (!empty($name)) {
                        $file = [
                            'name' => $name,
                            'type' => $_FILES['files']['type'][$key] ?? '',
                            'tmp_name' => $_FILES['files']['tmp_name'][$key],
                            'error' => $_FILES['files']['error'][$key] ?? UPLOAD_ERR_OK,
                            'size' => $_FILES['files']['size'][$key] ?? 0,
                        ];
                        $filename = $this->storeImageUpload($file, 'assets/logo', $key);
                        if ($filename) {
                            $settingModel->updateSetting($key, $filename);
                        } else {
                            $_SESSION['error'] = 'One or more uploaded brand files were invalid. Use JPG, PNG, WebP, or GIF under 2MB.';
                        }
                    }
                }
            }
            
            if (empty($_SESSION['error'])) {
                $_SESSION['success'] = "Settings updated successfully!";
            }
            $this->redirect('/admin/settings');
        }
    }

    public function testEmail() {
        $this->requireRoles([1]);

        $to = Setting::get('site_email', MAIL_FROM);
        if (!$to || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Set a valid Site Email before testing SMTP.';
            $this->redirect('/admin/settings');
        }

        $service = new CommunicationService();
        $sent = $service->sendEmail(
            $to,
            'GHCC SMTP Test',
            '<p>This is a test email from your GHCC production configuration.</p>'
        );

        $_SESSION[$sent ? 'success' : 'error'] = $sent
            ? 'SMTP test email sent successfully.'
            : 'SMTP test failed. Check SMTP host, port, username, password, encryption, and logs.';

        $this->redirect('/admin/settings');
    }

    public function testPaystack() {
        $this->requireRoles([1]);

        $service = new PaystackService();
        $result = $service->testConnection();

        $_SESSION[($result && !empty($result->status)) ? 'success' : 'error'] =
            ($result && !empty($result->status))
                ? 'Paystack connection verified successfully.'
                : 'Paystack test failed: ' . ($result->message ?? 'Unknown error');

        $this->redirect('/admin/settings');
    }
}
