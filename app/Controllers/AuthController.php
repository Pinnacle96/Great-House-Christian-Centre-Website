<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Security;

class AuthController extends Controller {
    
    public function loginForm() {
        // Only redirect to admin if user is already logged in AND trying to access login page
        // But allow access to login page for everyone (including logged-in users who want to log out)
        $this->view('auth/login', ['title' => 'Login']);
    }

    public function login() {
        if (!Security::rateLimit('login:' . Security::clientIp(), 5, 300)) {
            $this->view('auth/login', ['error' => 'Too many login attempts. Please wait a few minutes and try again.', 'title' => 'Login']);
            return;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $this->view('auth/login', ['error' => 'Please fill in all fields', 'title' => 'Login']);
            return;
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['branch_id'] = $user['branch_id'] ?? null;
            
            // Fetch role name
            $stmtRole = $db->prepare("SELECT name FROM roles WHERE id = :id");
            $stmtRole->execute(['id' => $user['role_id']]);
            $role = $stmtRole->fetch();
            $_SESSION['role_name'] = $role ? $role['name'] : 'Member';

            $_SESSION['branch_name'] = 'All Branches';
            if (!empty($user['branch_id'])) {
                $stmtBranch = $db->prepare("SELECT name FROM branches WHERE id = :id");
                $stmtBranch->execute(['id' => $user['branch_id']]);
                $branch = $stmtBranch->fetch();
                $_SESSION['branch_name'] = $branch ? $branch['name'] : 'Assigned Branch';
            }

            $this->redirect((int)$user['role_id'] === 4 ? '/member' : '/admin');
        } else {
            $this->view('auth/login', ['error' => 'Invalid credentials', 'title' => 'Login']);
        }
    }

    public function logout() {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        $this->redirect('/login');
    }
}
