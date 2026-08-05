<?php
require_once 'config/config.php';
require_once 'app/Core/Database.php';

use App\Core\Database;

function columnExists(PDO $db, $table, $column) {
    $stmt = $db->prepare("
        SELECT COUNT(*) 
        FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
    ");
    $stmt->execute([$table, $column]);
    return (int)$stmt->fetchColumn() > 0;
}

function tableExists(PDO $db, $table) {
    $stmt = $db->prepare("
        SELECT COUNT(*) 
        FROM information_schema.TABLES 
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
    ");
    $stmt->execute([$table]);
    return (int)$stmt->fetchColumn() > 0;
}

function indexExists(PDO $db, $table, $index) {
    $stmt = $db->prepare("
        SELECT COUNT(*) 
        FROM information_schema.STATISTICS 
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?
    ");
    $stmt->execute([$table, $index]);
    return (int)$stmt->fetchColumn() > 0;
}

function addColumn(PDO $db, $table, $column, $definition) {
    if (!tableExists($db, $table) || columnExists($db, $table, $column)) {
        return;
    }
    $db->exec("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
    echo "Added $table.$column\n";
}

function addIndex(PDO $db, $table, $index, $columns) {
    if (!tableExists($db, $table) || indexExists($db, $table, $index)) {
        return;
    }
    $db->exec("ALTER TABLE `$table` ADD INDEX `$index` ($columns)");
    echo "Added index $index on $table\n";
}

function addUniqueIndex(PDO $db, $table, $index, $columns) {
    if (!tableExists($db, $table) || indexExists($db, $table, $index)) {
        return;
    }
    $db->exec("ALTER TABLE `$table` ADD UNIQUE INDEX `$index` ($columns)");
    echo "Added unique index $index on $table\n";
}

function dropIndex(PDO $db, $table, $index) {
    if (!tableExists($db, $table) || !indexExists($db, $table, $index)) {
        return;
    }
    $db->exec("ALTER TABLE `$table` DROP INDEX `$index`");
    echo "Dropped index $index on $table\n";
}

try {
    $db = Database::getInstance()->getConnection();
    echo "Connected to database.\n";

    $db->exec("
        CREATE TABLE IF NOT EXISTS branches (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            slug VARCHAR(160) NOT NULL UNIQUE,
            registration_token VARCHAR(64) NOT NULL UNIQUE,
            address TEXT NULL,
            phone VARCHAR(30) NULL,
            email VARCHAR(150) NULL,
            pastor_user_id INT NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_branch_active (is_active),
            INDEX idx_branch_token (registration_token)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "Ensured branches table.\n";

    $stmt = $db->prepare("SELECT id FROM branches WHERE slug IN (?, ?) ORDER BY id ASC LIMIT 1");
    $stmt->execute(['ghcc-ibadan', 'headquarters']);
    $hqId = $stmt->fetchColumn();

    if (!$hqId) {
        $token = bin2hex(random_bytes(16));
        $stmt = $db->prepare("
            INSERT INTO branches (name, slug, registration_token, address, email, is_active)
            VALUES (?, ?, ?, ?, ?, 1)
        ");
        $stmt->execute(['GHCC Ibadan', 'ghcc-ibadan', $token, '', '']);
        $hqId = $db->lastInsertId();
        echo "Created GHCC Ibadan branch.\n";
    }

    $branchTables = [
        'users' => 'INT NULL AFTER role_id',
        'members' => 'INT NULL AFTER id',
        'families' => 'INT NULL AFTER id',
        'events' => 'INT NULL AFTER id',
        'registrations' => 'INT NULL AFTER event_id',
        'donations' => 'INT NULL AFTER id',
        'prayer_requests' => 'INT NULL AFTER id',
        'small_groups' => 'INT NULL AFTER id',
        'attendance' => 'INT NULL AFTER id',
        'communications' => 'INT NULL AFTER id',
        'communication_logs' => 'INT NULL AFTER id',
        'contact_messages' => 'INT NULL AFTER id',
        'sermons' => 'INT NULL AFTER id',
        'services' => 'INT NULL AFTER id',
    ];

    foreach ($branchTables as $table => $definition) {
        addColumn($db, $table, 'branch_id', $definition);
        if (tableExists($db, $table) && columnExists($db, $table, 'branch_id')) {
            $stmt = $db->prepare("UPDATE `$table` SET branch_id = ? WHERE branch_id IS NULL");
            $stmt->execute([$hqId]);
            addIndex($db, $table, 'idx_' . $table . '_branch_id', '`branch_id`');
        }
    }

    $branchColumns = [
        'pastor_name' => 'VARCHAR(150) NULL AFTER email',
        'is_headquarters' => 'TINYINT(1) NOT NULL DEFAULT 0 AFTER is_active',
        'paystack_public_key' => 'VARCHAR(255) NULL AFTER pastor_user_id',
        'paystack_secret_key' => 'TEXT NULL AFTER paystack_public_key',
        'smtp_host' => 'VARCHAR(255) NULL AFTER paystack_secret_key',
        'smtp_port' => 'INT NULL AFTER smtp_host',
        'smtp_encryption' => "VARCHAR(10) NULL AFTER smtp_port",
        'smtp_user' => 'VARCHAR(255) NULL AFTER smtp_encryption',
        'smtp_pass' => 'TEXT NULL AFTER smtp_user',
        'bank_name' => 'VARCHAR(150) NULL AFTER smtp_pass',
        'bank_account_name' => 'VARCHAR(150) NULL AFTER bank_name',
        'bank_account_number' => 'VARCHAR(50) NULL AFTER bank_account_name',
    ];

    foreach ($branchColumns as $column => $definition) {
        addColumn($db, 'branches', $column, $definition);
    }

    $centres = [
        ['GHCC Ibadan', 'ghcc-ibadan', 'Dr. Bibiloni Ademusi', '0816 946 4676', 'The Fulfilment Place, Therben Filling Station, Opposite Lead City University, Tollgate, Ibadan, Oyo State'],
        ['GHCC Ikeja', 'ghcc-ikeja', 'Mr. Abraham', '0810 233 8517', 'The Fulfilment Place, 3 Toyin Street, Ikeja, Lagos'],
        ['GHCC Lekki', 'ghcc-lekki', 'Mrs. Adenike Ige', '0814 884 7777', 'The Fulfilment Place, End of Shoprite Monastery Road, Amode Area, Lekki, Lagos'],
        ['GHCC Ile-Ife', 'ghcc-ile-ife', 'Pastor Mrs. Abiola Oriade', '0703 124 3988', 'The Fulfilment Place, 3rd Hall Mayfair Hotel, Mayfair, Ile-Ife, Osun State'],
        ['GHCC Osogbo', 'ghcc-osogbo', 'Pastor Dayo Jubee', '0901 862 1110', 'The Fulfilment Place, NUJ Hall beside Technical College, Osogbo, Osun State'],
        ['GHCC Potters Assembly', 'ghcc-potters-assembly', 'Pastor Favour', '0704 771 3817', 'The Fulfilment Place, Ajigbade Junction, Ido-Ijesa, along University of Ilesa, Osun State'],
        ['GHCC Ilesa', 'ghcc-ilesa', 'Pastor Peter Okon', '0811 417 3016', 'The Fulfilment Place, 7 Raimi Omole Street, Imo, Ilesa, Osun State'],
    ];

    foreach ($centres as $index => $centre) {
        [$name, $slug, $pastorName, $phone, $address] = $centre;
        if ($index === 0) {
            $stmt = $db->prepare("
                UPDATE branches
                SET name = ?, slug = ?, pastor_name = ?, phone = ?, address = ?, is_active = 1, is_headquarters = 1
                WHERE id = ?
            ");
            $stmt->execute([$name, $slug, $pastorName, $phone, $address, $hqId]);
            continue;
        }

        $stmt = $db->prepare("SELECT id FROM branches WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        $existingId = $stmt->fetchColumn();

        if ($existingId) {
            $stmt = $db->prepare("UPDATE branches SET name = ?, pastor_name = ?, phone = ?, address = ?, is_active = 1 WHERE id = ?");
            $stmt->execute([$name, $pastorName, $phone, $address, $existingId]);
        } else {
            $stmt = $db->prepare("
                INSERT INTO branches (name, slug, registration_token, pastor_name, phone, address, is_active)
                VALUES (?, ?, ?, ?, ?, ?, 1)
            ");
            $stmt->execute([$name, $slug, bin2hex(random_bytes(16)), $pastorName, $phone, $address]);
        }
    }

    $stmt = $db->prepare("UPDATE branches SET is_active = 0 WHERE slug = ? AND name = ?");
    $stmt->execute(['lagos-branch', 'Lagos Branch']);

    $stmt = $db->query("SELECT id FROM branches WHERE is_headquarters = 1 AND is_active = 1 ORDER BY id ASC LIMIT 1");
    $headquartersId = $stmt->fetchColumn();
    if (!$headquartersId) {
        $headquartersId = $hqId;
    }
    $stmt = $db->prepare("UPDATE branches SET is_headquarters = CASE WHEN id = ? THEN 1 ELSE 0 END");
    $stmt->execute([$headquartersId]);

    if (tableExists($db, 'users') && columnExists($db, 'users', 'branch_id')) {
        $stmt = $db->prepare("UPDATE users SET branch_id = NULL WHERE role_id = 1");
        $stmt->execute();
    }

    if (tableExists($db, 'members') && columnExists($db, 'members', 'branch_id')) {
        dropIndex($db, 'members', 'email');
        addUniqueIndex($db, 'members', 'uniq_members_branch_email', '`branch_id`, `email`');
    }

    echo "Multibranch schema is ready.\n";
    echo "Default branch ID: $hqId\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
