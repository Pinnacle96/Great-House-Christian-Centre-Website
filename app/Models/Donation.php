<?php
namespace App\Models;

use App\Core\BranchScope;
use App\Core\Model;

class Donation extends Model {
    protected $table = 'donations';

    public function updateStatus($reference, $status) {
        $stmt = $this->db->prepare("UPDATE donations SET status = :status WHERE transaction_id = :reference");
        return $stmt->execute(['status' => $status, 'reference' => $reference]);
    }

    public function findAllWithDetails($limit = 50, $offset = 0) {
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        [$where, $params] = BranchScope::where('d');
        $branchSql = $where !== '' ? "WHERE $where" : "";
        $stmt = $this->db->prepare("
            SELECT d.*, 
                   m.first_name, m.last_name, 
                   f.name as fund_name 
            FROM donations d 
            LEFT JOIN members m ON d.member_id = m.id 
            LEFT JOIN funds f ON d.fund_id = f.id 
            $branchSql
            ORDER BY d.donation_date DESC 
            LIMIT $limit OFFSET $offset
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countAllWithDetails() {
        [$where, $params] = BranchScope::where('d');
        $branchSql = $where !== '' ? "WHERE $where" : "";
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM donations d
            $branchSql
        ");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function getTotalByFund() {
        [$where, $params] = BranchScope::where('d');
        $branchSql = $where !== '' ? "AND $where" : "";
        $stmt = $this->db->prepare("
            SELECT f.name, SUM(d.amount) as total 
            FROM donations d 
            JOIN funds f ON d.fund_id = f.id 
            WHERE d.status = 'successful' 
            $branchSql
            GROUP BY f.id
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getRecentTotal($days = 30) {
        [$where, $params] = BranchScope::where();
        $branchSql = $where !== '' ? "AND $where" : "";
        $days = max(1, (int)$days);
        $stmt = $this->db->prepare("
            SELECT SUM(amount) as total 
            FROM donations 
            WHERE status = 'successful' 
            AND donation_date >= DATE_SUB(CURDATE(), INTERVAL $days DAY)
            $branchSql
        ");
        $stmt->execute($params);
        return $stmt->fetch()['total'] ?? 0;
    }

    public function getMonthlyStats() {
        [$where, $params] = BranchScope::where();
        $branchSql = $where !== '' ? "AND $where" : "";
        $stmt = $this->db->prepare("
            SELECT 
                DATE_FORMAT(donation_date, '%Y-%m') as month,
                SUM(amount) as total
            FROM donations
            WHERE status = 'successful'
            $branchSql
            GROUP BY month
            ORDER BY month DESC
            LIMIT 12
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
