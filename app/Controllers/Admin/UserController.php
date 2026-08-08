<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;

class UserController extends Controller {
    
    private $db;
    
    public function __construct() {
        $this->requireAuth();
        $this->db = Database::getInstance();
    }

    public function index() {
        // User management is restricted to global superadmins.
        if ($_SESSION['role_id'] != 1) {
            $_SESSION['error'] = 'Access denied. Superadmin privileges required.';
            $this->redirect('/admin');
        }
        
        $conn = $this->db->getConnection();
        
        $pagination = $this->paginationParams(15);
        $totalUsers = (int)$conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $pagination = $this->paginationMeta($totalUsers, $pagination, 'users');

        // Get users with their role names
        $limit = (int)$pagination['per_page'];
        $offset = (int)$pagination['offset'];
        $stmt = $conn->query("
            SELECT u.*, r.name as role_name, b.name as branch_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            LEFT JOIN branches b ON b.id = u.branch_id
            ORDER BY u.created_at DESC
            LIMIT $limit OFFSET $offset
        ");
        $users = $stmt->fetchAll();
        
        // Get all roles for the create form
        $stmt = $conn->query("SELECT * FROM roles ORDER BY name");
        $roles = $stmt->fetchAll();
        
        $this->view('admin/users/index', [
            'title' => 'User Management',
            'users' => $users,
            'roles' => $roles,
            'pagination' => $pagination
        ]);
    }

    public function create() {
        if ($_SESSION['role_id'] != 1) {
            $_SESSION['error'] = 'Access denied. Superadmin privileges required.';
            $this->redirect('/admin');
        }
        
        $conn = $this->db->getConnection();
        $stmt = $conn->query("SELECT * FROM roles ORDER BY name");
        $roles = $stmt->fetchAll();
        $stmt = $conn->query("SELECT id, name FROM branches WHERE is_active = 1 ORDER BY name ASC");
        $branches = $stmt->fetchAll();
        
        $this->view('admin/users/create', [
            'title' => 'Create User',
            'roles' => $roles,
            'branches' => $branches
        ]);
    }

    public function store() {
        if ($_SESSION['role_id'] != 1) {
            $_SESSION['error'] = 'Access denied. Superadmin privileges required.';
            $this->redirect('/admin');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/users');
        }
        
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $role_id = (int)$_POST['role_id'];
        $phone = trim($_POST['phone'] ?? '');
        $branch_id = $role_id === 1 ? null : (int)($_POST['branch_id'] ?? 0);
        
        // Validation
        if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
            $_SESSION['error'] = 'All fields are required.';
            $this->redirect('/admin/users/create');
        }
        
        if ($password !== $confirm_password) {
            $_SESSION['error'] = 'Passwords do not match.';
            $this->redirect('/admin/users/create');
        }
        
        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Password must be at least 6 characters long.';
            $this->redirect('/admin/users/create');
        }

        if ($role_id !== 1 && $branch_id <= 0) {
            $_SESSION['error'] = 'Please assign this user to a branch.';
            $this->redirect('/admin/users/create');
        }
        
        $conn = $this->db->getConnection();
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Email already exists.';
            $this->redirect('/admin/users/create');
        }
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role_id, branch_id, phone) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashed_password, $role_id, $branch_id ?: null, $phone]);
        
        $_SESSION['success'] = 'User created successfully.';
        $this->redirect('/admin/users');
    }

    public function edit($id) {
        $conn = $this->db->getConnection();
        
        // Users can edit their own profile, admins can edit any
        if ($_SESSION['role_id'] != 1 && $_SESSION['user_id'] != $id) {
            $_SESSION['error'] = 'Access denied.';
            $this->redirect('/admin');
        }
        
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        
        if (!$user) {
            $_SESSION['error'] = 'User not found.';
            $this->redirect('/admin/users');
        }
        
        // Only admins can see roles dropdown
        $roles = [];
        if ($_SESSION['role_id'] == 1) {
            $stmt = $conn->query("SELECT * FROM roles ORDER BY name");
            $roles = $stmt->fetchAll();
        }

        $branches = [];
        if ($_SESSION['role_id'] == 1) {
            $stmt = $conn->query("SELECT id, name FROM branches WHERE is_active = 1 ORDER BY name ASC");
            $branches = $stmt->fetchAll();
        }
        
        $this->view('admin/users/edit', [
            'title' => 'Edit User',
            'user' => $user,
            'roles' => $roles,
            'branches' => $branches
        ]);
    }

    public function update($id) {
        $conn = $this->db->getConnection();
        
        // Check permissions
        if ($_SESSION['role_id'] != 1 && $_SESSION['user_id'] != $id) {
            $_SESSION['error'] = 'Access denied.';
            $this->redirect('/admin');
        }
        
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone'] ?? '');
        
        // Superadmins can change role, regular users cannot.
        $role_id = null;
        if ($_SESSION['role_id'] == 1) {
            $role_id = (int)$_POST['role_id'];
        }
        $branch_id = null;
        if ($_SESSION['role_id'] == 1) {
            $branch_id = $role_id === 1 ? null : (int)($_POST['branch_id'] ?? 0);
            if ($role_id !== 1 && $branch_id <= 0) {
                $_SESSION['error'] = 'Please assign this user to a branch.';
                $this->redirect("/admin/users/edit/$id");
            }
        }
        
        // Check if email exists for other users
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Email already exists.';
            $this->redirect("/admin/users/edit/$id");
        }
        
        // Update user
        if ($_SESSION['role_id'] == 1) {
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, role_id = ?, branch_id = ? WHERE id = ?");
            $stmt->execute([$name, $email, $phone, $role_id, $branch_id ?: null, $id]);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
            $stmt->execute([$name, $email, $phone, $id]);
        }
        
        $_SESSION['success'] = 'Profile updated successfully.';
        
        if ($_SESSION['role_id'] == 1) {
            $this->redirect('/admin/users');
        } else {
            $this->redirect('/admin');
        }
    }

    public function changePassword($id) {
        $conn = $this->db->getConnection();
        
        // Users can only change their own password
        if ($_SESSION['user_id'] != $id) {
            $_SESSION['error'] = 'Access denied.';
            $this->redirect('/admin');
        }
        
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validation
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $_SESSION['error'] = 'All fields are required.';
            $this->redirect("/admin/users/edit/$id");
        }
        
        if ($new_password !== $confirm_password) {
            $_SESSION['error'] = 'New passwords do not match.';
            $this->redirect("/admin/users/edit/$id");
        }
        
        if (strlen($new_password) < 6) {
            $_SESSION['error'] = 'New password must be at least 6 characters long.';
            $this->redirect("/admin/users/edit/$id");
        }
        
        // Verify current password
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        
        if (!password_verify($current_password, $user['password'])) {
            $_SESSION['error'] = 'Current password is incorrect.';
            $this->redirect("/admin/users/edit/$id");
        }
        
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $id]);
        
        $_SESSION['success'] = 'Password changed successfully.';
        $this->redirect('/admin');
    }

    public function delete($id) {
        if ($_SESSION['role_id'] != 1) {
            $_SESSION['error'] = 'Access denied. Superadmin privileges required.';
            $this->redirect('/admin');
        }
        
        // Prevent deleting own account
        if ($_SESSION['user_id'] == $id) {
            $_SESSION['error'] = 'Cannot delete your own account.';
            $this->redirect('/admin/users');
        }
        
        $conn = $this->db->getConnection();
        
        // Check if user exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            $_SESSION['error'] = 'User not found.';
            $this->redirect('/admin/users');
        }
        
        // Delete user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['success'] = 'User deleted successfully.';
        $this->redirect('/admin/users');
    }
}
