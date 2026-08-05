<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-800"><?= $title ?></h1>
        <p class="text-gray-600">Update global site settings, branding, and API keys</p>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<form action="<?= APP_URL ?>/admin/settings/update" method="POST" enctype="multipart/form-data">
    
    <!-- Branding Section -->
    <div class="bg-white rounded-xl shadow-md mb-8 overflow-hidden border border-gray-100">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-brand-green uppercase tracking-wider">Site Branding</h2>
        </div>
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-tight">Site Name</label>
                    <input type="text" name="settings[site_name]" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>" 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-transparent outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-tight">Site Email</label>
                    <input type="email" name="settings[site_email]" value="<?= htmlspecialchars($settings['site_email'] ?? '') ?>" 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-transparent outline-none transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-tight">Site Logo</label>
                    <div class="flex items-start space-x-4">
                        <img src="<?= APP_URL ?>/<?= $settings['site_logo'] ?? 'assets/logo/ghcc_logo.png' ?>" class="h-16 object-contain rounded border border-gray-200 p-1">
                        <div class="flex-1">
                            <input type="file" name="files[site_logo]" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-green/10 file:text-brand-green hover:file:bg-brand-green/20">
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-tight">Site Favicon</label>
                    <div class="flex items-start space-x-4">
                        <img src="<?= APP_URL ?>/<?= $settings['site_favicon'] ?? 'assets/logo/ghcc_logo.png' ?>" class="h-16 w-16 object-contain rounded border border-gray-200 p-1">
                        <div class="flex-1">
                            <input type="file" name="files[site_favicon]" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-green/10 file:text-brand-green hover:file:bg-brand-green/20">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Paystack Settings -->
    <div class="bg-white rounded-xl shadow-md mb-8 overflow-hidden border border-gray-100">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex items-center">
            <h2 class="text-lg font-bold text-brand-green uppercase tracking-wider">Paystack Integration</h2>
            <img src="https://paystack.com/assets/img/v3/logo-blue.svg" class="h-4 ml-4">
        </div>
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-tight">Public Key</label>
                    <input type="text" name="settings[paystack_public_key]" value="<?= htmlspecialchars($settings['paystack_public_key'] ?? '') ?>" 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-transparent outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-tight">Secret Key</label>
                    <input type="password" name="settings[paystack_secret_key]" value="" placeholder="<?= !empty($settings['paystack_secret_key']) ? 'Configured - leave blank to keep current key' : 'Enter Paystack secret key' ?>" 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-transparent outline-none transition-all">
                </div>
            </div>
            <div class="pt-2">
                <button type="submit" formaction="<?= APP_URL ?>/admin/settings/test-paystack" formmethod="POST" class="inline-flex items-center px-5 py-3 rounded-lg border border-brand-green text-brand-green font-bold hover:bg-brand-green hover:text-white transition-colors">
                    <i class="fas fa-plug mr-2"></i> Test Paystack Connection
                </button>
            </div>
        </div>
    </div>

    <!-- PHPMailer Settings -->
    <div class="bg-white rounded-xl shadow-md mb-8 overflow-hidden border border-gray-100">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-brand-green uppercase tracking-wider">SMTP Email Settings</h2>
        </div>
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-tight">SMTP Host</label>
                    <input type="text" name="settings[smtp_host]" value="<?= htmlspecialchars($settings['smtp_host'] ?? '') ?>" 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-transparent outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-tight">SMTP Port</label>
                    <input type="text" name="settings[smtp_port]" value="<?= htmlspecialchars($settings['smtp_port'] ?? '') ?>" 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-transparent outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-tight">SMTP Encryption</label>
                    <select name="settings[smtp_encryption]" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-transparent outline-none transition-all">
                        <option value="tls" <?= ($settings['smtp_encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS</option>
                        <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-tight">SMTP Username</label>
                    <input type="text" name="settings[smtp_user]" value="<?= htmlspecialchars($settings['smtp_user'] ?? '') ?>" 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-transparent outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-tight">SMTP Password</label>
                    <input type="password" name="settings[smtp_pass]" value="" placeholder="<?= !empty($settings['smtp_pass']) ? 'Configured - leave blank to keep current password' : 'Enter SMTP password' ?>" 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-transparent outline-none transition-all">
                </div>
            </div>
            <div class="pt-2">
                <button type="submit" formaction="<?= APP_URL ?>/admin/settings/test-email" formmethod="POST" class="inline-flex items-center px-5 py-3 rounded-lg border border-brand-green text-brand-green font-bold hover:bg-brand-green hover:text-white transition-colors">
                    <i class="fas fa-envelope-open-text mr-2"></i> Send SMTP Test Email
                </button>
            </div>
        </div>
    </div>

    <div class="fixed bottom-8 right-8">
        <button type="submit" class="bg-brand-green text-white px-8 py-4 rounded-xl shadow-2xl hover:bg-brand-green-dark transition-all transform hover:scale-105 font-bold flex items-center">
            <i class="fas fa-save mr-2"></i> SAVE GLOBAL SETTINGS
        </button>
    </div>
</form>

<?php require_once 'app/Views/layouts/admin_footer.php'; ?>
