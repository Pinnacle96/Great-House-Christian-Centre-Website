<?php
namespace App\Controllers\Admin;

use App\Core\BranchScope;
use App\Core\Controller;
use App\Core\Database;

class ContactMessageController extends Controller {

    public function __construct() {
        $this->requireRoles([1, 2, 3, 7]);
    }

    public function index() {
        $db = Database::getInstance()->getConnection();
        $filter = $_GET['status'] ?? 'all';

        $sql = "SELECT * FROM contact_messages";
        $params = [];
        if (in_array($filter, ['new', 'read', 'archived'], true)) {
            $sql .= " WHERE status = ?";
            $params[] = $filter;
        }
        [$sql, $params] = BranchScope::appendWhere($sql, $params);
        $sql .= " ORDER BY created_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $messages = $stmt->fetchAll();

        $this->view('admin/contact_messages/index', [
            'title' => 'Contact Messages',
            'messages' => $messages,
            'filter' => $filter
        ]);
    }

    public function markRead($id) {
        $this->updateStatus($id, 'read');
        $_SESSION['success'] = 'Message marked as read.';
        $this->redirect('/admin/contact-messages');
    }

    public function archive($id) {
        $this->updateStatus($id, 'archived');
        $_SESSION['success'] = 'Message archived.';
        $this->redirect('/admin/contact-messages');
    }

    public function delete($id) {
        $db = Database::getInstance()->getConnection();
        $sql = "DELETE FROM contact_messages WHERE id = ?";
        $params = [$id];
        [$where, $branchParams] = BranchScope::where();
        if ($where !== '') {
            $sql .= " AND $where";
            $params = array_merge($params, $branchParams);
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $_SESSION['success'] = 'Message deleted.';
        $this->redirect('/admin/contact-messages');
    }

    private function updateStatus($id, $status) {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE contact_messages SET status = ? WHERE id = ?";
        $params = [$status, $id];
        [$where, $branchParams] = BranchScope::where();
        if ($where !== '') {
            $sql .= " AND $where";
            $params = array_merge($params, $branchParams);
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    }
}
