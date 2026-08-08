<?php
/**
 * @var array $branches      Branch rows for the current admin scope.
 * @var bool  $isSuperAdmin  Whether the current user is a super admin.
 */
require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="px-6 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Branches</h1>
            <p class="text-gray-600">Manage branch registration links, QR codes, and branch assignments.</p>
        </div>
        <?php if ($isSuperAdmin): ?>
            <a href="<?= APP_URL ?>/admin/branches/create" class="bg-brand-green text-white px-6 py-3 rounded-lg shadow-md hover:bg-brand-green-dark transition-all duration-200 flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Add Branch</span>
            </a>
        <?php endif; ?>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded mb-6">
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

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <?php foreach ($branches as $branch): ?>
            <?php
                $registrationLink = APP_URL . '/b/' . $branch['registration_token'] . '/register';
                $givingLink = APP_URL . '/branches/' . $branch['slug'] . '/give';
                $publicLink = APP_URL . '/branches/' . $branch['slug'];
                $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . urlencode($registrationLink);
            ?>
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($branch['name']) ?></h2>
                            <span class="px-2 py-1 rounded-full text-xs font-bold <?= $branch['is_active'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' ?>">
                                <?= $branch['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                            <?php if (!empty($branch['is_headquarters'])): ?>
                                <span class="px-2 py-1 rounded-full text-xs font-bold bg-brand-gold/20 text-brand-green">
                                    Headquarters
                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($branch['address'] ?: 'No address set') ?></p>
                    </div>
                    <a href="<?= APP_URL ?>/admin/branches/edit/<?= $branch['id'] ?>" class="text-brand-green hover:text-brand-green-dark bg-brand-green-50 p-2 rounded-lg" title="Edit Branch">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-[220px_1fr] gap-6">
                    <div class="bg-gray-50 border border-gray-100 rounded-lg p-4 flex items-center justify-center">
                        <img src="<?= $qrUrl ?>" alt="<?= htmlspecialchars($branch['name']) ?> registration QR code" class="w-[180px] h-[180px] max-w-full">
                    </div>
                    <div class="space-y-4 min-w-0">
                        <div>
                            <div class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Member Registration Link</div>
                            <div class="flex flex-col sm:flex-row gap-2 min-w-0">
                                <input type="text" readonly value="<?= htmlspecialchars($registrationLink) ?>" class="branch-link flex-1 min-w-0 w-full px-4 py-3 border border-gray-300 rounded-lg text-sm bg-gray-50">
                                <button type="button" onclick="copyBranchLink(this)" class="shrink-0 px-4 py-3 rounded-lg bg-gray-900 text-white font-bold text-sm">
                                    Copy
                                </button>
                            </div>
                        </div>

                        <div>
                            <div class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Branch Giving Link</div>
                            <div class="flex flex-col sm:flex-row gap-2 min-w-0">
                                <input type="text" readonly value="<?= htmlspecialchars($givingLink) ?>" class="branch-link flex-1 min-w-0 w-full px-4 py-3 border border-gray-300 rounded-lg text-sm bg-gray-50">
                                <button type="button" onclick="copyBranchLink(this)" class="shrink-0 px-4 py-3 rounded-lg bg-gray-900 text-white font-bold text-sm">
                                    Copy
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-lg bg-gray-50 p-4">
                                <div class="text-gray-500 text-xs uppercase font-bold">Members</div>
                                <div class="text-2xl font-black text-gray-900"><?= (int)($branch['active_members'] ?? 0) ?></div>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-4">
                                <div class="text-gray-500 text-xs uppercase font-bold">Users</div>
                                <div class="text-2xl font-black text-gray-900"><?= (int)($branch['assigned_users'] ?? 0) ?></div>
                            </div>
                        </div>

                        <div class="text-sm text-gray-600">
                            <strong>Pastor:</strong> <?= htmlspecialchars(($branch['pastor_name'] ?? '') ?: 'Not assigned') ?>
                        </div>

                        <div class="flex flex-wrap gap-2 text-sm">
                            <a href="<?= htmlspecialchars($publicLink) ?>" class="font-bold text-brand-green hover:underline" target="_blank">Public page</a>
                            <span class="text-gray-300">/</span>
                            <span class="<?= !empty($branch['paystack_secret_key']) ? 'text-green-700' : 'text-gray-500' ?>">
                                Paystack <?= !empty($branch['paystack_secret_key']) ? 'configured' : 'not configured' ?>
                            </span>
                            <span class="text-gray-300">/</span>
                            <span class="<?= !empty($branch['smtp_pass']) ? 'text-green-700' : 'text-gray-500' ?>">
                                SMTP <?= !empty($branch['smtp_pass']) ? 'configured' : 'not configured' ?>
                            </span>
                        </div>

                        <form action="<?= APP_URL ?>/admin/branches/regenerate-token/<?= $branch['id'] ?>" method="POST" onsubmit="return confirm('Regenerate this branch registration link? The old QR code will stop working.')">
                            <button type="submit" class="text-sm font-bold text-red-600 hover:text-red-700">
                                Regenerate registration link
                            </button>
                        </form>

                        <?php if ($isSuperAdmin && empty($branch['is_headquarters']) && !empty($branch['is_active'])): ?>
                            <form action="<?= APP_URL ?>/admin/branches/make-headquarters/<?= $branch['id'] ?>" method="POST" onsubmit="return confirm('Make this branch the headquarters branch? Global giving and default public submissions will use it.')">
                                <button type="submit" class="text-sm font-bold text-brand-green hover:text-brand-green-dark">
                                    Make headquarters
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if ($isSuperAdmin && empty($branch['is_headquarters'])): ?>
                            <form action="<?= APP_URL ?>/admin/branches/delete/<?= $branch['id'] ?>" method="POST" onsubmit="return confirm('Delete this branch? Branches with existing records will be deactivated instead of permanently deleted.')">
                                <button type="submit" class="text-sm font-bold text-red-600 hover:text-red-700">
                                    <?= !empty($branch['is_active']) ? 'Delete / deactivate branch' : 'Delete branch' ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="mt-6 overflow-hidden rounded-xl shadow-md">
        <?php require 'app/Views/partials/pagination.php'; ?>
    </div>
</div>

<script>
function copyBranchLink(button) {
    const input = button.closest('div').querySelector('.branch-link');
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard?.writeText(input.value).then(() => {
        button.textContent = 'Copied';
        setTimeout(() => button.textContent = 'Copy', 1500);
    }).catch(() => {
        document.execCommand('copy');
        button.textContent = 'Copied';
        setTimeout(() => button.textContent = 'Copy', 1500);
    });
}
</script>

<?php require_once 'app/Views/layouts/admin_footer.php'; ?>
