<?php
namespace App\Controllers\Admin;

use App\Core\BranchScope;
use App\Core\Controller;
use App\Models\CommunicationLog;
use App\Models\Group;
use App\Models\Member;
use App\Services\CommunicationService;

class CommunicationController extends Controller {
    
    public function __construct() {
        $this->requireRoles([1, 2, 3, 7]);
    }

    public function index() {
        $logModel = new CommunicationLog();
        $recentLogs = $logModel->getRecentLogs(10);
        
        $groupModel = new Group();
        $groups = $groupModel->findAll();

        $this->view('admin/communication/index', [
            'title' => 'Communication Center',
            'recentLogs' => $recentLogs,
            'groups' => $groups
        ]);
    }

    public function send() {
        $channel = $_POST['channel'] ?? '';
        $recipientType = $_POST['recipient_type'] ?? '';
        $groupId = $_POST['group_id'] ?? null;
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (!in_array($channel, ['email', 'sms'], true) || !in_array($recipientType, ['all', 'group'], true) || $message === '') {
            $_SESSION['error'] = 'Please choose a valid channel, audience, and message.';
            $this->redirect('/admin/communication');
        }

        if ($channel === 'email' && $subject === '') {
            $_SESSION['error'] = 'Email broadcasts require a subject.';
            $this->redirect('/admin/communication');
        }
        
        $memberModel = new Member();
        $commService = new CommunicationService();
        $recipients = [];

        // 1. Gather Recipients
        if ($recipientType === 'all') {
            $recipients = $memberModel->where('status', 'active');
        } elseif ($recipientType === 'group' && $groupId) {
            $groupModel = new Group();
            $group = $groupModel->find($groupId);
            if (!$group) {
                $_SESSION['error'] = 'Please choose a valid group for your branch.';
                $this->redirect('/admin/communication');
            }
            $recipients = $groupModel->getMembers($groupId);
        }
        // TODO: Individual selection could be added here

        if (empty($recipients)) {
            $this->redirect('/admin/communication?error=no_recipients');
            return;
        }

        // 2. Create Log Entry
        $logModel = new CommunicationLog();
        $logRecipientType = $recipientType === 'all' ? 'all_members' : $recipientType;
        $logBranchId = BranchScope::currentBranchId();
        if (BranchScope::isSuperAdmin() && $recipientType === 'group' && !empty($group['branch_id'])) {
            $logBranchId = (int)$group['branch_id'];
        }
        $logId = $logModel->createLog(
            $_SESSION['user_id'], 
            $logRecipientType, 
            $channel, 
            $subject, 
            $message, 
            $groupId,
            $logBranchId
        );

        // 3. Send Messages & Log Individual Status
        $successCount = 0;
        
        foreach ($recipients as $recipient) {
            $status = 'failed';
            $contactDetail = '';

            if ($channel === 'email') {
                $contactDetail = $recipient['email'];
                if ($contactDetail && filter_var($contactDetail, FILTER_VALIDATE_EMAIL)) {
                    if ($commService->sendEmail($contactDetail, $subject, $message, $logBranchId)) {
                        $status = 'sent';
                        $successCount++;
                    }
                }
            } elseif ($channel === 'sms') {
                $contactDetail = $recipient['phone'];
                if ($contactDetail) {
                    if ($commService->sendSMS($contactDetail, $message, $logBranchId)) {
                        $status = 'sent';
                        $successCount++;
                    }
                }
            }

            // Log individual result
            $logModel->logRecipient($logId, $recipient['id'], $contactDetail, $status);
        }

        // 4. Update Log with counts (optional, can be calculated)
        // For now, we just redirect
        $this->redirect('/admin/communication?success=' . $successCount);
    }
}
