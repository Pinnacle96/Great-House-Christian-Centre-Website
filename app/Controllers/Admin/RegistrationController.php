<?php
namespace App\Controllers\Admin;

use App\Core\BranchScope;
use App\Core\Controller;
use App\Core\Database;
use App\Services\CommunicationService;
use Dompdf\Dompdf;
use Dompdf\Options;

class RegistrationController extends Controller {

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
        
        // Allowed roles: Superadmin(1), Pastor(2), Leader(3), Reg Manager(5), Reg Team(6), Branch Admin(7)
        $allowed = [1, 2, 3, 5, 6, 7];
        if (!in_array($_SESSION['role_id'], $allowed)) {
            $_SESSION['error'] = "Access denied.";
            $this->redirect('/admin');
        }
    }

    public function index() {
        // Get all events for the dropdown
        $db = Database::getInstance()->getConnection();
        // Only show events that require registration for the list as well, to avoid clutter
        $sql = "SELECT id, title FROM events WHERE requires_registration = 1";
        [$sql, $params] = BranchScope::appendWhere($sql);
        $sql .= " ORDER BY start_datetime DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $events = $stmt->fetchAll();

        // Get selected event ID
        $eventId = $_GET['event_id'] ?? ($events[0]['id'] ?? null);
        $filter = $_GET['filter'] ?? 'all'; // all, onsite, online, checked_in

        $registrations = [];
        $stats = [
            'total' => 0,
            'onsite' => 0,
            'online' => 0,
            'checked_in' => 0
        ];

        if ($eventId) {
            $sql = "SELECT * FROM registrations WHERE event_id = ?";
            $params = [$eventId];
            [$branchWhere, $branchParams] = BranchScope::where();
            if ($branchWhere !== '') {
                $sql .= " AND $branchWhere";
                $params = array_merge($params, $branchParams);
            }

            if ($filter === 'onsite') {
                $sql .= " AND attendance_mode = 'onsite'";
            } elseif ($filter === 'online') {
                $sql .= " AND attendance_mode = 'online'";
            } elseif ($filter === 'checked_in') {
                $sql .= " AND checked_in_at IS NOT NULL";
            }

            $countSql = preg_replace('/^SELECT \* FROM registrations/i', 'SELECT COUNT(*) FROM registrations', $sql);
            $stmt = $db->prepare($countSql);
            $stmt->execute($params);

            $pagination = $this->paginationParams(15);
            $pagination = $this->paginationMeta((int)$stmt->fetchColumn(), $pagination, 'registrations');

            $limit = (int)$pagination['per_page'];
            $offset = (int)$pagination['offset'];
            $sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $registrations = $stmt->fetchAll();

            // Calculate Stats
            $statsSql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN attendance_mode = 'onsite' THEN 1 ELSE 0 END) as onsite,
                SUM(CASE WHEN attendance_mode = 'online' THEN 1 ELSE 0 END) as online,
                SUM(CASE WHEN checked_in_at IS NOT NULL THEN 1 ELSE 0 END) as checked_in
                FROM registrations WHERE event_id = ?";
            $statsParams = [$eventId];
            [$branchWhere, $branchParams] = BranchScope::where();
            if ($branchWhere !== '') {
                $statsSql .= " AND $branchWhere";
                $statsParams = array_merge($statsParams, $branchParams);
            }
            $stmtStats = $db->prepare($statsSql);
            $stmtStats->execute($statsParams);
            $stats = $stmtStats->fetch();
        } else {
            $pagination = $this->paginationMeta(0, $this->paginationParams(15), 'registrations');
        }

        $this->view('admin/registrations/index', [
            'events' => $events,
            'selectedEventId' => $eventId,
            'registrations' => $registrations,
            'stats' => $stats,
            'filter' => $filter,
            'pagination' => $pagination
        ]);
    }

    public function checkIn($id) {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE registrations SET checked_in_at = NOW() WHERE id = ?";
        $params = [$id];
        [$branchWhere, $branchParams] = BranchScope::where();
        if ($branchWhere !== '') {
            $sql .= " AND $branchWhere";
            $params = array_merge($params, $branchParams);
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        $this->redirectBack('/admin/registrations');
    }

    public function create() {
        $db = Database::getInstance()->getConnection();
        
        // Get all upcoming events that require registration
        // Filtering:
        // 1. requires_registration = 1
        // 2. Not past: end_datetime > NOW (if exists) OR start_datetime >= TODAY
        // This ensures multi-day events are still selectable until they end.
        $sql = "
            SELECT id, title 
            FROM events 
            WHERE requires_registration = 1 
            AND (
                (end_datetime IS NOT NULL AND end_datetime >= NOW())
                OR 
                (end_datetime IS NULL AND start_datetime >= CURRENT_DATE())
            )
        ";
        [$sql, $params] = BranchScope::appendWhere($sql);
        $sql .= " ORDER BY start_datetime ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $events = $stmt->fetchAll();

        $this->view('admin/registrations/create', [
            'title' => 'Manual Registration',
            'events' => $events,
            'selected_event_id' => $_GET['event_id'] ?? null
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/registrations/create');
        }

        $db = Database::getInstance()->getConnection();
        
        $eventId = (int)($_POST['event_id'] ?? 0);
        
        $stmt = $db->prepare("SELECT id, branch_id FROM events WHERE id = ?");
        $stmt->execute([$eventId]);
        $event = $stmt->fetch();
        if (!$event || !BranchScope::canAccess($event['branch_id'])) {
            $_SESSION['error'] = 'Selected event was not found for your branch.';
            $this->redirect('/admin/registrations/create');
        }

        // Core Identity
        $first_name = trim($_POST['first_name'] ?? '');
        $middle_name = trim($_POST['middle_name'] ?? '') ?: null;
        $last_name = trim($_POST['last_name'] ?? '');
        $gender = in_array($_POST['gender'] ?? null, ['Male', 'Female'], true) ? $_POST['gender'] : null;
        $dob = $_POST['dob'] ?? null;
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address_city = trim($_POST['address_city'] ?? '') ?: null;
        $address_state = trim($_POST['address_state'] ?? '') ?: null;
        $address_country = trim($_POST['address_country'] ?? '') ?: null;

        // Church & Role
        $church_name = trim($_POST['church_name'] ?? '') ?: null;
        $church_location = trim($_POST['church_location'] ?? '') ?: null;
        $church_role = trim($_POST['church_role'] ?? '') ?: 'Guest';

        // Conference Specific
        $attendance_mode = 'onsite'; // Manual registration is typically onsite
        $is_first_time = isset($_POST['is_first_time']) ? 1 : 0;
        $referral_source = trim($_POST['referral_source'] ?? '') ?: null;
        $allowedInterests = ['Prayer', 'Leadership', 'Evangelism', 'Worship', 'Business', 'Youth', 'Marriage'];
        $selectedInterests = array_values(array_intersect($_POST['ministry_interests'] ?? [], $allowedInterests));
        $ministry_interests = $selectedInterests ? implode(',', $selectedInterests) : null;
        $prayer_request = trim($_POST['prayer_request'] ?? '') ?: null;

        // Validation
        if ($eventId <= 0 || empty($first_name) || empty($last_name) || empty($phone) || (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL))) {
            $_SESSION['error'] = 'Event, name, phone, and a valid email when provided are required.';
            $this->redirect('/admin/registrations/create?event_id=' . $eventId);
        }

        // Check duplicates (by email if provided, or phone)
        if (!empty($email)) {
            $stmt = $db->prepare("SELECT id FROM registrations WHERE event_id = ? AND email = ?");
            $stmt->execute([$eventId, $email]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Participant already registered with this email.';
                $this->redirect('/admin/registrations/create?event_id=' . $eventId);
            }
        }

        // Generate Code
        $registration_code = strtoupper(substr(md5(uniqid($phone, true)), 0, 8));

        // Insert
        $sql = "INSERT INTO registrations (
            event_id, branch_id, first_name, middle_name, last_name, gender, dob, phone, email, 
            address_city, address_state, address_country, 
            church_name, church_location, church_role, 
            attendance_mode, is_first_time, referral_source, ministry_interests, prayer_request, 
            registration_code, checked_in_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"; // Auto check-in for manual reg
        
        $stmt = $db->prepare($sql);
        $params = [
            $eventId, $event['branch_id'], $first_name, $middle_name, $last_name, $gender, $dob, $phone, $email,
            $address_city, $address_state, $address_country,
            $church_name, $church_location, $church_role,
            $attendance_mode, $is_first_time, $referral_source, $ministry_interests, $prayer_request,
            $registration_code
        ];

        if ($stmt->execute($params)) {
            $_SESSION['success'] = 'Participant registered and checked in successfully.';
            $this->redirect('/admin/registrations?event_id=' . $eventId);
        } else {
            $_SESSION['error'] = 'Registration failed.';
            $this->redirect('/admin/registrations/create?event_id=' . $eventId);
        }
    }

    public function exportPdf() {
        if (!in_array($_SESSION['role_id'], [1, 2, 3, 5, 7])) {
            die("Access Denied");
        }
        $eventId = $_GET['event_id'] ?? null;
        if (!$eventId) die("Event ID required");

        $db = Database::getInstance()->getConnection();
        
        // Fetch Event Info
        $stmt = $db->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$eventId]);
        $event = $stmt->fetch();
        if (!$event || !BranchScope::canAccess($event['branch_id'])) die("Access Denied");

        // Fetch Registrations
        $sql = "SELECT * FROM registrations WHERE event_id = ?";
        $params = [$eventId];
        [$branchWhere, $branchParams] = BranchScope::where();
        if ($branchWhere !== '') {
            $sql .= " AND $branchWhere";
            $params = array_merge($params, $branchParams);
        }
        $sql .= " ORDER BY last_name ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $registrations = $stmt->fetchAll();

        // Generate HTML
        $html = '
        <html>
        <head>
            <style>
                body { font-family: sans-serif; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
                th { background-color: #f2f2f2; }
                .header { text-align: center; margin-bottom: 30px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>Event Registration Report</h2>
                <h3>' . htmlspecialchars($event['title']) . '</h3>
                <p>Date: ' . date('F j, Y', strtotime($event['start_datetime'])) . '</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Mode</th>
                        <th>Status</th>
                        <th>Check-in Time</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($registrations as $i => $reg) {
            $checkIn = $reg['checked_in_at'] ? date('H:i', strtotime($reg['checked_in_at'])) : '-';
            $html .= '<tr>
                <td>' . ($i + 1) . '</td>
                <td>' . htmlspecialchars($reg['first_name'] . ' ' . $reg['last_name']) . '</td>
                <td>' . htmlspecialchars($reg['email']) . '</td>
                <td>' . htmlspecialchars($reg['phone']) . '</td>
                <td>' . ucfirst($reg['attendance_mode']) . '</td>
                <td>' . ucfirst($reg['status']) . '</td>
                <td>' . $checkIn . '</td>
            </tr>';
        }

        $html .= '</tbody></table></body></html>';

        // Render PDF
        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("registrations-{$eventId}.pdf", ["Attachment" => true]);
    }

    public function exportCsv() {
        if (!in_array($_SESSION['role_id'], [1, 2, 3, 5, 7])) {
            die("Access Denied");
        }
        $eventId = $_GET['event_id'] ?? null;
        if (!$eventId) die("Event ID required");

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$eventId]);
        $event = $stmt->fetch();
        if (!$event || !BranchScope::canAccess($event['branch_id'])) die("Access Denied");

        $sql = "SELECT * FROM registrations WHERE event_id = ?";
        $params = [$eventId];
        [$branchWhere, $branchParams] = BranchScope::where();
        if ($branchWhere !== '') {
            $sql .= " AND $branchWhere";
            $params = array_merge($params, $branchParams);
        }
        $sql .= " ORDER BY last_name ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $registrations = $stmt->fetchAll();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="registrations-' . $eventId . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Registration Code', 'First Name', 'Last Name', 'Email', 'Phone', 'Mode', 'Status', 'Checked In At', 'Registered At']);

        foreach ($registrations as $reg) {
            fputcsv($output, [
                $reg['id'],
                $reg['registration_code'],
                $reg['first_name'],
                $reg['last_name'],
                $reg['email'],
                $reg['phone'],
                $reg['attendance_mode'],
                $reg['status'],
                $reg['checked_in_at'],
                $reg['created_at']
            ]);
        }
        fclose($output);
    }

    public function sendReminder() {
        if (!in_array($_SESSION['role_id'], [1, 2, 3, 5, 7])) {
            die("Access Denied");
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $eventId = $_POST['event_id'];
        $message = $_POST['message'];
        $target = $_POST['target']; // all, online, onsite

        $db = Database::getInstance()->getConnection();
        
        // Get Event
        $stmt = $db->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$eventId]);
        $event = $stmt->fetch();
        if (!$event || !BranchScope::canAccess($event['branch_id'])) {
            die("Access Denied");
        }

        // Get Recipients
        $sql = "SELECT * FROM registrations WHERE event_id = ?";
        $params = [$eventId];
        [$branchWhere, $branchParams] = BranchScope::where();
        if ($branchWhere !== '') {
            $sql .= " AND $branchWhere";
            $params = array_merge($params, $branchParams);
        }
        if ($target === 'online') $sql .= " AND attendance_mode = 'online'";
        if ($target === 'onsite') $sql .= " AND attendance_mode = 'onsite'";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $recipients = $stmt->fetchAll();

        $commService = new CommunicationService();
        $subject = "Reminder: " . $event['title'];
        $count = 0;

        foreach ($recipients as $recipient) {
            // Personalize message slightly
            $body = "Dear " . $recipient['first_name'] . ",\n\n" . $message . "\n\nSee you there!\nGHCC Team";
            // Convert newlines to BR for HTML email
            $bodyHtml = nl2br($body);
            
            $commService->sendEmail($recipient['email'], $subject, $bodyHtml, $event['branch_id'] ?? null);
            $count++;
        }

        $_SESSION['success'] = "Reminder sent to $count participants.";
        $this->redirectBack('/admin/registrations');
    }
}
