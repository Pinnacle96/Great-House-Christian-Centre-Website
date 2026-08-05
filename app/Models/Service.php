<?php
namespace App\Models;

use App\Core\BranchScope;
use App\Core\Model;

class Service extends Model {
    protected $table = 'services';
    protected $branchScoped = true;

    public function find($id) {
        $sql = "SELECT * FROM services WHERE id = ?";
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

    public function getUpcomingServices($limit = 5) {
        $limit = max(1, (int)$limit);
        $sql = "SELECT * FROM services WHERE service_date >= CURDATE()";
        [$sql, $params] = BranchScope::appendWhere($sql);
        $sql .= " ORDER BY service_date ASC LIMIT $limit";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getPastServices($limit = 10) {
        $limit = max(1, (int)$limit);
        $sql = "SELECT * FROM services WHERE service_date < CURDATE()";
        [$sql, $params] = BranchScope::appendWhere($sql);
        $sql .= " ORDER BY service_date DESC LIMIT $limit";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getRoster($serviceId) {
        $sql = "
            SELECT sr.*, m.first_name, m.last_name, m.email, t.name as team_name 
            FROM service_roster sr 
            JOIN services s ON sr.service_id = s.id
            JOIN members m ON sr.member_id = m.id 
            JOIN small_groups t ON sr.team_id = t.id 
            WHERE sr.service_id = ?
        ";
        $params = [$serviceId];
        [$where, $branchParams] = BranchScope::where('s');
        if ($where !== '') {
            $sql .= " AND $where";
            $params = array_merge($params, $branchParams);
        }
        $sql .= " ORDER BY t.name ASC, m.last_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function addRosterMember($serviceId, $memberId, $teamId, $role) {
        if (!$this->canRoster($serviceId, $memberId, $teamId)) {
            return false;
        }

        // Check for duplicates handled by unique key in DB, but good to check here too
        $stmt = $this->db->prepare("INSERT INTO service_roster (service_id, member_id, team_id, role, status) VALUES (:sid, :mid, :tid, :role, 'pending')");
        return $stmt->execute([
            'sid' => $serviceId,
            'mid' => $memberId,
            'tid' => $teamId,
            'role' => $role
        ]);
    }

    public function removeRosterMember($rosterId) {
        $sql = "
            DELETE sr FROM service_roster sr
            JOIN services s ON sr.service_id = s.id
            WHERE sr.id = ?
        ";
        $params = [$rosterId];
        [$where, $branchParams] = BranchScope::where('s');
        if ($where !== '') {
            $sql .= " AND $where";
            $params = array_merge($params, $branchParams);
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function updateRosterStatus($rosterId, $status) {
        $sql = "
            UPDATE service_roster sr
            JOIN services s ON sr.service_id = s.id
            SET sr.status = ?
            WHERE sr.id = ?
        ";
        $params = [$status, $rosterId];
        [$where, $branchParams] = BranchScope::where('s');
        if ($where !== '') {
            $sql .= " AND $where";
            $params = array_merge($params, $branchParams);
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    private function canRoster($serviceId, $memberId, $teamId) {
        $stmt = $this->db->prepare("
            SELECT s.branch_id as service_branch_id, m.branch_id as member_branch_id, g.branch_id as team_branch_id
            FROM services s
            JOIN members m ON m.id = ?
            JOIN small_groups g ON g.id = ?
            WHERE s.id = ?
        ");
        $stmt->execute([$memberId, $teamId, $serviceId]);
        $row = $stmt->fetch();

        return $row
            && (int)$row['service_branch_id'] === (int)$row['member_branch_id']
            && (int)$row['service_branch_id'] === (int)$row['team_branch_id']
            && BranchScope::canAccess($row['service_branch_id']);
    }
}
