<?php
namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Security;
use App\Services\CommunicationService;

class EventController extends Controller {
    
    public function registrationForm($slug) {
        $db = Database::getInstance()->getConnection();
        
        // Fetch event details
        $stmt = $db->prepare("SELECT * FROM events WHERE slug = ?");
        $stmt->execute([$slug]);
        $event = $stmt->fetch();

        if (!$event) {
            header('Location: ' . APP_URL . '/events');
            exit;
        }

        // Check if registration is open
        if (!$event['requires_registration']) {
            $_SESSION['info'] = 'Registration is not required for this event.';
            header('Location: ' . APP_URL . '/events/' . $event['slug']);
            exit;
        }

        $this->view('public/event_registration', [
            'title' => 'Register for ' . $event['title'],
            'event' => $event
        ]);
    }

    public function register($slug) {
        if (!Security::rateLimit('event_register:' . Security::clientIp(), 10, 600)) {
            $_SESSION['error'] = 'Too many registration attempts. Please wait a few minutes and try again.';
            header('Location: ' . APP_URL . '/events/' . $slug . '/register');
            exit;
        }

        $db = Database::getInstance()->getConnection();

        // Check registration requirement again
        $stmt = $db->prepare("SELECT * FROM events WHERE slug = ?");
        $stmt->execute([$slug]);
        $event = $stmt->fetch();

        if (!$event) {
            header('Location: ' . APP_URL . '/events');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/events/' . $event['slug']);
            exit;
        }

        if (!$event['requires_registration']) {
            $_SESSION['error'] = 'Registration is not available for this event.';
            header('Location: ' . APP_URL . '/events/' . $event['slug']);
            exit;
        }

        $eventId = $event['id'];

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
        $attendance_mode = in_array($_POST['attendance_mode'] ?? 'onsite', ['online', 'onsite'], true) ? $_POST['attendance_mode'] : 'onsite';
        $is_first_time = isset($_POST['is_first_time']) ? 1 : 0;
        $referral_source = trim($_POST['referral_source'] ?? '') ?: null;
        
        $allowedInterests = ['Prayer', 'Leadership', 'Evangelism', 'Worship', 'Business', 'Youth', 'Marriage'];
        $selectedInterests = array_values(array_intersect($_POST['ministry_interests'] ?? [], $allowedInterests));
        $ministry_interests = $selectedInterests ? implode(',', $selectedInterests) : null;
        $prayer_request = trim($_POST['prayer_request'] ?? '') ?: null;
        
        // Simple validation
        if (empty($first_name) || empty($last_name) || empty($phone) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Please fill in all required fields with a valid email address.';
            header('Location: ' . APP_URL . '/events/' . $event['slug'] . '/register');
            exit;
        }

        // Check if already registered
        $stmt = $db->prepare("SELECT id FROM registrations WHERE event_id = ? AND email = ?");
        $stmt->execute([$eventId, $email]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'You are already registered for this event with this email address.';
            header('Location: ' . APP_URL . '/events/' . $event['slug'] . '/register');
            exit;
        }

        // Generate Registration Code (Unique 8 chars)
        $registration_code = strtoupper(substr(md5(uniqid($email, true)), 0, 8));

        // Insert Registration
        $sql = "INSERT INTO registrations (
            event_id, branch_id, first_name, middle_name, last_name, gender, dob, phone, email, 
            address_city, address_state, address_country, 
            church_name, church_location, church_role, 
            attendance_mode, is_first_time, referral_source, ministry_interests, prayer_request, 
            registration_code
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($sql);
        $params = [
            $eventId, $event['branch_id'], $first_name, $middle_name, $last_name, $gender, $dob, $phone, $email,
            $address_city, $address_state, $address_country,
            $church_name, $church_location, $church_role,
            $attendance_mode, $is_first_time, $referral_source, $ministry_interests, $prayer_request,
            $registration_code
        ];

        if ($stmt->execute($params)) {
            
            // Send Confirmation Email
            $this->sendConfirmationEmail($eventId, $email, $first_name, $registration_code, $attendance_mode);

            $_SESSION['success'] = 'Registration successful! Check your email for your ticket.';
            header('Location: ' . APP_URL . '/events/' . $event['slug'] . '?registered=true');
        } else {
            $_SESSION['error'] = 'Registration failed. Please try again.';
            header('Location: ' . APP_URL . '/events/' . $event['slug'] . '/register');
        }
    }

    private function sendConfirmationEmail($eventId, $email, $name, $code, $mode) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$eventId]);
        $event = $stmt->fetch();

        $subject = "Registration Confirmed: " . $event['title'];
        
        // Generate QR Code URL (using Google Charts API for simplicity)
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . $code;

        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 10px; overflow: hidden; }
                .header { bg-color: #047857; background: #047857; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .ticket { border: 2px dashed #047857; padding: 20px; margin: 20px 0; background: #f9f9f9; text-align: center; }
                .footer { background: #f4f4f4; padding: 10px; text-align: center; font-size: 12px; }
                .btn { display: inline-block; padding: 10px 20px; background: #047857; color: white; text-decoration: none; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Registration Confirmed!</h1>
                </div>
                <div class='content'>
                    <p>Dear $name,</p>
                    <p>You have successfully registered for <strong>{$event['title']}</strong>.</p>
                    
                    <div class='ticket'>
                        <h3>YOUR TICKET</h3>
                        <p><strong>Event:</strong> {$event['title']}</p>
                        <p><strong>Date:</strong> " . date('F j, Y', strtotime($event['start_datetime'])) . "</p>
                        <p><strong>Location:</strong> {$event['location']} ($mode)</p>
                        <p><strong>Registration Code:</strong> <span style='font-size: 18px; font-weight: bold;'>$code</span></p>
                        <br>
                        <img src='$qrUrl' alt='QR Code' />
                        <p><small>Show this QR code at the entrance for quick check-in.</small></p>
                    </div>

                    <p>We look forward to seeing you!</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Great House Christian Centre</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $commService = new CommunicationService();
        $commService->sendEmail($email, $subject, $body, $event['branch_id'] ?? null);
    }
}
