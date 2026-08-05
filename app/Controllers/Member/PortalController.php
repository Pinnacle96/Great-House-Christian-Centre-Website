<?php
namespace App\Controllers\Member;

use App\Core\BranchScope;
use App\Core\Controller;
use App\Core\Database;

class PortalController extends Controller {

    public function __construct() {
        $this->requireRoles([4]);
    }

    public function index() {
        $db = Database::getInstance()->getConnection();
        $userId = $_SESSION['user_id'];

        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        $member = null;
        $groups = [];
        $registrations = [];
        $donations = [];

        if ($user && !empty($user['email'])) {
            $branchId = BranchScope::currentBranchId();
            if ($branchId) {
                $stmt = $db->prepare("SELECT * FROM members WHERE email = ? AND branch_id = ? LIMIT 1");
                $stmt->execute([$user['email'], $branchId]);
            } else {
                $stmt = $db->prepare("SELECT * FROM members WHERE email = ? LIMIT 1");
                $stmt->execute([$user['email']]);
            }
            $member = $stmt->fetch();

            if ($member) {
                $stmt = $db->prepare("
                    SELECT g.name, g.type, g.schedule_info, g.location, gm.role
                    FROM group_members gm
                    JOIN small_groups g ON g.id = gm.group_id
                    WHERE gm.member_id = ?
                    ORDER BY g.name ASC
                ");
                $stmt->execute([$member['id']]);
                $groups = $stmt->fetchAll();
            }

            $stmt = $db->prepare("
                SELECT r.*, e.title as event_title, e.start_datetime, e.location
                FROM registrations r
                JOIN events e ON e.id = r.event_id
                WHERE r.email = ?" . ($branchId ? " AND r.branch_id = ?" : "") . "
                ORDER BY r.created_at DESC
                LIMIT 10
            ");
            $params = [$user['email']];
            if ($branchId) $params[] = $branchId;
            $stmt->execute($params);
            $registrations = $stmt->fetchAll();

            $stmt = $db->prepare("
                SELECT d.*, f.name as fund_name
                FROM donations d
                LEFT JOIN funds f ON f.id = d.fund_id
                WHERE d.donor_email = ?" . ($branchId ? " AND d.branch_id = ?" : "") . "
                ORDER BY d.donation_date DESC
                LIMIT 10
            ");
            $params = [$user['email']];
            if ($branchId) $params[] = $branchId;
            $stmt->execute($params);
            $donations = $stmt->fetchAll();
        }

        $this->view('member/dashboard', [
            'title' => 'Member Portal',
            'user' => $user,
            'member' => $member,
            'groups' => $groups,
            'registrations' => $registrations,
            'donations' => $donations
        ]);
    }
}
