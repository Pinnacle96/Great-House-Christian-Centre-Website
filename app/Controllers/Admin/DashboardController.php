<?php
namespace App\Controllers\Admin;

use App\Core\BranchScope;
use App\Core\Controller;
use App\Core\Database;
use App\Models\Member;

class DashboardController extends Controller {
    
    public function __construct() {
        $this->requireAuth();
        if ((int)($_SESSION['role_id'] ?? 0) === 4) {
            $this->redirect('/member');
        }
    }

    public function index() {
        $db = Database::getInstance()->getConnection();
        $roleId = $_SESSION['role_id'];
        
        $stats = [];
        $upcomingBirthdays = [];

        // --- REGISTRATION TEAM (Role 6) & MANAGER (Role 5) ---
        if (in_array($roleId, [5, 6])) {
            // They mainly care about Event Stats
            $stats['total_events'] = $this->getTotalEvents($db);
            $stats['event_attendance'] = $this->getEventAttendance($db);
            
            // Add specific registration stats if needed (e.g. total registrations today)
            $stats['recent_registrations'] = $this->getRecentRegistrations($db);
        }
        
        // --- ADMIN (1) & PASTOR (2) & LEADER (3) ---
        elseif (in_array($roleId, [1, 2, 3, 7])) {
            $stats['total_members'] = $this->getTotalMembers($db);
            $stats['total_sermons'] = $this->getTotalSermons($db);
            $stats['total_events'] = $this->getTotalEvents($db);
            $stats['recent_members'] = $this->getRecentMembers($db);
            $stats['pending_prayers'] = $this->getPendingPrayers($db);
            $stats['member_growth'] = $this->getMemberGrowth($db);
            $stats['event_attendance'] = $this->getEventAttendance($db);
            $stats['sermon_stats'] = $this->getSermonStats($db);
            $stats['member_demographics'] = $this->getMemberDemographics($db);
            if (BranchScope::isSuperAdmin()) {
                $stats['branch_summary'] = $this->getBranchSummary($db);
            }

            // Birthday Logic
            $memberModel = new Member();
            $upcomingBirthdays = $memberModel->getUpcomingBirthdays(5);
            
            // --- FINANCE ONLY FOR ADMIN (1) & PASTOR (2) ---
            if (in_array($roleId, [1, 2, 7])) {
                $stats['total_donations'] = $this->getTotalDonations($db);
                $stats['recent_donations'] = $this->getRecentDonations($db);
                $stats['monthly_donations'] = $this->getMonthlyDonations($db);
                $stats['donation_categories'] = $this->getDonationCategories($db);
                $stats['weekly_donations'] = $this->getWeeklyDonations($db);
            }
        }

        $this->view('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'user_name' => $_SESSION['user_name'] ?? 'User',
            'role' => $_SESSION['role_name'] ?? 'Member',
            'stats' => $stats,
            'upcomingBirthdays' => $upcomingBirthdays,
            'role_id' => $roleId
        ]);
    }

    private function getRecentRegistrations($db) {
        $sql = "SELECT r.*, e.title as event_title 
                            FROM registrations r 
                            JOIN events e ON r.event_id = e.id";
        [$sql, $params] = BranchScope::appendWhere($sql, [], 'r');
        $sql .= " ORDER BY r.created_at DESC LIMIT 5";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function getTotalMembers($db) {
        $sql = "SELECT COUNT(*) as count FROM members WHERE status = 'active'";
        [$sql, $params] = BranchScope::appendWhere($sql);
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['count'];
    }

    private function getTotalDonations($db) {
        $sql = "SELECT COALESCE(SUM(amount), 0) as total FROM donations WHERE status = 'successful'";
        [$sql, $params] = BranchScope::appendWhere($sql);
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return number_format($stmt->fetch()['total'], 2);
    }

    private function getTotalSermons($db) {
        $sql = "SELECT COUNT(*) as count FROM sermons";
        [$sql, $params] = BranchScope::appendWhere($sql);
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['count'];
    }

    private function getTotalEvents($db) {
        $sql = "SELECT COUNT(*) as count FROM events WHERE start_datetime >= NOW()";
        [$sql, $params] = BranchScope::appendWhere($sql);
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['count'];
    }

    private function getRecentMembers($db) {
        $sql = "SELECT first_name, last_name, joined_at FROM members WHERE status = 'active'";
        [$sql, $params] = BranchScope::appendWhere($sql);
        $sql .= " ORDER BY created_at DESC LIMIT 5";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function getRecentDonations($db) {
        $sql = "SELECT donor_name, amount, donation_date FROM donations WHERE status = 'successful'";
        [$sql, $params] = BranchScope::appendWhere($sql);
        $sql .= " ORDER BY donation_date DESC LIMIT 5";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function getPendingPrayers($db) {
        $sql = "SELECT COUNT(*) as count FROM prayer_requests WHERE status = 'new'";
        [$sql, $params] = BranchScope::appendWhere($sql);
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['count'];
    }

    private function getMonthlyDonations($db) {
        $sql = "SELECT 
            DATE_FORMAT(donation_date, '%Y-%m') as month,
            COALESCE(SUM(amount), 0) as total
            FROM donations 
            WHERE status = 'successful' AND donation_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)";
        [$sql, $params] = BranchScope::appendWhere($sql);
        $sql .= " GROUP BY DATE_FORMAT(donation_date, '%Y-%m') ORDER BY month DESC LIMIT 6";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function getMemberGrowth($db) {
        $sql = "SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as new_members
            FROM members 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)";
        [$sql, $params] = BranchScope::appendWhere($sql);
        $sql .= " GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY month ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function getDonationCategories($db) {
        $sql = "SELECT 
            COALESCE(type, 'General') as category,
            COUNT(*) as count,
            COALESCE(SUM(amount), 0) as total
            FROM donations 
            WHERE status = 'successful'";
        [$sql, $params] = BranchScope::appendWhere($sql);
        $sql .= " GROUP BY COALESCE(type, 'General') ORDER BY total DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function getEventAttendance($db) {
        $sql = "SELECT 
            title,
            start_datetime,
            location,
            requires_registration
            FROM events 
            WHERE start_datetime >= NOW()";
        [$sql, $params] = BranchScope::appendWhere($sql);
        $sql .= " ORDER BY start_datetime ASC LIMIT 5";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function getSermonStats($db) {
        $sql = "SELECT 
            DATE_FORMAT(date_preached, '%Y-%m') as month,
            COUNT(*) as sermon_count
            FROM sermons 
            WHERE date_preached >= DATE_SUB(NOW(), INTERVAL 6 MONTH)";
        [$sql, $params] = BranchScope::appendWhere($sql);
        $sql .= " GROUP BY DATE_FORMAT(date_preached, '%Y-%m') ORDER BY month DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function getWeeklyDonations($db) {
        $sql = "SELECT 
            YEARWEEK(donation_date) as week_number,
            COALESCE(SUM(amount), 0) as total
            FROM donations 
            WHERE status = 'successful' AND donation_date >= DATE_SUB(NOW(), INTERVAL 8 WEEK)";
        [$sql, $params] = BranchScope::appendWhere($sql);
        $sql .= " GROUP BY YEARWEEK(donation_date) ORDER BY week_number ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function getMemberDemographics($db) {
        $sql = "SELECT 
            COALESCE(d.name, 'No Department') as department,
            COUNT(*) as count
            FROM members m
            LEFT JOIN departments d ON m.department_id = d.id
            WHERE m.status = 'active'";
        [$sql, $params] = BranchScope::appendWhere($sql, [], 'm');
        $sql .= " GROUP BY COALESCE(d.name, 'No Department') ORDER BY count DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function getBranchSummary($db) {
        $stmt = $db->query("
            SELECT b.id, b.name,
                (SELECT COUNT(*) FROM members m WHERE m.branch_id = b.id AND m.status = 'active') as members,
                (SELECT COUNT(*) FROM events e WHERE e.branch_id = b.id AND e.start_datetime >= NOW()) as upcoming_events,
                (SELECT COALESCE(SUM(d.amount), 0) FROM donations d WHERE d.branch_id = b.id AND d.status = 'successful') as donations
            FROM branches b
            WHERE b.is_active = 1
            ORDER BY b.name ASC
        ");
        return $stmt->fetchAll();
    }
}
