<?php
namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Security;
use App\Models\Branch;

class BranchRegistrationController extends Controller {
    public function show($token) {
        $branch = (new Branch())->findByToken($token);
        if (!$branch) {
            http_response_code(404);
            echo "Branch registration link not found.";
            exit;
        }

        $this->view('public/branch_member_registration', [
            'title' => 'Register with ' . $branch['name'],
            'branch' => $branch
        ]);
    }

    public function store($token) {
        if (!Security::rateLimit('branch_member_register:' . Security::clientIp(), 8, 600)) {
            $_SESSION['error'] = 'Too many registration attempts. Please wait a few minutes and try again.';
            header('Location: ' . APP_URL . '/b/' . $token . '/register');
            exit;
        }

        $branch = (new Branch())->findByToken($token);
        if (!$branch) {
            http_response_code(404);
            echo "Branch registration link not found.";
            exit;
        }

        $db = Database::getInstance()->getConnection();

        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $gender = in_array($_POST['gender'] ?? null, ['Male', 'Female'], true) ? $_POST['gender'] : null;
        $dob = $_POST['dob'] ?: null;
        $address = trim($_POST['address'] ?? '');
        $maritalStatus = in_array($_POST['marital_status'] ?? null, ['Single', 'Married', 'Divorced', 'Widowed'], true) ? $_POST['marital_status'] : null;
        $occupation = trim($_POST['occupation'] ?? '');
        $membershipType = 'Guest';
        $source = 'Branch QR Registration';

        if ($firstName === '' || $lastName === '' || $phone === '') {
            $_SESSION['error'] = 'First name, last name, and phone number are required.';
            header('Location: ' . APP_URL . '/b/' . $token . '/register');
            exit;
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Please enter a valid email address.';
            header('Location: ' . APP_URL . '/b/' . $token . '/register');
            exit;
        }

        if ($email !== '') {
            $stmt = $db->prepare("SELECT id FROM members WHERE branch_id = ? AND email = ? LIMIT 1");
            $stmt->execute([$branch['id'], $email]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'A member with this email already exists in this branch.';
                header('Location: ' . APP_URL . '/b/' . $token . '/register');
                exit;
            }
        }

        $stmt = $db->prepare("SELECT id FROM members WHERE branch_id = ? AND phone = ? LIMIT 1");
        $stmt->execute([$branch['id'], $phone]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'A member with this phone number already exists in this branch.';
            header('Location: ' . APP_URL . '/b/' . $token . '/register');
            exit;
        }

        $stmt = $db->prepare("
            INSERT INTO members (
                branch_id, first_name, last_name, email, phone, gender, dob, address,
                marital_status, occupation, membership_type, source, status, joined_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', CURDATE())
        ");
        $stmt->execute([
            $branch['id'],
            $firstName,
            $lastName,
            $email ?: null,
            $phone,
            $gender,
            $dob,
            $address,
            $maritalStatus,
            $occupation,
            $membershipType,
            $source
        ]);

        $_SESSION['success'] = 'Registration received successfully. Welcome to ' . $branch['name'] . '.';
        header('Location: ' . APP_URL . '/b/' . $token . '/register');
        exit;
    }
}
