<?php
// Script to update admin password
require_once 'config/config.php';
require_once 'app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance()->getConnection();

// New password hash for 'password123'
$newPasswordHash = '$2y$10$SDsOXY52DVF.NN595bFbH.IxjV0.IOkbU8ENwfmgcjbBqfdHbTBfu';

// Update the admin user password
$stmt = $db->prepare("UPDATE users SET password = :password WHERE email = 'admin@ghcc.org'");
$stmt->execute(['password' => $newPasswordHash]);

if ($stmt->rowCount() > 0) {
    echo "✅ Password updated successfully for admin@ghcc.org\n";
    echo "New password: password123\n";
} else {
    echo "❌ No rows were updated. Check if the user exists.\n";
}

// Verify the update
$stmt = $db->prepare("SELECT email, password FROM users WHERE email = 'admin@ghcc.org'");
$stmt->execute();
$user = $stmt->fetch();

echo "Current hash: " . $user['password'] . "\n";
?>