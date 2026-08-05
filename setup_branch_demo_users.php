<?php
require_once 'config/config.php';
require_once 'app/Core/Database.php';

use App\Core\Database;

function roleId(PDO $db, $name) {
    $stmt = $db->prepare("SELECT id FROM roles WHERE name = ? LIMIT 1");
    $stmt->execute([$name]);
    return (int)$stmt->fetchColumn();
}

function upsertUser(PDO $db, $name, $email, $passwordHash, $roleId, $branchId, $phone = null) {
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $id = $stmt->fetchColumn();

    if ($id) {
        $stmt = $db->prepare("UPDATE users SET name = ?, role_id = ?, branch_id = ?, phone = ? WHERE id = ?");
        $stmt->execute([$name, $roleId, $branchId, $phone, $id]);
        return (int)$id;
    }

    $stmt = $db->prepare("
        INSERT INTO users (name, email, password, role_id, branch_id, phone)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$name, $email, $passwordHash, $roleId, $branchId, $phone]);
    return (int)$db->lastInsertId();
}

function upsertMember(PDO $db, $branchId, $firstName, $lastName, $email, $phone) {
    $stmt = $db->prepare("SELECT id FROM members WHERE branch_id = ? AND email = ? LIMIT 1");
    $stmt->execute([$branchId, $email]);
    $id = $stmt->fetchColumn();

    if ($id) {
        $stmt = $db->prepare("
            UPDATE members
            SET first_name = ?, last_name = ?, phone = ?, status = 'active', membership_type = 'Member'
            WHERE id = ?
        ");
        $stmt->execute([$firstName, $lastName, $phone, $id]);
        return;
    }

    $stmt = $db->prepare("
        INSERT INTO members (
            branch_id, first_name, last_name, email, phone, status, membership_type, source, joined_at
        ) VALUES (?, ?, ?, ?, ?, 'active', 'Member', 'Demo Seed', CURDATE())
    ");
    $stmt->execute([$branchId, $firstName, $lastName, $email, $phone]);
}

try {
    $db = Database::getInstance()->getConnection();
    $password = 'Demo@12345';
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $roles = [
        'superadmin' => roleId($db, 'Superadmin'),
        'admin' => roleId($db, 'Admin'),
        'pastor' => roleId($db, 'Pastor'),
        'leader' => roleId($db, 'Department Leader'),
        'member' => roleId($db, 'Member'),
        'registration-manager' => roleId($db, 'Registration Manager'),
        'registration-team' => roleId($db, 'Registration Team'),
    ];

    foreach ($roles as $key => $id) {
        if (!$id) {
            throw new RuntimeException("Missing role: $key");
        }
    }

    upsertUser(
        $db,
        'Demo Super Admin',
        'demo.superadmin@ghcc.local',
        $passwordHash,
        $roles['superadmin'],
        null,
        null
    );

    $branches = $db->query("
        SELECT id, name, slug, phone
        FROM branches
        WHERE is_active = 1
        ORDER BY name ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $branchRoles = [
        'admin' => ['label' => 'Branch Admin', 'role_id' => $roles['admin']],
        'pastor' => ['label' => 'Resident Pastor', 'role_id' => $roles['pastor']],
        'leader' => ['label' => 'Department Leader', 'role_id' => $roles['leader']],
        'member' => ['label' => 'Member', 'role_id' => $roles['member']],
        'registration-manager' => ['label' => 'Registration Manager', 'role_id' => $roles['registration-manager']],
        'registration-team' => ['label' => 'Registration Team', 'role_id' => $roles['registration-team']],
    ];

    foreach ($branches as $index => $branch) {
        foreach ($branchRoles as $roleKey => $role) {
            $name = 'Demo ' . $role['label'] . ' - ' . $branch['name'];
            $email = 'demo.' . $branch['slug'] . '.' . $roleKey . '@ghcc.local';
            $phone = '0800' . str_pad((string)($index + 1), 3, '0', STR_PAD_LEFT) . str_pad((string)array_search($roleKey, array_keys($branchRoles), true), 3, '0', STR_PAD_LEFT);

            upsertUser($db, $name, $email, $passwordHash, $role['role_id'], (int)$branch['id'], $phone);

            if ($roleKey === 'member') {
                upsertMember($db, (int)$branch['id'], 'Demo', $branch['name'] . ' Member', $email, $phone);
            }
        }
    }

    echo "Branch demo users seeded.\n";
    echo "Password: $password\n";
    echo "Superadmin: demo.superadmin@ghcc.local\n";
    foreach ($branches as $branch) {
        echo $branch['name'] . ": demo." . $branch['slug'] . ".{admin|pastor|leader|member|registration-manager|registration-team}@ghcc.local\n";
    }
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
