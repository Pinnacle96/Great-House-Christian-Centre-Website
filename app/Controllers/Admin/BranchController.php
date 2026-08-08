<?php
namespace App\Controllers\Admin;

use App\Core\BranchScope;
use App\Core\Controller;
use App\Core\Database;
use App\Models\Branch;
use App\Models\Setting;
use App\Services\PaystackService;
use App\Services\CommunicationService;

class BranchController extends Controller {
    private $db;

    public function __construct() {
        $this->requireRoles([1, 2, 7]);
        $this->db = Database::getInstance()->getConnection();
    }

    public function index() {
        if (BranchScope::isSuperAdmin()) {
            $stmt = $this->db->query("
                SELECT b.*, u.name as pastor_name,
                    (SELECT COUNT(*) FROM members m WHERE m.branch_id = b.id AND m.status = 'active') as active_members,
                    (SELECT COUNT(*) FROM users us WHERE us.branch_id = b.id) as assigned_users
                FROM branches b
                LEFT JOIN users u ON u.id = b.pastor_user_id
                ORDER BY b.name ASC
            ");
            $branches = $stmt->fetchAll();
        } else {
            $branchId = BranchScope::currentBranchId();
            $stmt = $this->db->prepare("
                SELECT b.*, u.name as pastor_name,
                    (SELECT COUNT(*) FROM members m WHERE m.branch_id = b.id AND m.status = 'active') as active_members,
                    (SELECT COUNT(*) FROM users us WHERE us.branch_id = b.id) as assigned_users
                FROM branches b
                LEFT JOIN users u ON u.id = b.pastor_user_id
                WHERE b.id = ?
            ");
            $stmt->execute([$branchId]);
            $branches = array_filter([$stmt->fetch()]);
        }

        $this->view('admin/branches/index', [
            'title' => 'Branches',
            'branches' => $branches,
            'isSuperAdmin' => BranchScope::isSuperAdmin()
        ]);
    }

    public function create() {
        if (!BranchScope::isSuperAdmin()) {
            $this->redirect('/admin/branches');
        }

        $pastors = $this->branchAssignablePastors();
        $this->view('admin/branches/create', [
            'title' => 'Create Branch',
            'pastors' => $pastors
        ]);
    }

    public function store() {
        if (!BranchScope::isSuperAdmin()) {
            $this->redirect('/admin/branches');
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            $_SESSION['error'] = 'Branch name is required.';
            $this->redirect('/admin/branches/create');
        }

        $slug = $this->uniqueSlug($name);
        $token = bin2hex(random_bytes(16));

        $stmt = $this->db->prepare("
            INSERT INTO branches (
                name, slug, registration_token, address, phone, email, pastor_name, pastor_user_id, is_active,
                paystack_public_key, paystack_secret_key, smtp_host, smtp_port, smtp_encryption, smtp_user, smtp_pass,
                bank_name, bank_account_name, bank_account_number
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $name,
            $slug,
            $token,
            trim($_POST['address'] ?? ''),
            trim($_POST['phone'] ?? ''),
            trim($_POST['email'] ?? ''),
            trim($_POST['pastor_name'] ?? ''),
            !empty($_POST['pastor_user_id']) ? (int)$_POST['pastor_user_id'] : null,
            isset($_POST['is_active']) ? 1 : 0,
            trim($_POST['paystack_public_key'] ?? ''),
            Setting::encryptValue(trim($_POST['paystack_secret_key'] ?? '')),
            trim($_POST['smtp_host'] ?? ''),
            !empty($_POST['smtp_port']) ? (int)$_POST['smtp_port'] : null,
            in_array($_POST['smtp_encryption'] ?? '', ['tls', 'ssl'], true) ? $_POST['smtp_encryption'] : null,
            trim($_POST['smtp_user'] ?? ''),
            Setting::encryptValue(trim($_POST['smtp_pass'] ?? '')),
            trim($_POST['bank_name'] ?? ''),
            trim($_POST['bank_account_name'] ?? ''),
            trim($_POST['bank_account_number'] ?? '')
        ]);

        $branchId = (int)$this->db->lastInsertId();
        if (!empty($_POST['pastor_user_id'])) {
            $stmt = $this->db->prepare("UPDATE users SET branch_id = ? WHERE id = ? AND role_id != 1");
            $stmt->execute([$branchId, (int)$_POST['pastor_user_id']]);
        }

        $_SESSION['success'] = 'Branch created successfully.';
        $this->redirect('/admin/branches');
    }

    public function edit($id) {
        if (!BranchScope::isSuperAdmin()) {
            BranchScope::requireAccess($id);
        }

        $branchModel = new Branch();
        $branch = $branchModel->find($id);
        if (!$branch) {
            $this->redirect('/admin/branches');
        }

        $pastors = BranchScope::isSuperAdmin() ? $this->branchAssignablePastors() : [];
        $this->view('admin/branches/edit', [
            'title' => 'Edit Branch',
            'branch' => $branch,
            'pastors' => $pastors,
            'isSuperAdmin' => BranchScope::isSuperAdmin()
        ]);
    }

    public function update($id) {
        if (!BranchScope::isSuperAdmin()) {
            BranchScope::requireAccess($id);
        }

        $branchModel = new Branch();
        $branch = $branchModel->find($id);
        if (!$branch) {
            $this->redirect('/admin/branches');
        }

        $data = [
            'name' => trim($_POST['name'] ?? $branch['name']),
            'address' => trim($_POST['address'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'pastor_name' => trim($_POST['pastor_name'] ?? ''),
            'paystack_public_key' => trim($_POST['paystack_public_key'] ?? ''),
            'smtp_host' => trim($_POST['smtp_host'] ?? ''),
            'smtp_port' => !empty($_POST['smtp_port']) ? (int)$_POST['smtp_port'] : null,
            'smtp_encryption' => in_array($_POST['smtp_encryption'] ?? '', ['tls', 'ssl'], true) ? $_POST['smtp_encryption'] : null,
            'smtp_user' => trim($_POST['smtp_user'] ?? ''),
            'bank_name' => trim($_POST['bank_name'] ?? ''),
            'bank_account_name' => trim($_POST['bank_account_name'] ?? ''),
            'bank_account_number' => trim($_POST['bank_account_number'] ?? ''),
        ];

        if (trim($_POST['paystack_secret_key'] ?? '') !== '') {
            $data['paystack_secret_key'] = Setting::encryptValue(trim($_POST['paystack_secret_key']));
        }

        if (trim($_POST['smtp_pass'] ?? '') !== '') {
            $data['smtp_pass'] = Setting::encryptValue(trim($_POST['smtp_pass']));
        }

        if (BranchScope::isSuperAdmin()) {
            $data['pastor_user_id'] = !empty($_POST['pastor_user_id']) ? (int)$_POST['pastor_user_id'] : null;
            $data['is_active'] = isset($_POST['is_active']) ? 1 : 0;
        }

        $branchModel->update($id, $data);
        if (BranchScope::isSuperAdmin() && !empty($data['pastor_user_id'])) {
            $stmt = $this->db->prepare("UPDATE users SET branch_id = ? WHERE id = ? AND role_id != 1");
            $stmt->execute([$id, $data['pastor_user_id']]);
        }

        $_SESSION['success'] = 'Branch updated successfully.';
        $this->redirect('/admin/branches');
    }

    public function testPaystack($id) {
        if (!BranchScope::isSuperAdmin()) {
            BranchScope::requireAccess($id);
        }

        $branchModel = new Branch();
        $config = $branchModel->paymentConfig($id);
        $service = new PaystackService($config['secret_key'] ?? '');
        $result = $service->testConnection();

        $_SESSION[($result && !empty($result->status)) ? 'success' : 'error'] =
            ($result && !empty($result->status))
                ? 'Branch Paystack connection verified successfully.'
                : 'Branch Paystack test failed: ' . ($result->message ?? 'Unknown error');

        $this->redirect('/admin/branches/edit/' . $id);
    }

    public function testEmail($id) {
        if (!BranchScope::isSuperAdmin()) {
            BranchScope::requireAccess($id);
        }

        $branchModel = new Branch();
        $branch = $branchModel->find($id);
        $to = $branch['email'] ?? '';
        if (!$to || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Set a valid branch email before testing SMTP.';
            $this->redirect('/admin/branches/edit/' . $id);
        }

        $service = new CommunicationService();
        $sent = $service->sendEmail(
            $to,
            'GHCC Branch SMTP Test',
            '<p>This is a test email from your GHCC branch configuration.</p>',
            $id
        );

        $_SESSION[$sent ? 'success' : 'error'] = $sent
            ? 'Branch SMTP test email sent successfully.'
            : 'Branch SMTP test failed. Check branch SMTP host, port, username, password, encryption, and logs.';

        $this->redirect('/admin/branches/edit/' . $id);
    }

    public function regenerateToken($id) {
        if (!BranchScope::isSuperAdmin()) {
            BranchScope::requireAccess($id);
        }

        $stmt = $this->db->prepare("UPDATE branches SET registration_token = ? WHERE id = ?");
        $stmt->execute([bin2hex(random_bytes(16)), $id]);

        $_SESSION['success'] = 'Branch registration link regenerated.';
        $this->redirect('/admin/branches');
    }

    public function makeHeadquarters($id) {
        if (!BranchScope::isSuperAdmin()) {
            $this->redirect('/admin/branches');
        }

        $branchModel = new Branch();
        $branch = $branchModel->find($id);
        if (!$branch || empty($branch['is_active'])) {
            $_SESSION['error'] = 'Only an active branch can be made headquarters.';
            $this->redirect('/admin/branches');
        }

        $this->db->beginTransaction();
        try {
            $this->db->exec("UPDATE branches SET is_headquarters = 0");
            $stmt = $this->db->prepare("UPDATE branches SET is_headquarters = 1 WHERE id = ?");
            $stmt->execute([$id]);
            $this->db->commit();
            $_SESSION['success'] = $branch['name'] . ' is now the headquarters branch.';
        } catch (\Throwable $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Could not update headquarters branch.';
        }

        $this->redirect('/admin/branches');
    }

    public function delete($id) {
        if (!BranchScope::isSuperAdmin()) {
            $this->redirect('/admin/branches');
        }

        $branchModel = new Branch();
        $branch = $branchModel->find($id);
        if (!$branch) {
            $_SESSION['error'] = 'Branch not found.';
            $this->redirect('/admin/branches');
        }

        if (!empty($branch['is_headquarters'])) {
            $_SESSION['error'] = 'The headquarters branch cannot be deleted. Make another branch headquarters first.';
            $this->redirect('/admin/branches');
        }

        if (!empty($branch['is_active']) && $this->activeBranchCount() <= 1) {
            $_SESSION['error'] = 'You must keep at least one active branch.';
            $this->redirect('/admin/branches');
        }

        $usageCount = $this->branchUsageCount((int)$id);
        if ($usageCount > 0) {
            $stmt = $this->db->prepare("UPDATE branches SET is_active = 0 WHERE id = ?");
            $stmt->execute([(int)$id]);
            $_SESSION['success'] = $branch['name'] . ' has existing records, so it was deactivated instead of permanently deleted.';
            $this->redirect('/admin/branches');
        }

        $stmt = $this->db->prepare("DELETE FROM branches WHERE id = ?");
        $stmt->execute([(int)$id]);

        $_SESSION['success'] = $branch['name'] . ' was permanently deleted.';
        $this->redirect('/admin/branches');
    }

    private function branchAssignablePastors() {
        $stmt = $this->db->query("
            SELECT u.id, u.name, u.email, b.name as branch_name
            FROM users u
            LEFT JOIN branches b ON b.id = u.branch_id
            WHERE u.role_id = 2
            ORDER BY COALESCE(b.name, 'Unassigned'), u.name ASC
        ");
        return $stmt->fetchAll();
    }

    private function activeBranchCount() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM branches WHERE is_active = 1");
        return (int)$stmt->fetchColumn();
    }

    private function branchUsageCount($branchId) {
        $tables = [
            'users',
            'members',
            'families',
            'events',
            'registrations',
            'event_registrations',
            'donations',
            'prayer_requests',
            'small_groups',
            'attendance',
            'communications',
            'communication_logs',
            'contact_messages',
            'sermons',
            'services',
        ];

        $count = 0;
        foreach ($tables as $table) {
            if (!$this->tableHasBranchId($table)) {
                continue;
            }

            $stmt = $this->db->prepare("SELECT COUNT(*) FROM `$table` WHERE branch_id = ?");
            $stmt->execute([$branchId]);
            $count += (int)$stmt->fetchColumn();
        }

        return $count;
    }

    private function tableHasBranchId($table) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = ?
                AND COLUMN_NAME = 'branch_id'
        ");
        $stmt->execute([$table]);
        return (int)$stmt->fetchColumn() > 0;
    }

    private function uniqueSlug($name) {
        $base = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-'));
        if ($base === '') {
            $base = 'branch';
        }

        $slug = $base;
        $i = 2;
        while (true) {
            $stmt = $this->db->prepare("SELECT id FROM branches WHERE slug = ? LIMIT 1");
            $stmt->execute([$slug]);
            if (!$stmt->fetch()) {
                return $slug;
            }
            $slug = $base . '-' . $i;
            $i++;
        }
    }
}
