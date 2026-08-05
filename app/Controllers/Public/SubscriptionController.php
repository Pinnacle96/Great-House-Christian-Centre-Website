<?php
namespace App\Controllers\Public;

use App\Core\Controller;
use App\Models\Newsletter;
use App\Core\Email;
use App\Core\Security;

class SubscriptionController extends Controller {
    
    public function subscribe() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::rateLimit('subscribe:' . Security::clientIp(), 5, 600)) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Too many subscription attempts. Please try again later.']);
                exit;
            }

            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $newsletterModel = new Newsletter();
                if ($newsletterModel->subscribe($email)) {
                    // Send confirmation email
                    Email::send(
                        $email,
                        "Welcome to GHCC Newsletter",
                        "<h1>Welcome!</h1><p>Thank you for subscribing to the Great House Christian Center newsletter. You will now receive updates on our latest news and events.</p>"
                    );
                    
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'success', 'message' => 'Thank you for subscribing!']);
                    exit;
                }
            }
            
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid email address.']);
            exit;
        }
    }
}
