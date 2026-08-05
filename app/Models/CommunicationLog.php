<?php
namespace App\Models;

use App\Core\BranchScope;
use App\Core\Model;

class CommunicationLog extends Model {
    protected $table = 'communication_logs';

    public function createLog($senderId, $recipientType, $channel, $subject, $messageBody, $recipientGroupId = null, $branchId = null) {
        $stmt = $this->db->prepare("
            INSERT INTO communication_logs 
            (branch_id, sender_id, recipient_type, recipient_group_id, channel, subject, message_body, status, sent_at) 
            VALUES (:branch_id, :sid, :rtype, :gid, :channel, :subject, :body, 'sent', NOW())
        ");
        $stmt->execute([
            'branch_id' => $branchId,
            'sid' => $senderId,
            'rtype' => $recipientType,
            'gid' => $recipientGroupId,
            'channel' => $channel,
            'subject' => $subject,
            'body' => $messageBody
        ]);
        return $this->db->lastInsertId();
    }

    public function logRecipient($logId, $memberId, $contactDetail, $status = 'pending') {
        $stmt = $this->db->prepare("
            INSERT INTO message_recipients (log_id, member_id, contact_detail, status) 
            VALUES (:lid, :mid, :detail, :status)
        ");
        return $stmt->execute([
            'lid' => $logId,
            'mid' => $memberId,
            'detail' => $contactDetail,
            'status' => $status
        ]);
    }

    public function getRecentLogs($limit = 20) {
        $limit = max(1, (int)$limit);
        $sql = "
            SELECT cl.*, u.name as sender_name, sg.name as group_name
            FROM communication_logs cl
            LEFT JOIN users u ON cl.sender_id = u.id
            LEFT JOIN small_groups sg ON cl.recipient_group_id = sg.id
        ";
        [$sql, $params] = BranchScope::appendWhere($sql, [], 'cl');
        $sql .= " ORDER BY cl.created_at DESC LIMIT $limit";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
