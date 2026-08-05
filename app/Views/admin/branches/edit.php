<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="px-6 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Branch</h1>
            <p class="text-gray-600">Update branch details and assignment.</p>
        </div>
        <a href="<?= APP_URL ?>/admin/branches" class="bg-gray-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-gray-700 transition-all duration-200 flex items-center space-x-2">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Branches</span>
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6">
        <form action="<?= APP_URL ?>/admin/branches/update/<?= $branch['id'] ?>" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Branch Name *</label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($branch['name']) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                </div>
                <?php if ($isSuperAdmin): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Branch Pastor / Lead User</label>
                        <select name="pastor_user_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                            <option value="">Not assigned</option>
                            <?php foreach ($pastors as $pastor): ?>
                                <option value="<?= $pastor['id'] ?>" <?= (int)($branch['pastor_user_id'] ?? 0) === (int)$pastor['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($pastor['name'] . ' - ' . $pastor['email']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pastor / Contact Name</label>
                    <input type="text" name="pastor_name" value="<?= htmlspecialchars($branch['pastor_name'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($branch['phone'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($branch['email'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <textarea name="address" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent"><?= htmlspecialchars($branch['address'] ?? '') ?></textarea>
                </div>
                <?php if ($isSuperAdmin): ?>
                    <div class="md:col-span-2">
                        <label class="inline-flex items-center gap-3">
                            <input type="checkbox" name="is_active" value="1" <?= $branch['is_active'] ? 'checked' : '' ?> class="rounded border-gray-300 text-brand-green focus:ring-brand-green">
                            <span class="text-sm font-medium text-gray-700">Branch is active</span>
                        </label>
                    </div>
                <?php endif; ?>
            </div>

            <div class="border-t border-gray-100 pt-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Branch Payment Settings</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Paystack Public Key</label>
                        <input type="text" name="paystack_public_key" value="<?= htmlspecialchars($branch['paystack_public_key'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Paystack Secret Key</label>
                        <input type="password" name="paystack_secret_key" value="" placeholder="<?= !empty($branch['paystack_secret_key']) ? 'Configured - leave blank to keep current key' : 'Enter branch Paystack secret key' ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bank Name</label>
                        <input type="text" name="bank_name" value="<?= htmlspecialchars($branch['bank_name'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Account Number</label>
                        <input type="text" name="bank_account_number" value="<?= htmlspecialchars($branch['bank_account_number'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Account Name</label>
                        <input type="text" name="bank_account_name" value="<?= htmlspecialchars($branch['bank_account_name'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                    </div>
                </div>
                <button type="submit" formaction="<?= APP_URL ?>/admin/branches/test-paystack/<?= $branch['id'] ?>" formmethod="POST" class="mt-4 inline-flex items-center px-5 py-3 rounded-lg border border-brand-green text-brand-green font-bold hover:bg-brand-green hover:text-white transition-colors">
                    <i class="fas fa-plug mr-2"></i> Test Branch Paystack
                </button>
            </div>

            <div class="border-t border-gray-100 pt-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Branch SMTP Settings</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Host</label>
                        <input type="text" name="smtp_host" value="<?= htmlspecialchars($branch['smtp_host'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Port</label>
                        <input type="text" name="smtp_port" value="<?= htmlspecialchars($branch['smtp_port'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Encryption</label>
                        <select name="smtp_encryption" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                            <option value="">Use global default</option>
                            <option value="tls" <?= ($branch['smtp_encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS</option>
                            <option value="ssl" <?= ($branch['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Username</label>
                        <input type="text" name="smtp_user" value="<?= htmlspecialchars($branch['smtp_user'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Password</label>
                        <input type="password" name="smtp_pass" value="" placeholder="<?= !empty($branch['smtp_pass']) ? 'Configured - leave blank to keep current password' : 'Enter branch SMTP password' ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                    </div>
                </div>
                <button type="submit" formaction="<?= APP_URL ?>/admin/branches/test-email/<?= $branch['id'] ?>" formmethod="POST" class="mt-4 inline-flex items-center px-5 py-3 rounded-lg border border-brand-green text-brand-green font-bold hover:bg-brand-green hover:text-white transition-colors">
                    <i class="fas fa-envelope-open-text mr-2"></i> Send Branch SMTP Test
                </button>
            </div>

            <div class="pt-6 border-t border-gray-100">
                <button type="submit" class="bg-brand-green text-white px-8 py-3 rounded-lg shadow-md hover:bg-brand-green-dark transition-all duration-200 font-bold">
                    Save Branch
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'app/Views/layouts/admin_footer.php'; ?>
