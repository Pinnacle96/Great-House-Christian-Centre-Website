<?php
namespace App\Services;

use App\Models\Setting;

class PaystackService {
    private $secretKey;
    private $baseUrl = 'https://api.paystack.co';

    public function __construct($secretKey = null) {
        $this->secretKey = $secretKey ?: Setting::getSecret('paystack_secret_key', PAYSTACK_SECRET_KEY);
    }

    public function setSecretKey($key) {
        $this->secretKey = $key;
    }

    public function initializeTransaction($email, $amount, $reference, $callbackUrl, $metadata = []) {
        $url = $this->baseUrl . '/transaction/initialize';
        
        $fields = [
            'email' => $email,
            'amount' => $amount * 100, // Convert to kobo/cents
            'reference' => $reference,
            'callback_url' => $callbackUrl,
            'metadata' => json_encode($metadata)
        ];

        return $this->makeRequest('POST', $url, $fields);
    }

    public function verifyTransaction($reference) {
        $url = $this->baseUrl . '/transaction/verify/' . rawurlencode($reference);
        return $this->makeRequest('GET', $url);
    }

    public function testConnection() {
        $url = $this->baseUrl . '/balance';
        return $this->makeRequest('GET', $url);
    }

    private function makeRequest($method, $url, $body = null) {
        if (empty($this->secretKey)) {
            return (object)['status' => false, 'message' => 'Paystack secret key is not configured.'];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $headers = [
            "Authorization: Bearer " . $this->secretKey,
            "Cache-Control: no-cache",
            "Content-Type: application/json"
        ];
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($body) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
            }
        }

        $response = curl_exec($ch);
        $error = curl_error($ch);
        // curl_close($ch); // Not needed in PHP 8.0+ as CurlHandle is automatically closed

        if ($error) {
            return (object)['status' => false, 'message' => $error];
        }

        return json_decode($response);
    }
}
