<?php
namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Models\Branch;
use App\Models\Setting;

class CommunicationService {
    
    public function sendEmail($to, $subject, $body, $branchId = null) {
        $mail = new PHPMailer(true);

        try {
            $host = Setting::get('smtp_host', MAIL_HOST);
            $user = Setting::get('smtp_user', MAIL_USER);
            $pass = Setting::getSecret('smtp_pass', MAIL_PASS);
            $port = (int)Setting::get('smtp_port', MAIL_PORT);
            $encryption = Setting::get('smtp_encryption', 'tls');
            $from = Setting::get('site_email', MAIL_FROM);
            $fromName = Setting::get('site_name', MAIL_FROM_NAME);

            if ($branchId) {
                $branchModel = new Branch();
                $branchConfig = $branchModel->smtpConfig($branchId);
                if (!empty($branchConfig['host']) && !empty($branchConfig['user']) && !empty($branchConfig['pass'])) {
                    $host = $branchConfig['host'];
                    $user = $branchConfig['user'];
                    $pass = $branchConfig['pass'];
                    $port = $branchConfig['port'] ?: $port;
                    $encryption = in_array($branchConfig['encryption'], ['tls', 'ssl'], true) ? $branchConfig['encryption'] : $encryption;
                    $from = filter_var($branchConfig['from'], FILTER_VALIDATE_EMAIL) ? $branchConfig['from'] : $from;
                    $fromName = $branchConfig['from_name'] ?: $fromName;
                }
            }

            if (!$host || !$user || !$pass || !$from) {
                $this->log("EMAIL CONFIG ERROR: SMTP settings are incomplete.");
                return false;
            }

            // Server settings
            $mail->isSMTP();
            $mail->Host       = $host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $user;
            $mail->Password   = $pass;
            $mail->SMTPSecure = $encryption === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $port;

            // Recipients
            $mail->setFrom($from, $fromName);
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            $this->log("EMAIL SENT TO: $to | SUBJECT: $subject");
            return true;
        } catch (Exception $e) {
            $this->log("EMAIL ERROR: {$mail->ErrorInfo} | TO: $to");
            return false;
        }
    }

    public function sendSMS($to, $message) {
        // Placeholder for SMS logic (Twilio/Termii/Vonage)
        // Ensure phone number format is correct (e.g., E.164)
        // Simulate success
        $this->log("SMS TO: $to | MSG: $message");
        return true;
    }

    private function log($message) {
        $logDir = __DIR__ . '/../../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $logMessage = "[" . date('Y-m-d H:i:s') . "] " . $message . "\n";
        file_put_contents($logDir . '/communication.log', $logMessage, FILE_APPEND);
    }
}
