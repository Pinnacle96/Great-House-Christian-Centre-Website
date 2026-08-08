<?php
namespace App\Models;

use App\Core\BranchScope;
use App\Core\Model;

class Event extends Model {
    protected $table = 'events';
    protected $branchScoped = true;

    public function findAll() {
        [$where, $params] = BranchScope::where();
        $sql = "SELECT * FROM events";
        if ($where !== '') {
            $sql .= " WHERE $where";
        }
        $sql .= " ORDER BY start_datetime DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findPaginated($limit, $offset) {
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        [$where, $params] = BranchScope::where();
        $sql = "SELECT * FROM events";
        if ($where !== '') {
            $sql .= " WHERE $where";
        }
        $sql .= " ORDER BY start_datetime DESC LIMIT $limit OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countAll() {
        [$where, $params] = BranchScope::where();
        $sql = "SELECT COUNT(*) FROM events";
        if ($where !== '') {
            $sql .= " WHERE $where";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function stats() {
        [$where, $params] = BranchScope::where();
        $branchSql = $where !== '' ? "WHERE $where" : "";
        $stmt = $this->db->prepare("
            SELECT
                COUNT(*) as total,
                SUM(CASE WHEN start_datetime > NOW() THEN 1 ELSE 0 END) as upcoming,
                SUM(CASE WHEN requires_registration = 1 THEN 1 ELSE 0 END) as requires_registration
            FROM events
            $branchSql
        ");
        $stmt->execute($params);
        $stats = $stmt->fetch() ?: [];

        $registrationSql = "
            SELECT COUNT(*)
            FROM registrations r
            JOIN events e ON e.id = r.event_id
        ";
        [$registrationSql, $registrationParams] = BranchScope::appendWhere($registrationSql, [], 'e');
        $stmt = $this->db->prepare($registrationSql);
        $stmt->execute($registrationParams);

        return [
            'total' => (int)($stats['total'] ?? 0),
            'upcoming' => (int)($stats['upcoming'] ?? 0),
            'requires_registration' => (int)($stats['requires_registration'] ?? 0),
            'registrations' => (int)$stmt->fetchColumn(),
        ];
    }

    public function find($id) {
        $sql = "SELECT * FROM events WHERE id = ?";
        $params = [$id];
        [$where, $branchParams] = BranchScope::where();
        if ($where !== '') {
            $sql .= " AND $where";
            $params = array_merge($params, $branchParams);
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
}
