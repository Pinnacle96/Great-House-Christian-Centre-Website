<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - <?= htmlspecialchars($settings['site_name'] ?? APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/css/style.css">
</head>
<body class="bg-gray-50 min-h-screen text-gray-900">
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="<?= APP_URL ?>" class="flex items-center gap-3">
                <img src="<?= APP_URL ?>/<?= $settings['site_logo'] ?? 'assets/logo/ghcc_logo.png' ?>" class="h-10" alt="GHCC">
                <div>
                    <div class="font-black text-brand-green tracking-tight">GHCC</div>
                    <div class="text-xs text-gray-500 uppercase tracking-widest">Member Portal</div>
                </div>
            </a>
            <div class="flex items-center gap-4">
                <span class="hidden sm:inline text-sm text-gray-600"><?= htmlspecialchars($user['name'] ?? 'Member') ?></span>
                <a href="<?= APP_URL ?>/logout" class="px-4 py-2 rounded-lg bg-brand-green text-white text-sm font-bold hover:bg-green-800">Logout</a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-10">
        <section class="mb-8">
            <h1 class="text-3xl font-black text-gray-900">Welcome, <?= htmlspecialchars($user['name'] ?? 'Member') ?></h1>
            <p class="text-gray-600 mt-2">Your profile, groups, registrations, and giving activity in one place.</p>
        </section>

        <?php if (!$member): ?>
            <div class="mb-8 rounded-xl border border-yellow-200 bg-yellow-50 p-5 text-yellow-900">
                <strong>Profile not linked yet.</strong>
                This user account is not connected to a member record. Ask an administrator to create a member profile with the email
                <span class="font-mono"><?= htmlspecialchars($user['email'] ?? '') ?></span>.
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="text-xs font-bold uppercase tracking-widest text-brand-green mb-3">Profile</div>
                <h2 class="text-xl font-bold text-gray-900">
                    <?= htmlspecialchars($member ? ($member['first_name'] . ' ' . $member['last_name']) : ($user['name'] ?? 'Member')) ?>
                </h2>
                <div class="mt-4 space-y-2 text-sm text-gray-600">
                    <div>Email: <?= htmlspecialchars($user['email'] ?? '-') ?></div>
                    <div>Phone: <?= htmlspecialchars($member['phone'] ?? '-') ?></div>
                    <div>Status: <?= htmlspecialchars($member['status'] ?? 'Account only') ?></div>
                    <div>Membership: <?= htmlspecialchars($member['membership_type'] ?? '-') ?></div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="text-xs font-bold uppercase tracking-widest text-brand-green mb-3">Groups</div>
                <div class="text-3xl font-black text-gray-900"><?= count($groups) ?></div>
                <p class="text-sm text-gray-600 mt-2">Active group or ministry memberships linked to your profile.</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="text-xs font-bold uppercase tracking-widest text-brand-green mb-3">Registrations</div>
                <div class="text-3xl font-black text-gray-900"><?= count($registrations) ?></div>
                <p class="text-sm text-gray-600 mt-2">Recent event registrations under your email address.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <section class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-bold text-gray-900">My Groups</h2>
                </div>
                <div class="divide-y divide-gray-100">
                    <?php if (empty($groups)): ?>
                        <div class="p-6 text-gray-500">No linked groups yet.</div>
                    <?php else: ?>
                        <?php foreach ($groups as $group): ?>
                            <div class="p-6">
                                <div class="font-bold text-gray-900"><?= htmlspecialchars($group['name']) ?></div>
                                <div class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($group['type']) ?> · <?= htmlspecialchars($group['role']) ?></div>
                                <div class="text-sm text-gray-500 mt-2"><?= htmlspecialchars($group['schedule_info'] ?? '-') ?></div>
                                <div class="text-sm text-gray-500"><?= htmlspecialchars($group['location'] ?? '-') ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <section class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-bold text-gray-900">Event Registrations</h2>
                </div>
                <div class="divide-y divide-gray-100">
                    <?php if (empty($registrations)): ?>
                        <div class="p-6 text-gray-500">No event registrations found.</div>
                    <?php else: ?>
                        <?php foreach ($registrations as $registration): ?>
                            <div class="p-6">
                                <div class="font-bold text-gray-900"><?= htmlspecialchars($registration['event_title']) ?></div>
                                <div class="text-sm text-gray-600 mt-1"><?= date('M j, Y g:i A', strtotime($registration['start_datetime'])) ?></div>
                                <div class="text-sm text-gray-500 mt-2">Code: <span class="font-mono"><?= htmlspecialchars($registration['registration_code']) ?></span></div>
                                <div class="text-sm text-gray-500">Status: <?= htmlspecialchars($registration['status']) ?><?= $registration['checked_in_at'] ? ' · Checked in' : '' ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <section class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden lg:col-span-2">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-bold text-gray-900">Giving History</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Fund</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($donations)): ?>
                                <tr><td colspan="4" class="px-6 py-6 text-gray-500">No giving records found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($donations as $donation): ?>
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-600"><?= date('M j, Y', strtotime($donation['donation_date'])) ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($donation['fund_name'] ?? ucfirst($donation['type'])) ?></td>
                                        <td class="px-6 py-4 text-sm font-bold text-gray-900"><?= number_format((float)$donation['amount'], 2) ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars($donation['status']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
