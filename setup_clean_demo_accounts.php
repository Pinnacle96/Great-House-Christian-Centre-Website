<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This setup script can only be run from the command line.');
}

require_once 'config/config.php';
require_once 'app/Core/Database.php';

use App\Core\Database;

function roleId(PDO $db, $name) {
    $stmt = $db->prepare("SELECT id FROM roles WHERE name = ? LIMIT 1");
    $stmt->execute([$name]);
    return (int)$stmt->fetchColumn();
}

function branchId(PDO $db, $slug) {
    $stmt = $db->prepare("SELECT id FROM branches WHERE slug = ? AND is_active = 1 LIMIT 1");
    $stmt->execute([$slug]);
    return (int)$stmt->fetchColumn();
}

function upsertDemoUser(PDO $db, $name, $email, $passwordHash, $roleId, $branchId = null, $phone = null) {
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $id = $stmt->fetchColumn();

    if ($id) {
        $stmt = $db->prepare("UPDATE users SET name = ?, password = ?, role_id = ?, branch_id = ?, phone = ? WHERE id = ?");
        $stmt->execute([$name, $passwordHash, $roleId, $branchId, $phone, $id]);
        return (int)$id;
    }

    $stmt = $db->prepare("
        INSERT INTO users (name, email, password, role_id, branch_id, phone)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$name, $email, $passwordHash, $roleId, $branchId, $phone]);
    return (int)$db->lastInsertId();
}

function upsertDemoMember(PDO $db, $branchId, $email, $phone) {
    $stmt = $db->prepare("SELECT id FROM members WHERE branch_id = ? AND email = ? LIMIT 1");
    $stmt->execute([$branchId, $email]);
    $id = $stmt->fetchColumn();

    if ($id) {
        $stmt = $db->prepare("
            UPDATE members
            SET first_name = 'Demo', last_name = 'Member', phone = ?, status = 'active', membership_type = 'Member', source = 'Demo Seed'
            WHERE id = ?
        ");
        $stmt->execute([$phone, $id]);
        return;
    }

    $stmt = $db->prepare("
        INSERT INTO members (branch_id, first_name, last_name, email, phone, status, membership_type, source, joined_at)
        VALUES (?, 'Demo', 'Member', ?, ?, 'active', 'Member', 'Demo Seed', CURDATE())
    ");
    $stmt->execute([$branchId, $email, $phone]);
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
        'registration_manager' => roleId($db, 'Registration Manager'),
        'registration_team' => roleId($db, 'Registration Team'),
    ];

    foreach ($roles as $key => $id) {
        if (!$id) {
            throw new RuntimeException("Missing role: $key");
        }
    }

    $db->beginTransaction();

    $db->exec("
        DELETE FROM members
        WHERE email LIKE 'demo.%@ghcc.local'
            OR email IN ('member@ghcc.org')
    ");

    $db->exec("
        DELETE FROM users
        WHERE email LIKE 'demo.%@ghcc.local'
            OR email IN (
                'pastor@ghcc.org',
                'leader@ghcc.org',
                'member@ghcc.org',
                'registration.manager@ghcc.org',
                'registration.team@ghcc.org',
                'regmanager@ghcc.org',
                'regteam@ghcc.org'
            )
    ");

    $ilesaId = branchId($db, 'ghcc-ilesa');
    if (!$ilesaId) {
        throw new RuntimeException('GHCC Ilesa branch was not found.');
    }

    upsertDemoUser($db, 'Demo Superadmin', 'superadmin@ghccng.org', $passwordHash, $roles['superadmin']);
    upsertDemoUser($db, 'Ilesa Branch Admin', 'admin.ilesa@ghccng.org', $passwordHash, $roles['admin'], $ilesaId, '08114173016');
    upsertDemoUser($db, 'Ilesa Department Leader', 'leader.ilesa@ghccng.org', $passwordHash, $roles['leader'], $ilesaId, '08114173016');
    upsertDemoUser($db, 'Ilesa Member', 'member.ilesa@ghccng.org', $passwordHash, $roles['member'], $ilesaId, '08114173016');
    upsertDemoUser($db, 'Ilesa Registration Manager', 'registrations.ilesa@ghccng.org', $passwordHash, $roles['registration_manager'], $ilesaId, '08114173016');
    upsertDemoUser($db, 'Ilesa Check-in Team', 'checkin.ilesa@ghccng.org', $passwordHash, $roles['registration_team'], $ilesaId, '08114173016');
    upsertDemoMember($db, $ilesaId, 'member.ilesa@ghccng.org', '08114173016');

    $branchPastors = [
        'ghcc-ibadan' => ['Dr. Bibiloni Ademusi', 'pastor.ibadan@ghccng.org', '08169464676'],
        'ghcc-ikeja' => ['Mr. Abraham', 'pastor.ikeja@ghccng.org', '08102338517'],
        'ghcc-lekki' => ['Mrs. Adenike Ige', 'pastor.lekki@ghccng.org', '08148847777'],
        'ghcc-ile-ife' => ['Pastor Mrs. Abiola Oriade', 'pastor.ileife@ghccng.org', '07031243988'],
        'ghcc-osogbo' => ['Pastor Dayo Jubee', 'pastor.osogbo@ghccng.org', '09018621110'],
        'ghcc-potters-assembly' => ['Pastor Favour', 'pastor.potters@ghccng.org', '07047713817'],
        'ghcc-ilesa' => ['Pastor Peter Okon', 'pastor.ilesa@ghccng.org', '08114173016'],
    ];

    $updateBranchPastor = $db->prepare("UPDATE branches SET pastor_user_id = ? WHERE id = ?");
    foreach ($branchPastors as $slug => [$name, $email, $phone]) {
        $branchId = branchId($db, $slug);
        if ($branchId) {
            $pastorId = upsertDemoUser($db, $name, $email, $passwordHash, $roles['pastor'], $branchId, $phone);
            $updateBranchPastor->execute([$pastorId, $branchId]);
        }
    }

    $branchAdmins = [
        'ghcc-ibadan' => ['Ibadan Branch Admin', 'admin.ibadan@ghccng.org'],
        'ghcc-ikeja' => ['Ikeja Branch Admin', 'admin.ikeja@ghccng.org'],
        'ghcc-lekki' => ['Lekki Branch Admin', 'admin.lekki@ghccng.org'],
        'ghcc-ile-ife' => ['Ile-Ife Branch Admin', 'admin.ileife@ghccng.org'],
        'ghcc-osogbo' => ['Osogbo Branch Admin', 'admin.osogbo@ghccng.org'],
        'ghcc-potters-assembly' => ['Potters Assembly Admin', 'admin.potters@ghccng.org'],
    ];

    foreach ($branchAdmins as $slug => [$name, $email]) {
        $branchId = branchId($db, $slug);
        if ($branchId) {
            upsertDemoUser($db, $name, $email, $passwordHash, $roles['admin'], $branchId);
        }
    }

    $db->commit();

    echo "Clean demo accounts are ready.\n";
    echo "Password for demo accounts: $password\n";
    echo "Core accounts:\n";
    echo "superadmin@ghccng.org\n";
    echo "admin.ilesa@ghccng.org\n";
    echo "pastor.ilesa@ghccng.org\n";
    echo "leader.ilesa@ghccng.org\n";
    echo "member.ilesa@ghccng.org\n";
    echo "registrations.ilesa@ghccng.org\n";
    echo "checkin.ilesa@ghccng.org\n";
    echo "Branch pastor samples: pastor.ibadan@ghccng.org, pastor.ikeja@ghccng.org, pastor.lekki@ghccng.org, pastor.ileife@ghccng.org, pastor.osogbo@ghccng.org, pastor.potters@ghccng.org, pastor.ilesa@ghccng.org\n";
    echo "Branch admin samples: admin.ibadan@ghccng.org, admin.ikeja@ghccng.org, admin.lekki@ghccng.org, admin.ileife@ghccng.org, admin.osogbo@ghccng.org, admin.potters@ghccng.org\n";
} catch (Throwable $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }

    echo "Failed to prepare clean demo accounts: " . $e->getMessage() . "\n";
    exit(1);
}
