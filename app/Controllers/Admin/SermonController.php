<?php
namespace App\Controllers\Admin;

use App\Core\BranchScope;
use App\Core\Controller;
use App\Core\Database;
use App\Models\Sermon;

class SermonController extends Controller {
    
    public function __construct() {
        $this->requireRoles([1, 2, 3, 7]);
    }

    public function index() {
        $sermonModel = new Sermon();
        $sermons = $sermonModel->findAll();
        
        $this->view('admin/sermons/index', [
            'title' => 'Sermon Management',
            'sermons' => $sermons
        ]);
    }

    public function create() {
        $db = Database::getInstance()->getConnection();

        $this->view('admin/sermons/create', [
            'title' => 'Upload Sermon',
            'branches' => BranchScope::branchOptions($db),
            'selectedBranchId' => BranchScope::currentBranchId()
        ]);
    }

    public function store() {
        $branchId = BranchScope::isSuperAdmin()
            ? (int)($_POST['branch_id'] ?? 0)
            : BranchScope::currentBranchId();

        if (!$branchId || !BranchScope::canAccess($branchId)) {
            $_SESSION['error'] = 'Please select a valid branch for this sermon.';
            $this->redirect('/admin/sermons/create');
        }

        $audioUrl = trim($_POST['audio_url'] ?? '');
        if (!empty($_FILES['audio_file']['name'])) {
            $uploadedAudio = $this->storeAudioUpload($_FILES['audio_file']);
            if (!$uploadedAudio) {
                $_SESSION['error'] = 'Please upload a valid audio file: MP3, M4A, WAV, or OGG under 200MB.';
                $this->redirect('/admin/sermons/create');
            }
            $audioUrl = $uploadedAudio;
        }

        $data = [
            'branch_id' => $branchId,
            'title' => trim($_POST['title']),
            'preacher' => trim($_POST['preacher']),
            'date_preached' => $_POST['date_preached'],
            'description' => trim($_POST['description'] ?? ''),
            'video_url' => trim($_POST['video_url'] ?? ''),
            'audio_url' => $audioUrl
        ];

        $sermonModel = new Sermon();
        if ($sermonModel->create($data)) {
            $this->redirect('/admin/sermons');
        } else {
            die("Error creating sermon");
        }
    }

    private function storeAudioUpload($file) {
        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name']) || ($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return null;
        }

        if (($file['size'] ?? 0) > 200 * 1024 * 1024) {
            return null;
        }

        $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        $allowedExtensions = ['mp3', 'm4a', 'wav', 'ogg'];
        if (!in_array($extension, $allowedExtensions, true)) {
            return null;
        }

        $allowedMimeTypes = [
            'audio/mpeg',
            'audio/mp3',
            'audio/mp4',
            'audio/x-m4a',
            'audio/wav',
            'audio/x-wav',
            'audio/ogg',
            'application/ogg',
        ];

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, $allowedMimeTypes, true)) {
            return null;
        }

        $targetDir = 'assets/uploads/sermons/audio';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $filename = 'sermon_audio_' . bin2hex(random_bytes(12)) . '_' . time() . '.' . $extension;
        $targetPath = $targetDir . '/' . $filename;

        return move_uploaded_file($file['tmp_name'], $targetPath) ? $targetPath : null;
    }
}
