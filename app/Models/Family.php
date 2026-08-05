<?php
namespace App\Models;

use App\Core\BranchScope;
use App\Core\Model;

class Family extends Model {
    protected $table = 'families';
    protected $branchScoped = true;

    public function findAll() {
        [$where, $params] = BranchScope::where();
        $sql = "SELECT * FROM families";
        if ($where !== '') {
            $sql .= " WHERE $where";
        }
        $sql .= " ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getMembers($familyId) {
        $sql = "SELECT * FROM members WHERE family_id = ?";
        $params = [$familyId];
        [$where, $branchParams] = BranchScope::where();
        if ($where !== '') {
            $sql .= " AND $where";
            $params = array_merge($params, $branchParams);
        }
        $sql .= " ORDER BY CASE WHEN family_role = 'Head' THEN 1 WHEN family_role = 'Spouse' THEN 2 ELSE 3 END";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
