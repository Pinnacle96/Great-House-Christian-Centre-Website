<?php
namespace App\Controllers\Admin;

use App\Core\BranchScope;
use App\Core\Controller;
use App\Core\Database;
use App\Models\Donation;
use App\Models\Fund;
use App\Models\Member;

class FinanceController extends Controller {

    public function __construct() {
        $this->requireRoles([1, 2, 7]);
    }

    public function index() {
        $donationModel = new Donation();
        $pagination = $this->paginationParams(15);
        $totalTransactions = $donationModel->countAllWithDetails();
        $pagination = $this->paginationMeta($totalTransactions, $pagination, 'transactions');
        $transactions = $donationModel->findAllWithDetails($pagination['per_page'], $pagination['offset']);
        $recentTotal = $donationModel->getRecentTotal(30);
        $fundTotals = $donationModel->getTotalByFund();
        $monthlyStats = $donationModel->getMonthlyStats();

        $this->view('admin/finance/index', [
            'title' => 'Financial Stewardship',
            'transactions' => $transactions,
            'recentTotal' => $recentTotal,
            'fundTotals' => $fundTotals,
            'monthlyStats' => $monthlyStats,
            'pagination' => $pagination
        ]);
    }

    public function create() {
        $fundModel = new Fund();
        $funds = $fundModel->where('is_active', 1);
        
        $memberModel = new Member();
        $members = $memberModel->findAll();

        $this->view('admin/finance/create', [
            'title' => 'Record Transaction',
            'funds' => $funds,
            'members' => $members,
            'branches' => BranchScope::branchOptions(Database::getInstance()->getConnection()),
            'selectedBranchId' => BranchScope::currentBranchId()
        ]);
    }

    public function store() {
        $amount = (float)($_POST['amount'] ?? 0);
        $fundId = (int)($_POST['fund_id'] ?? 0);
        $paymentMethod = $_POST['payment_method'] ?? '';
        $branchId = BranchScope::isSuperAdmin()
            ? (int)($_POST['branch_id'] ?? 0)
            : (int)BranchScope::currentBranchId();

        if ($amount <= 0 || $fundId <= 0 || !in_array($paymentMethod, ['card', 'cash', 'check', 'transfer'], true)) {
            $_SESSION['error'] = 'Please enter a valid amount, fund, and payment method.';
            $this->redirect('/admin/finance/create');
        }
        if ($branchId <= 0) {
            $_SESSION['error'] = 'Please select a branch for this transaction.';
            $this->redirect('/admin/finance/create');
        }

        $fundModel = new Fund();
        $fund = $fundModel->find($fundId);
        if (!$fund) {
            $_SESSION['error'] = 'Selected fund was not found.';
            $this->redirect('/admin/finance/create');
        }
        $fundName = $fund['name'] ?? 'Offering';

        $data = [
            'amount' => $amount,
            'fund_id' => $fundId,
            'type' => $this->donationTypeForFund($fundName),
            'member_id' => !empty($_POST['member_id']) ? $_POST['member_id'] : null,
            'donor_name' => !empty($_POST['donor_name']) ? $_POST['donor_name'] : 'Anonymous',
            'donor_email' => $_POST['donor_email'] ?? null,
            'payment_method' => $paymentMethod,
            'transaction_id' => 'MANUAL-' . time(), // Manual entry ID
            'status' => 'successful', // Manual entries are assumed successful/collected
            'notes' => $_POST['notes'],
            'donation_date' => $_POST['donation_date'] . ' ' . date('H:i:s')
        ];

        // If member selected, get name/email from member record if empty
        if ($data['member_id']) {
            $memberModel = new Member();
            $member = $memberModel->find($data['member_id']);
            if ($member) {
                $branchId = (int)($member['branch_id'] ?? $branchId);
                if (empty($_POST['donor_name'])) $data['donor_name'] = $member['first_name'] . ' ' . $member['last_name'];
                if (empty($_POST['donor_email'])) $data['donor_email'] = $member['email'];
            }
        }
        $data['branch_id'] = $branchId;

        $donationModel = new Donation();
        if ($donationModel->create($data)) {
            $this->redirect('/admin/finance');
        } else {
            die("Error recording transaction");
        }
    }

    private function donationTypeForFund($fundName) {
        $name = strtolower($fundName);
        if (strpos($name, 'tithe') !== false) return 'tithe';
        if (strpos($name, 'seed') !== false) return 'seed';
        if (strpos($name, 'partner') !== false) return 'partnership';
        return 'offering';
    }
}
