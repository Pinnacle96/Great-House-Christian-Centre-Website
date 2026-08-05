<?php
namespace App\Core;

class BranchScope {
    public const ROLE_SUPERADMIN = 1;
    public const ROLE_BRANCH_ADMIN = 7;
    public const ROLE_PASTOR = 2;

    public static function isSuperAdmin() {
        return (int)($_SESSION['role_id'] ?? 0) === self::ROLE_SUPERADMIN;
    }

    public static function currentBranchId() {
        $branchId = $_SESSION['branch_id'] ?? null;
        return $branchId ? (int)$branchId : null;
    }

    public static function userBranchId() {
        return self::currentBranchId();
    }

    public static function canAccess($branchId) {
        if (self::isSuperAdmin()) {
            return true;
        }

        $current = self::currentBranchId();
        return $current !== null && (int)$branchId === $current;
    }

    public static function requireAccess($branchId) {
        if (!self::canAccess($branchId)) {
            $_SESSION['error'] = 'Access denied for this branch.';
            header('Location: ' . APP_URL . '/admin');
            exit;
        }
    }

    public static function isHeadquartersBranchUser() {
        $branchId = self::currentBranchId();
        if (!$branchId) {
            return false;
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id FROM branches WHERE id = ? AND is_active = 1 AND is_headquarters = 1 LIMIT 1");
        $stmt->execute([$branchId]);
        return (bool)$stmt->fetchColumn();
    }

    public static function canManageGlobalFrontend() {
        if (self::isSuperAdmin()) {
            return true;
        }

        $roleId = (int)($_SESSION['role_id'] ?? 0);
        if (!in_array($roleId, [self::ROLE_PASTOR, self::ROLE_BRANCH_ADMIN], true)) {
            return false;
        }

        return self::isHeadquartersBranchUser();
    }

    public static function requireGlobalFrontendAccess() {
        if (!self::canManageGlobalFrontend()) {
            $_SESSION['error'] = 'Access denied. Only superadmins and the headquarters branch leadership can manage global website content.';
            header('Location: ' . APP_URL . '/admin');
            exit;
        }
    }

    public static function where($alias = '') {
        if (self::isSuperAdmin()) {
            return ['', []];
        }

        $branchId = self::currentBranchId();
        if (!$branchId) {
            return ['1 = 0', []];
        }

        $prefix = $alias !== '' ? rtrim($alias, '.') . '.' : '';
        return [$prefix . 'branch_id = ?', [$branchId]];
    }

    public static function appendWhere($sql, array $params = [], $alias = '') {
        [$where, $branchParams] = self::where($alias);
        if ($where === '') {
            return [$sql, $params];
        }

        $sql .= preg_match('/\bwhere\b/i', $sql) ? ' AND ' . $where : ' WHERE ' . $where;
        return [$sql, array_merge($params, $branchParams)];
    }

    public static function branchOptions($db) {
        if (self::isSuperAdmin()) {
            $stmt = $db->query("SELECT id, name FROM branches WHERE is_active = 1 ORDER BY name ASC");
            return $stmt->fetchAll();
        }

        $branchId = self::currentBranchId();
        if (!$branchId) {
            return [];
        }

        $stmt = $db->prepare("SELECT id, name FROM branches WHERE id = ? AND is_active = 1");
        $stmt->execute([$branchId]);
        return $stmt->fetchAll();
    }
}
