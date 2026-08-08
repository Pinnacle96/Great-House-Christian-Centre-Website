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

    public function sendSMS($to, $message, $branchId = null) {
        $provider = '';
        $senderId = '';
        $apiKey = '';

        if ($branchId) {
            $branchModel = new Branch();
            $config = $branchModel->smsConfig($branchId);
            $provider = $config['provider'] ?? '';
            $senderId = $config['sender_id'] ?? '';
            $apiKey = $config['api_key'] ?? '';
        }

        if ($provider !== 'termii' || $senderId === '' || $apiKey === '') {
            $this->log("SMS CONFIG ERROR: SMS settings are incomplete for branch " . ($branchId ?: 'global') . ".");
            return false;
        }

        if (!function_exists('curl_init')) {
            $this->log("SMS ERROR: cURL is not available on this server.");
            return false;
        }

        $payload = json_encode([
            'api_key' => $apiKey,
            'to' => $to,
            'from' => $senderId,
            'sms' => $message,
            'type' => 'plain',
            'channel' => 'generic',
        ]);

        $ch = curl_init('https://api.ng.termii.com/api/sms/send');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => 20,
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $statusCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $statusCode < 200 || $statusCode >= 300) {
            $this->log("SMS ERROR: HTTP $statusCode | $error | TO: $to | RESPONSE: " . substr((string)$response, 0, 500));
            return false;
        }

        $this->log("SMS SENT TO: $to | PROVIDER: termii");
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
