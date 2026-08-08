<?php
namespace App\Models;

use App\Core\BranchScope;
use App\Core\Model;

class PrayerRequest extends Model {
    protected $table = 'prayer_requests';
    protected $branchScoped = true;

    public function findAll() {
        [$where, $params] = BranchScope::where();
        $sql = "SELECT * FROM prayer_requests";
        if ($where !== '') {
            $sql .= " WHERE $where";
        }
        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findPaginated($limit, $offset) {
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        [$where, $params] = BranchScope::where();
        $sql = "SELECT * FROM prayer_requests";
        if ($where !== '') {
            $sql .= " WHERE $where";
        }
        $sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countAll() {
        [$where, $params] = BranchScope::where();
        $sql = "SELECT COUNT(*) FROM prayer_requests";
        if ($where !== '') {
            $sql .= " WHERE $where";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function stats() {
        [$where, $params] = BranchScope::where();
        $sql = "
            SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status = 'prayed' THEN 1 ELSE 0 END) as prayed,
                SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_requests,
                SUM(CASE WHEN is_public = 1 THEN 1 ELSE 0 END) as public_requests
            FROM prayer_requests
        ";
        if ($where !== '') {
            $sql .= " WHERE $where";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $stats = $stmt->fetch() ?: [];
        return [
            'total' => (int)($stats['total'] ?? 0),
            'prayed' => (int)($stats['prayed'] ?? 0),
            'new_requests' => (int)($stats['new_requests'] ?? 0),
            'public_requests' => (int)($stats['public_requests'] ?? 0),
        ];
    }

    public function find($id) {
        $sql = "SELECT * FROM prayer_requests WHERE id = ?";
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
