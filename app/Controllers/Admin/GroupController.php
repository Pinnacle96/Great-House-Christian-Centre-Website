<?php
namespace App\Controllers\Admin;

use App\Core\BranchScope;
use App\Core\Controller;
use App\Core\Database;
use App\Models\Group;
use App\Models\User;
use App\Models\Member;

class GroupController extends Controller {

    public function __construct() {
        $this->requireRoles([1, 2, 3, 7]);
    }

    public function index() {
        $groupModel = new Group();
        $groups = $groupModel->findAllWithLeader();
        
        $this->view('admin/groups/index', [
            'title' => 'Small Groups & Ministries',
            'groups' => $groups
        ]);
    }

    public function create() {
        $db = Database::getInstance()->getConnection();
        $users = $this->branchUsers($db); // Potential leaders (Staff/Admins)
        
        $this->view('admin/groups/create', [
            'title' => 'Create New Group',
            'users' => $users,
            'branches' => BranchScope::branchOptions($db),
            'selectedBranchId' => BranchScope::currentBranchId()
        ]);
    }

    public function store() {
        $branchId = BranchScope::isSuperAdmin()
            ? (int)($_POST['branch_id'] ?? 0)
            : BranchScope::currentBranchId();

        if (!$branchId || !BranchScope::canAccess($branchId)) {
            $_SESSION['error'] = 'Please select a valid branch for this group.';
            $this->redirect('/admin/groups/create');
        }

        $data = [
            'branch_id' => $branchId,
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'type' => $_POST['type'],
            'schedule_info' => $_POST['schedule_info'],
            'location' => $_POST['location'],
            'leader_id' => !empty($_POST['leader_id']) ? $_POST['leader_id'] : null
        ];

        $groupModel = new Group();
        if ($groupModel->create($data)) {
            $this->redirect('/admin/groups');
        } else {
            die("Error creating group");
        }
    }

    public function show($id) {
        $groupModel = new Group();
        $group = $groupModel->findWithLeader($id);
        
        if (!$group) {
            $this->redirect('/admin/groups');
        }

        $members = $groupModel->getMembers($id);
        
        // For adding members dropdown
        $memberModel = new Member();
        $allMembers = $memberModel->findAll();

        $this->view('admin/groups/show', [
            'title' => $group['name'],
            'group' => $group,
            'members' => $members,
            'allMembers' => $allMembers
        ]);
    }

    public function edit($id) {
        $groupModel = new Group();
        $group = $groupModel->find($id);
        
        if (!$group) {
            $this->redirect('/admin/groups');
        }

        $db = Database::getInstance()->getConnection();
        $users = $this->branchUsers($db);

        $this->view('admin/groups/edit', [
            'title' => 'Edit Group',
            'group' => $group,
            'users' => $users,
            'branches' => BranchScope::branchOptions($db)
        ]);
    }

    public function update($id) {
        $groupModel = new Group();
        $group = $groupModel->find($id);
        if (!$group) {
            $this->redirect('/admin/groups');
        }

        $data = [
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'type' => $_POST['type'],
            'schedule_info' => $_POST['schedule_info'],
            'location' => $_POST['location'],
            'leader_id' => !empty($_POST['leader_id']) ? $_POST['leader_id'] : null
        ];

        if (BranchScope::isSuperAdmin() && !empty($_POST['branch_id'])) {
            $data['branch_id'] = (int)$_POST['branch_id'];
        }

        if ($groupModel->update($id, $data)) {
            $this->redirect('/admin/groups/show/' . $id);
        } else {
            die("Error updating group");
        }
    }

    public function addMember() {
        $groupId = $_POST['group_id'];
        $memberId = $_POST['member_id'];
        $role = $_POST['role'];

        $groupModel = new Group();
        $groupModel->addMember($groupId, $memberId, $role);
        
        $this->redirect('/admin/groups/show/' . $groupId);
    }

    public function removeMember() {
        $groupId = $_POST['group_id'];
        $memberId = $_POST['member_id'];

        $groupModel = new Group();
        $groupModel->removeMember($groupId, $memberId);
        
        $this->redirect('/admin/groups/show/' . $groupId);
    }

    public function updateRole() {
        $groupId = $_POST['group_id'];
        $memberId = $_POST['member_id'];
        $role = $_POST['role'];

        $groupModel = new Group();
        $groupModel->updateMemberRole($groupId, $memberId, $role);
        
        $this->redirect('/admin/groups/show/' . $groupId);
    }

    private function branchUsers($db) {
        $sql = "
            SELECT u.*, r.name as role_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.role_id IN (1, 2, 3, 5, 6, 7)
        ";
        $params = [];

        if (!BranchScope::isSuperAdmin()) {
            $sql .= " AND (u.branch_id = ? OR u.role_id = 1)";
            $params[] = BranchScope::currentBranchId();
        }

        $sql .= " ORDER BY u.name ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
