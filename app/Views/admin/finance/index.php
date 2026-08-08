<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="px-6 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Financial Stewardship</h1>
            <p class="text-gray-600">Track giving, manage funds, and view reports</p>
        </div>
        <div class="flex gap-3">
            <a href="<?= APP_URL ?>/admin/finance/create" class="bg-gradient-to-r from-brand-gold-500 to-brand-gold-600 text-white px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center space-x-2">
                <i class="fas fa-hand-holding-usd"></i>
                <span>Record Manual Entry</span>
            </a>
        </div>
    </div>

    <!-- Financial Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- 30 Day Total -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500 uppercase tracking-wider mb-1">Last 30 Days</p>
                    <h3 class="text-3xl font-bold text-gray-800">₦<?= number_format($recentTotal, 2) ?></h3>
                </div>
                <div class="p-3 bg-green-50 rounded-lg text-green-600">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Fund Breakdown -->
        <div class="bg-white rounded-xl shadow-md p-6 lg:col-span-3">
            <h3 class="font-bold text-gray-800 mb-4 text-sm uppercase tracking-wider">Fund Distribution (All Time)</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <?php foreach($fundTotals as $fund): ?>
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <span class="block text-xs text-gray-500 mb-1"><?= $fund['name'] ?></span>
                        <span class="block text-lg font-bold text-gray-800">₦<?= number_format($fund['total'], 2) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Transactions</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Donor</th>
                        <th class="px-6 py-3">Fund</th>
                        <th class="px-6 py-3">Method</th>
                        <th class="px-6 py-3">Amount</th>
                        <th class="px-6 py-3 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($transactions)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No transactions found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($transactions as $trx): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-gray-600">
                                    <?= date('M d, Y', strtotime($trx['donation_date'])) ?>
                                    <span class="block text-xs text-gray-400"><?= date('h:i A', strtotime($trx['donation_date'])) ?></span>
                                </td>
                                <td class="px-6 py-3">
                                    <?php if($trx['member_id']): ?>
                                        <a href="<?= APP_URL ?>/admin/members/show/<?= $trx['member_id'] ?>" class="font-medium text-gray-900 hover:text-brand-green-600">
                                            <?= $trx['first_name'] . ' ' . $trx['last_name'] ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-900 font-medium"><?= $trx['donor_name'] ?: 'Anonymous' ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-3">
                                    <span class="px-2 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-semibold">
                                        <?= $trx['fund_name'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-gray-600 capitalize">
                                    <?= $trx['payment_method'] ?: 'Card' ?>
                                </td>
                                <td class="px-6 py-3 font-bold text-gray-800">
                                    ₦<?= number_format($trx['amount'], 2) ?>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <?php if($trx['status'] === 'successful'): ?>
                                        <span class="text-green-600 font-medium text-xs bg-green-50 px-2 py-1 rounded-full">
                                            Success
                                        </span>
                                    <?php elseif($trx['status'] === 'failed'): ?>
                                        <span class="text-red-600 font-medium text-xs bg-red-50 px-2 py-1 rounded-full">
                                            Failed
                                        </span>
                                    <?php else: ?>
                                        <span class="text-yellow-600 font-medium text-xs bg-yellow-50 px-2 py-1 rounded-full">
                                            Pending
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php require 'app/Views/partials/pagination.php'; ?>
    </div>
</div>

<?php require_once 'app/Views/layouts/admin_footer.php'; ?>
