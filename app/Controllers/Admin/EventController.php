<?php
namespace App\Controllers\Admin;

use App\Core\BranchScope;
use App\Core\Controller;
use App\Core\Database;
use App\Models\Event;

class EventController extends Controller {
    
    public function __construct() {
        $this->requireRoles([1, 2, 3, 5, 7]);
    }

    public function index() {
        $eventModel = new Event();
        $pagination = $this->paginationParams(15);
        $totalEvents = $eventModel->countAll();
        $pagination = $this->paginationMeta($totalEvents, $pagination, 'events');
        $events = $eventModel->findPaginated($pagination['per_page'], $pagination['offset']);
        $eventStats = $eventModel->stats();
        
        $this->view('admin/events/index', [
            'title' => 'Event Management',
            'events' => $events,
            'eventStats' => $eventStats,
            'pagination' => $pagination
        ]);
    }

    public function create() {
        $this->view('admin/events/create', [
            'title' => 'Create Event',
            'branches' => BranchScope::branchOptions(Database::getInstance()->getConnection()),
            'selectedBranchId' => BranchScope::currentBranchId()
        ]);
    }

    public function store() {
        $db = \App\Core\Database::getInstance()->getConnection();
        $branchId = BranchScope::isSuperAdmin()
            ? (int)($_POST['branch_id'] ?? 0)
            : (int)BranchScope::currentBranchId();

        if ($branchId <= 0) {
            $_SESSION['error'] = 'Please select a branch for this event.';
            $this->redirect('/admin/events/create');
        }
        
        $slug = $this->createSlug($_POST['title']);

        $sql = "INSERT INTO events (branch_id, title, slug, description, start_datetime, end_datetime, location, category, image, requires_registration) 
                VALUES (:branch_id, :title, :slug, :description, :start_datetime, :end_datetime, :location, :category, :image, :requires_registration)";
        
        $stmt = $db->prepare($sql);
        
        $data = [
            'branch_id' => $branchId,
            'title' => $_POST['title'],
            'slug' => $slug,
            'description' => $_POST['description'],
            'start_datetime' => $_POST['start_datetime'],
            'end_datetime' => !empty($_POST['end_datetime']) ? $_POST['end_datetime'] : null,
            'location' => $_POST['location'],
            'category' => $_POST['category'] ?? 'General',
            'image' => $_POST['image'] ?? null,
            'requires_registration' => isset($_POST['requires_registration']) ? 1 : 0
        ];

        if ($stmt->execute($data)) {
            header('Location: ' . APP_URL . '/admin/events');
            exit;
        } else {
            die("Error creating event");
        }
    }

    public function edit($id) {
        $eventModel = new Event();
        $event = $eventModel->find($id);
        
        if (!$event) {
            header('Location: ' . APP_URL . '/admin/events');
            exit;
        }

        $this->view('admin/events/edit', [
            'title' => 'Edit Event',
            'event' => $event,
            'branches' => BranchScope::branchOptions(Database::getInstance()->getConnection())
        ]);
    }

    public function update($id) {
        $eventModel = new Event();
        if (!$eventModel->find($id)) {
            $this->redirect('/admin/events');
        }

        $db = \App\Core\Database::getInstance()->getConnection();
        
        $slug = $this->createSlug($_POST['title'], $id);

        $branchSql = BranchScope::isSuperAdmin() && !empty($_POST['branch_id']) ? "branch_id = :branch_id," : "";
        $sql = "UPDATE events SET 
                $branchSql
                title = :title, 
                slug = :slug,
                description = :description, 
                start_datetime = :start_datetime, 
                end_datetime = :end_datetime, 
                location = :location, 
                category = :category, 
                image = :image, 
                requires_registration = :requires_registration 
                WHERE id = :id";
        
        $stmt = $db->prepare($sql);
        
        $data = [
            'id' => $id,
            'title' => $_POST['title'],
            'slug' => $slug,
            'description' => $_POST['description'],
            'start_datetime' => $_POST['start_datetime'],
            'end_datetime' => !empty($_POST['end_datetime']) ? $_POST['end_datetime'] : null,
            'location' => $_POST['location'],
            'category' => $_POST['category'] ?? 'General',
            'image' => $_POST['image'] ?? null,
            'requires_registration' => isset($_POST['requires_registration']) ? 1 : 0
        ];
        if (BranchScope::isSuperAdmin() && !empty($_POST['branch_id'])) {
            $data['branch_id'] = (int)$_POST['branch_id'];
        }

        if ($stmt->execute($data)) {
            header('Location: ' . APP_URL . '/admin/events');
            exit;
        } else {
            die("Error updating event");
        }
    }

    private function createSlug($title, $ignoreId = null) {
        $db = \App\Core\Database::getInstance()->getConnection();
        
        // Slugify logic
        $slug = preg_replace('~[^\pL\d]+~u', '-', $title);
        $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
        $slug = preg_replace('~[^-\w]+~', '', $slug);
        $slug = trim($slug, '-');
        $slug = preg_replace('~-+~', '-', $slug);
        $slug = strtolower($slug);
        if (empty($slug)) $slug = 'n-a';

        // Check uniqueness
        $originalSlug = $slug;
        $counter = 1;
        while (true) {
            $sql = "SELECT id FROM events WHERE slug = ?";
            $params = [$slug];
            if ($ignoreId) {
                $sql .= " AND id != ?";
                $params[] = $ignoreId;
            }
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            
            if ($stmt->fetch()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            } else {
                break;
            }
        }
        
        return $slug;
    }

    public function delete($id) {
        $eventModel = new Event();
        $event = $eventModel->find($id);
        if (!$event) {
            $this->redirect('/admin/events');
        }

        if ($eventModel->delete($id)) {
            header('Location: ' . APP_URL . '/admin/events');
            exit;
        } else {
            die("Error deleting event");
        }
    }
}
