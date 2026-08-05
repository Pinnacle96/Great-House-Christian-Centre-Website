<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This seed script can only be run from the command line.');
}

require_once 'config/config.php';

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Database;

$accounts = [
    ['System Superadmin', 'admin@ghcc.org', 1],
    ['Pastor Demo', 'pastor@ghcc.org', 2],
    ['Leader Demo', 'leader@ghcc.org', 3],
    ['Member Demo', 'member@ghcc.org', 4],
    ['Registration Manager Demo', 'registration.manager@ghcc.org', 5],
    ['Registration Team Demo', 'registration.team@ghcc.org', 6],
    ['Branch Admin Demo', 'branch.admin@ghcc.org', 7],
];

$defaultPassword = 'password123';
$hash = password_hash($defaultPassword, PASSWORD_DEFAULT);

try {
    $db = Database::getInstance()->getConnection();
    $db->beginTransaction();

    $stmt = $db->prepare("
        INSERT INTO users (name, email, password, role_id)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            role_id = VALUES(role_id)
    ");

    foreach ($accounts as $account) {
        $stmt->execute([$account[0], $account[1], $hash, $account[2]]);
        echo "Seeded {$account[1]}\n";
    }

    $memberStmt = $db->prepare("
        INSERT INTO members (first_name, last_name, email, phone, status, membership_type, joined_at)
        VALUES ('Member', 'Demo', 'member@ghcc.org', '+2340000000000', 'active', 'Member', CURDATE())
        ON DUPLICATE KEY UPDATE
            first_name = VALUES(first_name),
            last_name = VALUES(last_name),
            phone = VALUES(phone),
            status = VALUES(status),
            membership_type = VALUES(membership_type)
    ");
    $memberStmt->execute();
    echo "Seeded linked member profile for member@ghcc.org\n";

    $db->commit();
    echo "\nDefault password for all sample accounts: {$defaultPassword}\n";
    echo "Change or remove these accounts before a live production launch.\n";
} catch (Throwable $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo "Seed failed: " . $e->getMessage() . "\n";
    exit(1);
}
