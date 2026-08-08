<?php
namespace App\Models;

use App\Core\BranchScope;
use App\Core\Model;

class Sermon extends Model {
    protected $table = 'sermons';
    protected $branchScoped = true;

    public function findAll() {
        [$where, $params] = BranchScope::where();
        $sql = "SELECT * FROM sermons";
        if ($where !== '') {
            $sql .= " WHERE $where";
        }
        $sql .= " ORDER BY date_preached DESC, created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findPaginated($limit, $offset) {
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        [$where, $params] = BranchScope::where();
        $sql = "SELECT * FROM sermons";
        if ($where !== '') {
            $sql .= " WHERE $where";
        }
        $sql .= " ORDER BY date_preached DESC, created_at DESC LIMIT $limit OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countAll() {
        [$where, $params] = BranchScope::where();
        $sql = "SELECT COUNT(*) FROM sermons";
        if ($where !== '') {
            $sql .= " WHERE $where";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function find($id) {
        $sql = "SELECT * FROM sermons WHERE id = ?";
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
