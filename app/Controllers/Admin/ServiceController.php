<?php
namespace App\Controllers\Admin;

use App\Core\BranchScope;
use App\Core\Controller;
use App\Core\Database;
use App\Models\Service;
use App\Models\Group;

class ServiceController extends Controller {

    public function __construct() {
        $this->requireRoles([1, 2, 3, 7]);
    }

    public function index() {
        $serviceModel = new Service();
        $upcomingServices = $serviceModel->getUpcomingServices();
        $pagination = $this->paginationParams(15);
        $totalPastServices = $serviceModel->countPastServices();
        $pagination = $this->paginationMeta($totalPastServices, $pagination, 'past services');
        $pastServices = $serviceModel->getPastServices($pagination['per_page'], $pagination['offset']);

        $this->view('admin/services/index', [
            'title' => 'Service Planning',
            'upcomingServices' => $upcomingServices,
            'pastServices' => $pastServices,
            'pagination' => $pagination
        ]);
    }

    public function create() {
        $db = Database::getInstance()->getConnection();

        $this->view('admin/services/create', [
            'title' => 'Schedule New Service',
            'branches' => BranchScope::branchOptions($db),
            'selectedBranchId' => BranchScope::currentBranchId()
        ]);
    }

    public function store() {
        $branchId = BranchScope::isSuperAdmin()
            ? (int)($_POST['branch_id'] ?? 0)
            : BranchScope::currentBranchId();

        if (!$branchId || !BranchScope::canAccess($branchId)) {
            $_SESSION['error'] = 'Please select a valid branch for this service.';
            $this->redirect('/admin/services/create');
        }

        $data = [
            'branch_id' => $branchId,
            'title' => $_POST['title'],
            'service_date' => $_POST['service_date'],
            'service_time' => $_POST['service_time'],
            'type' => $_POST['type'],
            'series_title' => $_POST['series_title'],
            'notes' => $_POST['notes']
        ];

        $serviceModel = new Service();
        if ($serviceModel->create($data)) {
            $this->redirect('/admin/services');
        } else {
            die("Error creating service");
        }
    }

    public function show($id) {
        $serviceModel = new Service();
        $service = $serviceModel->find($id);

        if (!$service) {
            $this->redirect('/admin/services');
        }

        $roster = $serviceModel->getRoster($id);
        
        // Get Ministry Teams for rostering
        $groupModel = new Group();
        $teams = $groupModel->where('type', 'Ministry Team');

        $this->view('admin/services/show', [
            'title' => $service['title'],
            'service' => $service,
            'roster' => $roster,
            'teams' => $teams
        ]);
    }

    public function edit($id) {
        $serviceModel = new Service();
        $service = $serviceModel->find($id);

        if (!$service) {
            $this->redirect('/admin/services');
        }

        $db = Database::getInstance()->getConnection();

        $this->view('admin/services/edit', [
            'title' => 'Edit Service',
            'service' => $service,
            'branches' => BranchScope::branchOptions($db)
        ]);
    }

    public function update($id) {
        $serviceModel = new Service();
        $service = $serviceModel->find($id);
        if (!$service) {
            $this->redirect('/admin/services');
        }

        $data = [
            'title' => $_POST['title'],
            'service_date' => $_POST['service_date'],
            'service_time' => $_POST['service_time'],
            'type' => $_POST['type'],
            'series_title' => $_POST['series_title'],
            'notes' => $_POST['notes']
        ];

        if (BranchScope::isSuperAdmin() && !empty($_POST['branch_id'])) {
            $data['branch_id'] = (int)$_POST['branch_id'];
        }

        if ($serviceModel->update($id, $data)) {
            $this->redirect('/admin/services/show/' . $id);
        } else {
            die("Error updating service");
        }
    }

    public function addRoster() {
        $serviceId = $_POST['service_id'];
        $teamId = $_POST['team_id'];
        $memberId = $_POST['member_id'];
        $role = $_POST['role'];

        $serviceModel = new Service();
        // Check if member already rostered for this team? DB constraint handles it.
        try {
            if (!$serviceModel->addRosterMember($serviceId, $memberId, $teamId, $role)) {
                $_SESSION['error'] = 'That volunteer cannot be scheduled for this branch service.';
            }
            $this->redirect('/admin/services/show/' . $serviceId);
        } catch (\Exception $e) {
            // Handle duplicate entry gracefully
            $this->redirect('/admin/services/show/' . $serviceId . '?error=duplicate');
        }
    }

    public function removeRoster() {
        $rosterId = $_POST['roster_id'];
        $serviceId = $_POST['service_id'];

        $serviceModel = new Service();
        $serviceModel->removeRosterMember($rosterId);

        $this->redirect('/admin/services/show/' . $serviceId);
    }
    
    public function updateStatus() {
        $rosterId = $_POST['roster_id'];
        $serviceId = $_POST['service_id'];
        $status = $_POST['status'];

        $serviceModel = new Service();
        $serviceModel->updateRosterStatus($rosterId, $status);

        $this->redirect('/admin/services/show/' . $serviceId);
    }

    // AJAX endpoint to get members of a specific team
    public function getTeamMembers($teamId) {
        $groupModel = new Group();
        $members = $groupModel->getMembers($teamId);
        
        header('Content-Type: application/json');
        echo json_encode($members);
        exit;
    }
}
