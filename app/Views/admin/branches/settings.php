<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="px-6 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Branch Settings</h1>
        <p class="text-gray-600">Manage payment, email, SMS, and giving details for <?= htmlspecialchars($branch['name'] ?? 'your branch') ?>.</p>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert-auto-dismiss bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded mb-6">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded mb-6">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form action="<?= APP_URL ?>/admin/branch-settings/update" method="POST" class="space-y-6">
        <?= \App\Core\Security::csrfField() ?>

        <section class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <div class="mb-5 flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-brand-green-50 text-brand-green-700 flex items-center justify-center">
                    <i class="fas fa-location-dot"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Branch Contact</h2>
                    <p class="text-sm text-gray-500">Used as sender identity and public branch contact where applicable.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Branch Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($branch['email'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Branch Phone</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($branch['phone'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                </div>
            </div>
        </section>

        <section class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <div class="mb-5 flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                    <i class="fas fa-building-columns"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Bank Transfer Details</h2>
                    <p class="text-sm text-gray-500">Shown on the branch giving page for direct deposits and transfers.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
        </section>

        <section class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <div class="mb-5 flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Paystack</h2>
                    <p class="text-sm text-gray-500">Online gifts and donations for this branch will use these keys when configured.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Public Key</label>
                    <input type="text" name="paystack_public_key" value="<?= htmlspecialchars($branch['paystack_public_key'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Secret Key</label>
                    <input type="password" name="paystack_secret_key" value="" placeholder="<?= !empty($branch['paystack_secret_key']) ? 'Configured - leave blank to keep current key' : 'Enter Paystack secret key' ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                </div>
            </div>

            <button type="submit" formaction="<?= APP_URL ?>/admin/branch-settings/test-paystack" formmethod="POST" class="mt-4 inline-flex items-center px-5 py-3 rounded-lg border border-brand-green text-brand-green font-bold hover:bg-brand-green hover:text-white transition-colors">
                <i class="fas fa-plug mr-2"></i> Test Paystack
            </button>
        </section>

        <section class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <div class="mb-5 flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-purple-50 text-purple-700 flex items-center justify-center">
                    <i class="fas fa-envelope"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">SMTP Email</h2>
                    <p class="text-sm text-gray-500">Emails sent by this branch can use its own mailbox and sender identity.</p>
                </div>
            </div>

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
                    <input type="password" name="smtp_pass" value="" placeholder="<?= !empty($branch['smtp_pass']) ? 'Configured - leave blank to keep current password' : 'Enter SMTP password' ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                </div>
            </div>

            <button type="submit" formaction="<?= APP_URL ?>/admin/branch-settings/test-email" formmethod="POST" class="mt-4 inline-flex items-center px-5 py-3 rounded-lg border border-brand-green text-brand-green font-bold hover:bg-brand-green hover:text-white transition-colors">
                <i class="fas fa-envelope-open-text mr-2"></i> Send Test Email
            </button>
        </section>

        <section class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <div class="mb-5 flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                    <i class="fas fa-sms"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">SMS Gateway</h2>
                    <p class="text-sm text-gray-500">Branch SMS broadcasts use this provider when configured.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Provider</label>
                    <select name="sms_provider" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                        <option value="" <?= empty($branch['sms_provider']) ? 'selected' : '' ?>>Disabled</option>
                        <option value="termii" <?= ($branch['sms_provider'] ?? '') === 'termii' ? 'selected' : '' ?>>Termii</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sender ID</label>
                    <input type="text" name="sms_sender_id" value="<?= htmlspecialchars($branch['sms_sender_id'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent" placeholder="e.g. GHCC">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                    <input type="password" name="sms_api_key" value="" placeholder="<?= !empty($branch['sms_api_key']) ? 'Configured - leave blank to keep current key' : 'Enter SMS API key' ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                </div>
            </div>

            <button type="submit" formaction="<?= APP_URL ?>/admin/branch-settings/test-sms" formmethod="POST" class="mt-4 inline-flex items-center px-5 py-3 rounded-lg border border-brand-green text-brand-green font-bold hover:bg-brand-green hover:text-white transition-colors">
                <i class="fas fa-paper-plane mr-2"></i> Send Test SMS
            </button>
        </section>

        <div class="sticky bottom-0 -mx-6 border-t border-gray-200 bg-white/95 px-6 py-4 backdrop-blur">
            <button type="submit" class="inline-flex items-center rounded-lg bg-brand-green px-8 py-3 font-bold text-white shadow-md transition-colors hover:bg-brand-green-dark">
                <i class="fas fa-save mr-2"></i> Save Branch Settings
            </button>
        </div>
    </form>
</div>

<?php require_once 'app/Views/layouts/admin_footer.php'; ?>
