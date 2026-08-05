<?php
namespace App\Models;

use App\Core\BranchScope;
use App\Core\Model;

class Member extends Model {
    protected $table = 'members';
    protected $branchScoped = true;

    public function findAll() {
        [$where, $params] = BranchScope::where();
        $sql = "SELECT * FROM members";
        if ($where !== '') {
            $sql .= " WHERE $where";
        }
        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($id) {
        $sql = "SELECT * FROM members WHERE id = ?";
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
        $sql = "SELECT * FROM members WHERE {$column} = ?";
        $queryParams = [$value];
        if ($where !== '') {
            $sql .= " AND $where";
            $queryParams = array_merge($queryParams, $params);
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($queryParams);
        return $stmt->fetchAll();
    }

    public function getFamily($memberId) {
        $member = $this->find($memberId);
        if ($member && !empty($member['family_id'])) {
            $stmt = $this->db->prepare("SELECT * FROM families WHERE id = :id");
            $stmt->execute(['id' => $member['family_id']]);
            return $stmt->fetch();
        }
        return null;
    }
    
    public function getFamilyMembers($familyId) {
        if (!$familyId) return [];
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

    public function getNotes($memberId) {
        $stmt = $this->db->prepare("SELECT n.*, u.name as author_name FROM member_notes n JOIN users u ON n.author_id = u.id WHERE member_id = :id ORDER BY created_at DESC");
        $stmt->execute(['id' => $memberId]);
        return $stmt->fetchAll();
    }
    
    public function addNote($memberId, $authorId, $content, $visibility = 'private') {
        $stmt = $this->db->prepare("INSERT INTO member_notes (member_id, author_id, note_content, visibility) VALUES (:mid, :aid, :content, :vis)");
        return $stmt->execute([
            'mid' => $memberId,
            'aid' => $authorId,
            'content' => $content,
            'vis' => $visibility
        ]);
    }

    public function getGroups($memberId) {
        $stmt = $this->db->prepare("
            SELECT g.*, gm.role 
            FROM small_groups g 
            JOIN group_members gm ON g.id = gm.group_id 
            WHERE gm.member_id = :id
        ");
        $stmt->execute(['id' => $memberId]);
        return $stmt->fetchAll();
    }

    public function getVolunteerRoles($memberId) {
        $stmt = $this->db->prepare("
            SELECT vr.*, va.status 
            FROM volunteer_roles vr 
            JOIN volunteer_assignments va ON vr.id = va.role_id 
            WHERE va.member_id = :id
        ");
        $stmt->execute(['id' => $memberId]);
        return $stmt->fetchAll();
    }
    
    public function getAttendanceStats($memberId) {
        [$where, $branchParams] = BranchScope::where('m');
        $sql = "
            SELECT 
                COUNT(*) as total_attended,
                MAX(check_in_time) as last_attended
            FROM individual_attendance ia
            JOIN members m ON m.id = ia.member_id
            WHERE ia.member_id = ? AND ia.status = 'present'
        ";
        $params = [$memberId];
        if ($where !== '') {
            $sql .= " AND $where";
            $params = array_merge($params, $branchParams);
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function getUpcomingBirthdays($limit = 5) {
        $limit = max(1, (int)$limit);
        [$where, $params] = BranchScope::where();
        $branchSql = $where !== '' ? "AND $where" : "";
        $stmt = $this->db->prepare("
            SELECT *, 
                DATE_FORMAT(dob, '%m-%d') as birth_day 
            FROM members 
            WHERE dob IS NOT NULL 
            AND status = 'active'
            $branchSql
            AND (
                DATE_FORMAT(dob, '%m-%d') >= DATE_FORMAT(CURDATE(), '%m-%d') 
                OR DATE_FORMAT(dob, '%m-%d') < DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 30 DAY), '%m-%d')
            )
            ORDER BY 
                CASE 
                    WHEN DATE_FORMAT(dob, '%m-%d') >= DATE_FORMAT(CURDATE(), '%m-%d') THEN 0 
                    ELSE 1 
                END,
                DATE_FORMAT(dob, '%m-%d') ASC
            LIMIT $limit
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
