<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This setup script can only be run from the command line.');
}

require_once 'config/config.php';
require_once 'app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    $db->beginTransaction();

    $stmt = $db->prepare("SELECT id FROM roles WHERE name = ? LIMIT 1");
    $stmt->execute(['Superadmin']);
    $existingSuperadminId = $stmt->fetchColumn();

    if ($existingSuperadminId && (int)$existingSuperadminId !== 1) {
        $moveUsers = $db->prepare("UPDATE users SET role_id = 1 WHERE role_id = ?");
        $moveUsers->execute([(int)$existingSuperadminId]);

        $deleteDuplicate = $db->prepare("DELETE FROM roles WHERE id = ?");
        $deleteDuplicate->execute([(int)$existingSuperadminId]);
    }

    $stmt = $db->prepare("
        UPDATE roles
        SET name = 'Superadmin', description = 'Full access to all branches and system settings'
        WHERE id = 1
    ");
    $stmt->execute();

    $stmt = $db->prepare("UPDATE users SET name = 'System Superadmin' WHERE email = 'admin@ghcc.org' AND role_id = 1");
    $stmt->execute();

    $stmt = $db->prepare("SELECT id FROM roles WHERE name = ? LIMIT 1");
    $stmt->execute(['Admin']);
    $adminId = $stmt->fetchColumn();

    if (!$adminId) {
        $stmt = $db->prepare("
            INSERT INTO roles (id, name, description)
            VALUES (7, 'Admin', 'Branch-scoped administrative access')
        ");
        $stmt->execute();
    } elseif ((int)$adminId !== 7) {
        $targetExists = $db->prepare("SELECT id FROM roles WHERE id = 7 LIMIT 1");
        $targetExists->execute();

        if (!$targetExists->fetchColumn()) {
            $stmt = $db->prepare("UPDATE roles SET id = 7, description = 'Branch-scoped administrative access' WHERE id = ?");
            $stmt->execute([(int)$adminId]);
        } else {
            $stmt = $db->prepare("UPDATE roles SET name = 'Admin', description = 'Branch-scoped administrative access' WHERE id = 7");
            $stmt->execute();

            $moveUsers = $db->prepare("UPDATE users SET role_id = 7 WHERE role_id = ?");
            $moveUsers->execute([(int)$adminId]);

            $deleteDuplicate = $db->prepare("DELETE FROM roles WHERE id = ?");
            $deleteDuplicate->execute([(int)$adminId]);
        }
    } else {
        $stmt = $db->prepare("UPDATE roles SET description = 'Branch-scoped administrative access' WHERE id = 7");
        $stmt->execute();
    }

    $db->commit();

    echo "Roles migrated.\n";
    echo "Role 1: Superadmin (global)\n";
    echo "Role 7: Admin (branch-scoped)\n";
} catch (Throwable $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }

    echo "Role migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
