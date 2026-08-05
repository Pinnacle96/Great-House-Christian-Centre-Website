<?php
namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Security;
use App\Models\PrayerRequest;
use App\Models\Setting;
use App\Services\CommunicationService;

class ContactController extends Controller {

    public function processContact() {
        if (!Security::rateLimit('contact:' . Security::clientIp(), 5, 600)) {
            $_SESSION['error'] = 'Too many contact submissions. Please wait a few minutes and try again.';
            $this->redirect('/contact');
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $subject === '' || $message === '') {
            $_SESSION['error'] = 'Please complete all required contact fields with a valid email address.';
            $this->redirect('/contact');
        }

        $db = Database::getInstance()->getConnection();
        $this->ensureContactMessagesTable($db);
        $branchId = $this->defaultBranchId($db);

        $stmt = $db->prepare("
            INSERT INTO contact_messages (branch_id, name, email, subject, message, status)
            VALUES (?, ?, ?, ?, ?, 'new')
        ");
        $stmt->execute([$branchId, $name, $email, $subject, $message]);

        $body = "<p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>"
            . "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>"
            . "<p><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>"
            . "<p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>";

        $mail = new CommunicationService();
        $mail->sendEmail(Setting::get('site_email', MAIL_FROM), 'New Contact Message: ' . $subject, $body);

        $_SESSION['success'] = 'Thank you. Your message has been received.';
        $this->redirect('/contact');
    }

    public function submitPrayer() {
        if (!Security::rateLimit('prayer:' . Security::clientIp(), 5, 600)) {
            $_SESSION['error'] = 'Too many prayer submissions. Please wait a few minutes and try again.';
            $this->redirect('/contact#prayer-request');
        }

        $name = trim($_POST['name'] ?? '');
        $request = trim($_POST['request'] ?? '');
        $isPublic = isset($_POST['is_public']) ? 1 : 0;

        if ($request === '') {
            $_SESSION['error'] = 'Please enter your prayer request.';
            $this->redirect('/contact#prayer-request');
        }

        $prayerModel = new PrayerRequest();
        $saved = $prayerModel->create([
            'branch_id' => $this->defaultBranchId(),
            'name' => $name !== '' ? $name : 'Anonymous',
            'request' => $request,
            'is_public' => $isPublic,
            'status' => 'new'
        ]);

        if ($saved) {
            $_SESSION['success'] = 'Your prayer request has been submitted.';
        } else {
            $_SESSION['error'] = 'We could not submit your prayer request. Please try again.';
        }

        $this->redirect('/contact#prayer-request');
    }

    private function ensureContactMessagesTable($db) {
        $db->exec("
            CREATE TABLE IF NOT EXISTS contact_messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(150) NOT NULL,
                subject VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                status ENUM('new', 'read', 'archived') DEFAULT 'new',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        if (!$this->columnExists($db, 'contact_messages', 'branch_id')) {
            $db->exec("ALTER TABLE contact_messages ADD COLUMN branch_id INT NULL AFTER id");
            $db->exec("CREATE INDEX idx_contact_messages_branch_id ON contact_messages (branch_id)");
        }
    }

    private function defaultBranchId($db = null) {
        $db = $db ?: Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT id FROM branches WHERE is_active = 1 AND is_headquarters = 1 LIMIT 1");
        $branchId = $stmt->fetchColumn();

        if (!$branchId) {
            $stmt = $db->query("SELECT id FROM branches WHERE is_active = 1 ORDER BY id ASC LIMIT 1");
            $branchId = $stmt->fetchColumn();
        }

        return $branchId ?: null;
    }

    private function columnExists($db, $table, $column) {
        $stmt = $db->prepare("
            SELECT COUNT(*)
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
        ");
        $stmt->execute([$table, $column]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
