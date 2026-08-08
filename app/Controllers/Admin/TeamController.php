<?php
namespace App\Controllers\Admin;

use App\Core\BranchScope;
use App\Core\Controller;
use App\Core\Database;

class TeamController extends Controller {

    public function __construct() {
        $this->requireAuth();
        BranchScope::requireGlobalFrontendAccess();
    }

    public function index() {
        $db = Database::getInstance()->getConnection();
        $pagination = $this->paginationParams(15);
        $totalMembers = (int)$db->query("SELECT COUNT(*) FROM team_members")->fetchColumn();
        $pagination = $this->paginationMeta($totalMembers, $pagination, 'team members');

        $limit = (int)$pagination['per_page'];
        $offset = (int)$pagination['offset'];
        $stmt = $db->query("SELECT * FROM team_members ORDER BY display_order ASC LIMIT $limit OFFSET $offset");
        $members = $stmt->fetchAll();

        $this->view('admin/team/index', [
            'title' => 'Manage Team Members',
            'members' => $members,
            'pagination' => $pagination
        ]);
    }

    public function create() {
        $this->view('admin/team/create', ['title' => 'Add Team Member']);
    }

    public function store() {
        $db = Database::getInstance()->getConnection();
        
        $imagePath = null;
        if (!empty($_FILES['image']['name'])) {
            $imagePath = $this->storeImageUpload($_FILES['image'], 'assets/uploads/team', 'team');
            if (!$imagePath) {
                $_SESSION['error'] = 'Please upload a valid image under 2MB.';
                $this->redirect('/admin/team/create');
            }
        }

        $sql = "INSERT INTO team_members (name, role, bio, image, facebook, twitter, instagram, display_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $_POST['name'],
            $_POST['role'],
            $_POST['bio'] ?? null,
            $imagePath,
            $_POST['facebook'] ?? null,
            $_POST['twitter'] ?? null,
            $_POST['instagram'] ?? null,
            $_POST['display_order'] ?? 0
        ]);

        header('Location: ' . APP_URL . '/admin/team');
        exit;
    }

    public function edit($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM team_members WHERE id = ?");
        $stmt->execute([$id]);
        $member = $stmt->fetch();

        if (!$member) {
            header('Location: ' . APP_URL . '/admin/team');
            exit;
        }

        $this->view('admin/team/edit', [
            'title' => 'Edit Team Member',
            'member' => $member
        ]);
    }

    public function update($id) {
        $db = Database::getInstance()->getConnection();
        
        // Get existing member to check for old image
        $stmt = $db->prepare("SELECT image FROM team_members WHERE id = ?");
        $stmt->execute([$id]);
        $existing = $stmt->fetch();
        $imagePath = $existing['image'];

        if (!empty($_FILES['image']['name'])) {
            $uploadedPath = $this->storeImageUpload($_FILES['image'], 'assets/uploads/team', 'team');
            if (!$uploadedPath) {
                $_SESSION['error'] = 'Please upload a valid image under 2MB.';
                $this->redirect('/admin/team/edit/' . $id);
            }
            $imagePath = $uploadedPath;
        }

        $sql = "UPDATE team_members SET 
                name = ?, role = ?, bio = ?, image = ?, 
                facebook = ?, twitter = ?, instagram = ?, display_order = ? 
                WHERE id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $_POST['name'],
            $_POST['role'],
            $_POST['bio'] ?? null,
            $imagePath,
            $_POST['facebook'] ?? null,
            $_POST['twitter'] ?? null,
            $_POST['instagram'] ?? null,
            $_POST['display_order'] ?? 0,
            $id
        ]);

        header('Location: ' . APP_URL . '/admin/team');
        exit;
    }

    public function delete($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM team_members WHERE id = ?");
        $stmt->execute([$id]);
        
        header('Location: ' . APP_URL . '/admin/team');
        exit;
    }
}
