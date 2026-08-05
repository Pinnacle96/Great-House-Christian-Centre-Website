<?php
namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Security;
use App\Models\Donation;
use App\Models\Fund;
use App\Models\Setting;
use App\Models\Branch;
use App\Services\PaystackService;

class GiveController extends Controller {
    
    public function index() {
        $branchModel = new Branch();
        $this->renderGiving(null, $branchModel->headquarters());
    }

    public function branch($slug) {
        $branchModel = new Branch();
        $branch = $branchModel->findBySlug($slug);
        if (!$branch) {
            $this->redirect('/give');
        }

        $this->renderGiving($branch);
    }

    private function renderGiving($branch = null, $paymentBranch = null) {
        $content = \App\Models\PageContent::getPageContent('give');
        
        // Fetch funds dynamically
        $fundModel = new Fund();
        $funds = $fundModel->where('is_active', 1);

        $this->view('public/give', [
            'title' => $branch ? 'Give - ' . $branch['name'] : 'Give',
            'content' => $content,
            'funds' => $funds,
            'branch' => $branch,
            'paymentBranch' => $paymentBranch,
            'formAction' => $branch ? APP_URL . '/branches/' . $branch['slug'] . '/give/process' : APP_URL . '/give/process'
        ]);
    }

    public function process() {
        $branchModel = new Branch();
        $branch = $branchModel->find($this->defaultBranchId());
        $this->processForBranch($branch, '/give');
    }

    public function branchProcess($slug) {
        $branchModel = new Branch();
        $branch = $branchModel->findBySlug($slug);
        if (!$branch) {
            $this->redirect('/give');
        }

        $this->processForBranch($branch, '/branches/' . $branch['slug'] . '/give');
    }

    private function processForBranch($branch, $returnPath) {
        if (!Security::rateLimit('give:' . Security::clientIp(), 10, 600)) {
            $_SESSION['error'] = 'Too many giving attempts. Please wait a few minutes and try again.';
            $this->redirect($returnPath);
        }

        $fundId = (int)($_POST['fund_id'] ?? 0);
        $amount = (float)($_POST['amount'] ?? 0);
        $email = trim($_POST['email'] ?? '');
        $name = trim($_POST['name'] ?? '') ?: 'Anonymous';

        if ($fundId <= 0 || $amount < 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Please choose a fund, enter a valid email, and give at least 100.';
            $this->redirect($returnPath);
        }

        // Get Fund Name
        $fundModel = new Fund();
        $fund = $fundModel->find($fundId);
        if (!$fund || empty($fund['is_active'])) {
            $_SESSION['error'] = 'The selected giving fund is not available.';
            $this->redirect($returnPath);
        }
        $fundName = $fund ? $fund['name'] : 'General';
        $branchId = (int)($branch['id'] ?? $this->defaultBranchId());

        // Generate Transaction Reference
        $reference = 'GHCC-' . time() . '-' . mt_rand(1000, 9999);
        
        // Record pending donation
        $donationModel = new Donation();
        $donationModel->create([
            'branch_id' => $branchId,
            'donor_name' => $name,
            'donor_email' => $email,
            'amount' => $amount,
            'type' => $this->donationTypeForFund($fundName),
            'fund_id' => $fundId,
            'payment_method' => 'card',
            'transaction_id' => $reference,
            'status' => 'pending',
            'donation_date' => date('Y-m-d H:i:s')
        ]);

        // Initialize Paystack Transaction
        $branchModel = new Branch();
        $paymentConfig = $branchModel->paymentConfig($branchId);
        $paystack = new PaystackService($paymentConfig['secret_key'] ?? '');
        
        $callbackUrl = APP_URL . '/give/callback';
        
        $response = $paystack->initializeTransaction($email, $amount, $reference, $callbackUrl, [
            'name' => $name,
            'fund_id' => $fundId,
            'fund_name' => $fundName,
            'branch_id' => $branchId,
            'branch_slug' => $branch['slug'] ?? ''
        ]);

        if ($response && $response->status) {
            $this->redirect($response->data->authorization_url, true);
        } else {
            $_SESSION['error'] = "Payment initialization failed: " . ($response->message ?? 'Unknown error');
            $this->redirect($returnPath);
        }
    }

    public function callback() {
        $reference = $_GET['reference'] ?? null;
        if (!$reference) {
            $this->redirect('/give?status=error');
        }

        $donation = $this->findDonationByReference($reference);
        $branchId = (int)($donation['branch_id'] ?? $this->defaultBranchId());
        $branchModel = new Branch();
        $paymentConfig = $branchModel->paymentConfig($branchId);
        $paystack = new PaystackService($paymentConfig['secret_key'] ?? '');
        $result = $paystack->verifyTransaction($reference);
        $returnPath = $this->givingReturnPath($branchId);

        if ($result && $result->status && $result->data->status === 'success') {
            $donationModel = new Donation();
            $donationModel->updateStatus($reference, 'successful');
            
            // TODO: Send thank you email here

            $this->redirect($returnPath . '?status=success');
        } else {
            $donationModel = new Donation();
            $donationModel->updateStatus($reference, 'failed');
            $this->redirect($returnPath . '?status=failed');
        }
    }

    public function webhook() {
        $payload = file_get_contents('php://input');
        $signature = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? '';

        $event = json_decode($payload);
        if (!$event || empty($event->event) || empty($event->data->reference)) {
            http_response_code(400);
            echo 'Invalid payload';
            return;
        }

        $donation = $this->findDonationByReference($event->data->reference);
        $branchId = (int)($donation['branch_id'] ?? 0);
        $branchModel = new Branch();
        $paymentConfig = $branchId ? $branchModel->paymentConfig($branchId) : [];
        $secretKey = $paymentConfig['secret_key'] ?? Setting::getSecret('paystack_secret_key', PAYSTACK_SECRET_KEY);

        if (empty($secretKey) || !hash_equals(hash_hmac('sha512', $payload, $secretKey), $signature)) {
            http_response_code(401);
            echo 'Invalid signature';
            return;
        }

        if ($event->event === 'charge.success' && ($event->data->status ?? '') === 'success') {
            $donationModel = new Donation();
            $donationModel->updateStatus($event->data->reference, 'successful');
        }

        http_response_code(200);
        echo 'OK';
    }

    private function donationTypeForFund($fundName) {
        $name = strtolower($fundName);
        if (strpos($name, 'tithe') !== false) return 'tithe';
        if (strpos($name, 'seed') !== false) return 'seed';
        if (strpos($name, 'partner') !== false) return 'partnership';
        return 'offering';
    }

    private function defaultBranchId() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT id FROM branches WHERE is_active = 1 AND is_headquarters = 1 LIMIT 1");
        $branchId = $stmt->fetchColumn();
        if (!$branchId) {
            $stmt = $db->query("SELECT id FROM branches WHERE is_active = 1 ORDER BY id ASC LIMIT 1");
            $branchId = $stmt->fetchColumn();
        }
        return $branchId ?: null;
    }

    private function findDonationByReference($reference) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM donations WHERE transaction_id = ? LIMIT 1");
        $stmt->execute([$reference]);
        return $stmt->fetch();
    }

    private function givingReturnPath($branchId) {
        if (!$branchId) {
            return '/give';
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT slug FROM branches WHERE id = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([$branchId]);
        $slug = $stmt->fetchColumn();

        return $slug ? '/branches/' . $slug . '/give' : '/give';
    }
}
