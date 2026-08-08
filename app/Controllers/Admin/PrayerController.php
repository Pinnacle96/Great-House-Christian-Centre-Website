<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\PrayerRequest;

class PrayerController extends Controller {
    
    public function __construct() {
        $this->requireRoles([1, 2, 3, 7]);
    }

    public function index() {
        $prayerModel = new PrayerRequest();
        $pagination = $this->paginationParams(15);
        $totalPrayers = $prayerModel->countAll();
        $pagination = $this->paginationMeta($totalPrayers, $pagination, 'prayer requests');
        $prayers = $prayerModel->findPaginated($pagination['per_page'], $pagination['offset']);
        $prayerStats = $prayerModel->stats();
        
        $this->view('admin/prayers/index', [
            'title' => 'Prayer Requests',
            'prayers' => $prayers,
            'prayerStats' => $prayerStats,
            'pagination' => $pagination
        ]);
    }

    public function markPrayed($id) {
        $prayerModel = new PrayerRequest();
        if ($prayerModel->find($id)) {
            $prayerModel->update($id, ['status' => 'prayed']);
        }
        $_SESSION['success'] = 'Prayer request marked as prayed.';
        $this->redirect('/admin/prayers');
    }

    public function archive($id) {
        $prayerModel = new PrayerRequest();
        if ($prayerModel->find($id)) {
            $prayerModel->update($id, ['status' => 'archived']);
        }
        $_SESSION['success'] = 'Prayer request archived.';
        $this->redirect('/admin/prayers');
    }

    public function delete($id) {
        $prayerModel = new PrayerRequest();
        if ($prayerModel->find($id)) {
            $prayerModel->delete($id);
        }
        $_SESSION['success'] = 'Prayer request deleted.';
        $this->redirect('/admin/prayers');
    }
}
