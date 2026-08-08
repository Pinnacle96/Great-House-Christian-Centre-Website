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
    $stmt = $db->prepare("SELECT id FROM branches WHERE slug = ? AND is_active = 1 LIMIT 1");
    $stmt->execute(['ghcc-ilesa']);
    $ilesaId = $stmt->fetchColumn();

    if (!$ilesaId) {
        throw new RuntimeException('GHCC Ilesa branch was not found or is inactive.');
    }

    $db->beginTransaction();
    $db->exec("UPDATE branches SET is_headquarters = 0");
    $stmt = $db->prepare("UPDATE branches SET is_headquarters = 1 WHERE id = ?");
    $stmt->execute([(int)$ilesaId]);

    $stmt = $db->prepare("SELECT id FROM branches WHERE slug = ? LIMIT 1");
    $stmt->execute(['ghcc-ibadan']);
    $ibadanId = $stmt->fetchColumn();

    if ($ibadanId) {
        $stmt = $db->prepare("SELECT COUNT(*) FROM events WHERE branch_id = ?");
        $stmt->execute([(int)$ilesaId]);
        $ilesaEvents = (int)$stmt->fetchColumn();

        if ($ilesaEvents === 0) {
            $stmt = $db->prepare("
                UPDATE events
                SET branch_id = ?
                WHERE branch_id = ?
                    AND title IN ('Kingdom Summit 2026', 'Night of Encounters', 'Christmas Celebration')
            ");
            $stmt->execute([(int)$ilesaId, (int)$ibadanId]);
            echo "Moved legacy public seed events to GHCC Ilesa.\n";
        }
    }

    $db->commit();

    echo "GHCC Ilesa is now the headquarters branch.\n";
} catch (Throwable $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }

    echo "Failed to set headquarters: " . $e->getMessage() . "\n";
    exit(1);
}
