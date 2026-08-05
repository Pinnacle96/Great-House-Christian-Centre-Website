<?php
namespace App\Models;

use App\Core\BranchScope;
use App\Core\Model;

class Group extends Model {
    protected $table = 'small_groups';
    protected $branchScoped = true;

    public function findAll() {
        [$where, $params] = BranchScope::where();
        $sql = "SELECT * FROM small_groups";
        if ($where !== '') {
            $sql .= " WHERE $where";
        }
        $sql .= " ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($id) {
        $sql = "SELECT * FROM small_groups WHERE id = ?";
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

    public function where($column, $value) {
        [$where, $params] = BranchScope::where();
        $sql = "SELECT * FROM small_groups WHERE {$column} = ?";
        $queryParams = [$value];
        if ($where !== '') {
            $sql .= " AND $where";
            $queryParams = array_merge($queryParams, $params);
        }
        $sql .= " ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($queryParams);
        return $stmt->fetchAll();
    }

    public function findAllWithLeader() {
        $sql = "
            SELECT g.*, u.name as leader_name 
            FROM small_groups g 
            LEFT JOIN users u ON g.leader_id = u.id
        ";
        [$sql, $params] = BranchScope::appendWhere($sql, [], 'g');
        $sql .= " ORDER BY g.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findWithLeader($id) {
        $sql = "
            SELECT g.*, u.name as leader_name 
            FROM small_groups g 
            LEFT JOIN users u ON g.leader_id = u.id
            WHERE g.id = ?
        ";
        $params = [$id];
        [$where, $branchParams] = BranchScope::where('g');
        if ($where !== '') {
            $sql .= " AND $where";
            $params = array_merge($params, $branchParams);
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function getMembers($groupId) {
        $sql = "
            SELECT m.*, gm.role, gm.joined_at 
            FROM members m 
            JOIN group_members gm ON m.id = gm.member_id 
            JOIN small_groups g ON g.id = gm.group_id
            WHERE gm.group_id = ?
        ";
        $params = [$groupId];
        [$where, $branchParams] = BranchScope::where('g');
        if ($where !== '') {
            $sql .= " AND $where";
            $params = array_merge($params, $branchParams);
        }
        $sql .= " ORDER BY gm.role DESC, m.last_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function addMember($groupId, $memberId, $role = 'member') {
        if (!$this->canAttachMember($groupId, $memberId)) {
            return false;
        }

        // Check if already exists
        $stmt = $this->db->prepare("SELECT id FROM group_members WHERE group_id = :gid AND member_id = :mid");
        $stmt->execute(['gid' => $groupId, 'mid' => $memberId]);
        if ($stmt->fetch()) {
            return false; // Already in group
        }

        $stmt = $this->db->prepare("INSERT INTO group_members (group_id, member_id, role, joined_at) VALUES (:gid, :mid, :role, CURDATE())");
        return $stmt->execute(['gid' => $groupId, 'mid' => $memberId, 'role' => $role]);
    }
    
    public function removeMember($groupId, $memberId) {
        if (!$this->canAttachMember($groupId, $memberId)) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM group_members WHERE group_id = :gid AND member_id = :mid");
        return $stmt->execute(['gid' => $groupId, 'mid' => $memberId]);
    }

    public function updateMemberRole($groupId, $memberId, $role) {
        if (!$this->canAttachMember($groupId, $memberId)) {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE group_members SET role = :role WHERE group_id = :gid AND member_id = :mid");
        return $stmt->execute(['role' => $role, 'gid' => $groupId, 'mid' => $memberId]);
    }

    private function canAttachMember($groupId, $memberId) {
        $stmt = $this->db->prepare("
            SELECT g.branch_id as group_branch_id, m.branch_id as member_branch_id
            FROM small_groups g
            JOIN members m ON m.id = ?
            WHERE g.id = ?
        ");
        $stmt->execute([$memberId, $groupId]);
        $row = $stmt->fetch();

        return $row
            && (int)$row['group_branch_id'] === (int)$row['member_branch_id']
            && BranchScope::canAccess($row['group_branch_id']);
    }
}
