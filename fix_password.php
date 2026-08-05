<?php
// Direct script to fix admin password

try {
    // Database configuration - update these if different in your config.php
    $host = 'localhost';
    $dbname = 'ghcc_db';
    $username = 'root';
    $password = '';
    
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // New password hash for 'password123'
    $newPasswordHash = '$2y$10$SDsOXY52DVF.NN595bFbH.IxjV0.IOkbU8ENwfmgcjbBqfdHbTBfu';
    
    // Update the admin user password
    $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE email = 'admin@ghcc.org'");
    $stmt->execute(['password' => $newPasswordHash]);
    
    if ($stmt->rowCount() > 0) {
        echo "✅ Password updated successfully for admin@ghcc.org\n";
        echo "New password: password123\n";
        
        // Verify the update
        $stmt = $pdo->prepare("SELECT email, password FROM users WHERE email = 'admin@ghcc.org'");
        $stmt->execute();
        $user = $stmt->fetch();
        
        echo "Current hash: " . $user['password'] . "\n";
        echo "You can now login with email: admin@ghcc.org and password: password123\n";
    } else {
        echo "❌ No rows were updated. The user admin@ghcc.org may not exist.\n";
        echo "You may need to run the database schema first.\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    echo "Please check your database configuration.\n";
}
?>