<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="px-6 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Record Transaction</h1>
            <p class="text-gray-600">Manually enter a donation or offering</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="<?= APP_URL ?>/admin/finance" class="bg-gray-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-gray-700 transition-all duration-200 flex items-center space-x-2">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Finance</span>
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="border-b border-gray-200 bg-gray-50 px-8 py-4">
            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                <i class="fas fa-money-bill-wave text-brand-green-600 mr-3"></i>
                Transaction Details
            </h2>
        </div>
        
        <form action="<?= APP_URL ?>/admin/finance/store" method="POST" class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Amount & Fund -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount (₦) *</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="text-gray-500 sm:text-sm">₦</span>
                        </div>
                        <input type="number" name="amount" step="0.01" class="block w-full rounded-lg border-gray-300 pl-8 py-3 focus:border-brand-green-500 focus:ring-brand-green-500 sm:text-sm" placeholder="0.00" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fund Category *</label>
                    <select name="fund_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent" required>
                        <?php foreach($funds as $fund): ?>
                            <option value="<?= $fund['id'] ?>"><?= $fund['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Branch *</label>
                    <select name="branch_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent" required>
                        <?php foreach($branches as $branch): ?>
                            <option value="<?= $branch['id'] ?>" <?= (int)($selectedBranchId ?? 0) === (int)$branch['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($branch['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Date & Method -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Received *</label>
                    <input type="date" name="donation_date" value="<?= date('Y-m-d') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
                    <select name="payment_method" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                        <option value="cash">Cash</option>
                        <option value="card">Card / POS</option>
                        <option value="transfer">Bank Transfer</option>
                        <option value="check">Check</option>
                    </select>
                </div>

                <!-- Donor Info -->
                <div class="md:col-span-2 bg-gray-50 p-6 rounded-lg border border-gray-100">
                    <h3 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wider">Donor Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Link to Member (Optional)</label>
                            <select name="member_id" id="memberSelect" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                                <option value="">-- Anonymous / Guest --</option>
                                <?php foreach($members as $member): ?>
                                    <option value="<?= $member['id'] ?>"><?= $member['first_name'] . ' ' . $member['last_name'] ?> (<?= $member['email'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Selecting a member will auto-fill their details for records.</p>
                        </div>

                        <div id="manualDonorFields" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Donor Name</label>
                                <input type="text" name="donor_name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent" placeholder="John Doe">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email (for receipt)</label>
                                <input type="email" name="donor_email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent" placeholder="john@example.com">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent" placeholder="Check number, envelope number, or special instructions..."></textarea>
                </div>
            </div>

            <div class="flex justify-end pt-8 border-t border-gray-100">
                <button type="submit" class="bg-gradient-to-r from-brand-green-600 to-brand-green-700 text-white px-8 py-4 rounded-lg shadow-lg hover:from-brand-green-700 hover:to-brand-green-800 transition-all duration-200 flex items-center space-x-2 transform hover:-translate-y-1">
                    <i class="fas fa-save fa-lg"></i>
                    <span class="font-bold text-lg">Record Transaction</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Simple logic to toggle manual fields if member is selected (optional visual enhancement)
    document.getElementById('memberSelect').addEventListener('change', function() {
        const manualFields = document.getElementById('manualDonorFields');
        if(this.value) {
            manualFields.classList.add('opacity-50', 'pointer-events-none');
        } else {
            manualFields.classList.remove('opacity-50', 'pointer-events-none');
        }
    });
</script>

<?php require_once 'app/Views/layouts/admin_footer.php'; ?>
