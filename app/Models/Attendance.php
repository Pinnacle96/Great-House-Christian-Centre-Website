<?php
namespace App\Models;

use App\Core\Model;

class Attendance extends Model {
    protected $table = 'attendance';

    public function getAttendees($attendanceId) {
        $stmt = $this->db->prepare("
            SELECT m.*, ia.status, ia.check_in_time 
            FROM members m 
            JOIN individual_attendance ia ON m.id = ia.member_id 
            WHERE ia.attendance_id = :id
        ");
        $stmt->execute(['id' => $attendanceId]);
        return $stmt->fetchAll();
    }
}
