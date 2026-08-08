<?php
namespace App\Controllers\Admin;

use App\Core\BranchScope;
use App\Core\Controller;
use App\Core\Database;
use App\Models\Family;
use App\Models\Member;

class MemberController extends Controller {
    
    public function __construct() {
        $this->requireRoles([1, 2, 3, 7]);
    }

    public function index() {
        $memberModel = new Member();
        $pagination = $this->paginationParams(15);
        $totalMembers = $memberModel->countAll();
        $pagination = $this->paginationMeta($totalMembers, $pagination, 'members');
        $members = $memberModel->findPaginated($pagination['per_page'], $pagination['offset']);
        $memberStats = $memberModel->membershipStats();
        
        $this->view('admin/members/index', [
            'title' => 'Member Management',
            'members' => $members,
            'memberStats' => $memberStats,
            'pagination' => $pagination
        ]);
    }

    public function create() {
        $familyModel = new Family();
        $families = $familyModel->findAll();
        $branches = BranchScope::branchOptions(Database::getInstance()->getConnection());
        
        $this->view('admin/members/create', [
            'title' => 'Add Member',
            'families' => $families,
            'branches' => $branches,
            'selectedBranchId' => BranchScope::currentBranchId()
        ]);
    }

    public function store() {
        $branchId = BranchScope::isSuperAdmin()
            ? (int)($_POST['branch_id'] ?? 0)
            : (int)BranchScope::currentBranchId();

        if ($branchId <= 0) {
            $_SESSION['error'] = 'Please select a branch for this member.';
            $this->redirect('/admin/members/create');
        }

        $data = [
            'branch_id' => $branchId,
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'gender' => $_POST['gender'],
            'marital_status' => $_POST['marital_status'],
            'dob' => $_POST['dob'] ?: null,
            'address' => $_POST['address'],
            'occupation' => $_POST['occupation'],
            'status' => 'active',
            'membership_type' => $_POST['membership_type'] ?? 'Guest',
            'joined_at' => date('Y-m-d')
        ];

        // Handle Family
        if (!empty($_POST['family_id'])) {
            $data['family_id'] = $_POST['family_id'];
            $data['family_role'] = $_POST['family_role'];
        } elseif (!empty($_POST['new_family_name'])) {
            // Create new family
            $familyModel = new Family();
            $familyId = $familyModel->create([
                'branch_id' => $branchId,
                'name' => $_POST['new_family_name']
            ]);
            if ($familyId) {
                $data['family_id'] = $familyId;
                $data['family_role'] = $_POST['family_role'];
            }
        }

        $memberModel = new Member();
        if ($memberModel->create($data)) {
            $this->redirect('/admin/members');
        } else {
            die("Error creating member");
        }
    }

    public function show($id) {
        $memberModel = new Member();
        $member = $memberModel->find($id);
        
        if (!$member) {
            $this->redirect('/admin/members');
        }

        $family = $memberModel->getFamily($id);
        $familyMembers = $family ? $memberModel->getFamilyMembers($family['id']) : [];
        $notes = $memberModel->getNotes($id);
        $groups = $memberModel->getGroups($id);
        $attendance = $memberModel->getAttendanceStats($id);

        $this->view('admin/members/show', [
            'title' => $member['first_name'] . ' ' . $member['last_name'],
            'member' => $member,
            'family' => $family,
            'familyMembers' => $familyMembers,
            'notes' => $notes,
            'groups' => $groups,
            'attendance' => $attendance
        ]);
    }

    public function edit($id) {
        $memberModel = new Member();
        $member = $memberModel->find($id);
        
        if (!$member) {
            $this->redirect('/admin/members');
        }

        $familyModel = new Family();
        $families = $familyModel->findAll();
        $branches = BranchScope::branchOptions(Database::getInstance()->getConnection());

        $this->view('admin/members/edit', [
            'title' => 'Edit Member',
            'member' => $member,
            'families' => $families,
            'branches' => $branches
        ]);
    }

    public function update($id) {
        $memberModel = new Member();
        if (!$memberModel->find($id)) {
            $this->redirect('/admin/members');
        }

        $branchId = BranchScope::isSuperAdmin()
            ? (int)($_POST['branch_id'] ?? 0)
            : null;

        $data = [
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'gender' => $_POST['gender'],
            'marital_status' => $_POST['marital_status'],
            'dob' => $_POST['dob'] ?: null,
            'address' => $_POST['address'],
            'occupation' => $_POST['occupation'],
            'membership_type' => $_POST['membership_type'],
            'status' => $_POST['status']
        ];

        if (BranchScope::isSuperAdmin() && $branchId > 0) {
            $data['branch_id'] = $branchId;
        }

        if (!empty($_POST['family_id'])) {
            $data['family_id'] = $_POST['family_id'];
            $data['family_role'] = $_POST['family_role'];
        }

        if ($memberModel->update($id, $data)) {
            $this->redirect('/admin/members/show/' . $id);
        } else {
            die("Error updating member");
        }
    }

    public function addNote() {
        $memberId = $_POST['member_id'];
        $content = $_POST['note_content'];
        $visibility = $_POST['visibility'];
        $authorId = $_SESSION['user_id'];

        $memberModel = new Member();
        if (!$memberModel->find($memberId)) {
            $this->redirect('/admin/members');
        }

        $memberModel->addNote($memberId, $authorId, $content, $visibility);
        
        $this->redirect('/admin/members/show/' . $memberId);
    }
}
