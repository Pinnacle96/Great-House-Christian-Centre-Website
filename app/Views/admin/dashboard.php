<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<!-- Welcome Banner -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Welcome back, <?= htmlspecialchars($user_name) ?>!</h1>
    <p class="text-gray-500">Here's what's happening in your department today.</p>
</div>

<?php if (!empty($stats['branch_summary'])): ?>
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Branch Overview</h2>
                <p class="text-sm text-gray-500">All active branches at a glance.</p>
            </div>
            <a href="<?= APP_URL ?>/admin/branches" class="text-brand-green font-bold text-sm hover:underline">Manage Branches</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Branch</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Members</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Upcoming Events</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Successful Giving</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($stats['branch_summary'] as $branch): ?>
                        <tr>
                            <td class="px-4 py-3 font-bold text-gray-900"><?= htmlspecialchars($branch['name']) ?></td>
                            <td class="px-4 py-3 text-gray-700"><?= (int)$branch['members'] ?></td>
                            <td class="px-4 py-3 text-gray-700"><?= (int)$branch['upcoming_events'] ?></td>
                            <td class="px-4 py-3 text-gray-700">₦<?= number_format((float)$branch['donations'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<!-- REGISTRATION TEAM DASHBOARD -->
<?php if (in_array($role_id, [5, 6])): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Total Events -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 border-l-4 border-l-brand-green">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-brand-green">
                    <i class="fas fa-calendar-alt text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Active Events</h3>
                    <p class="text-3xl font-bold text-gray-900"><?= $stats['total_events'] ?></p>
                </div>
            </div>
        </div>
        
        <!-- Quick Action -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 flex flex-col justify-center items-start">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Quick Actions</h3>
            <a href="<?= APP_URL ?>/admin/registrations" class="bg-brand-green text-white px-4 py-2 rounded-lg hover:bg-brand-green-dark transition-colors w-full text-center">
                <i class="fas fa-clipboard-check mr-2"></i> Manage Registrations
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Upcoming Events List -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Upcoming Events</h3>
            <?php if (!empty($stats['event_attendance'])): ?>
                <div class="space-y-4">
                    <?php foreach ($stats['event_attendance'] as $event): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-bold text-gray-800"><?= htmlspecialchars($event['title']) ?></p>
                                <p class="text-sm text-gray-500">
                                    <i class="far fa-clock mr-1"></i> <?= date('M j, Y @ g:i A', strtotime($event['start_datetime'])) ?>
                                </p>
                            </div>
                            <?php if ($event['requires_registration']): ?>
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-bold">Reg. Req.</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-500">No upcoming events scheduled.</p>
            <?php endif; ?>
        </div>

        <!-- Recent Registrations -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Recent Registrations</h3>
            <?php if (!empty($stats['recent_registrations'])): ?>
                <div class="space-y-4">
                    <?php foreach ($stats['recent_registrations'] as $reg): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-bold text-gray-800"><?= htmlspecialchars($reg['first_name'] . ' ' . $reg['last_name']) ?></p>
                                <p class="text-xs text-gray-500">For: <?= htmlspecialchars($reg['event_title']) ?></p>
                            </div>
                            <span class="text-xs font-mono bg-gray-200 px-2 py-1 rounded"><?= $reg['registration_code'] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-500">No recent registrations.</p>
            <?php endif; ?>
        </div>
    </div>

<!-- ADMIN / PASTOR / LEADER DASHBOARD -->
<?php else: ?>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Members -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Total Members</h3>
                    <p class="text-2xl font-bold text-gray-900"><?= $stats['total_members'] ?></p>
                    <p class="text-sm text-green-600 font-medium">Active Members</p>
                </div>
            </div>
        </div>
        
        <!-- Total Giving (Admin/Pastor Only) -->
        <?php if (in_array($role_id, [1, 2, 7])): ?>
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-hand-holding-usd text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Total Giving</h3>
                    <p class="text-2xl font-bold text-gray-900">₦<?= $stats['total_donations'] ?></p>
                    <p class="text-sm text-green-600 font-medium">Successful Donations</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Total Sermons -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-microphone-alt text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Total Sermons</h3>
                    <p class="text-2xl font-bold text-gray-900"><?= $stats['total_sermons'] ?></p>
                    <p class="text-sm text-green-600 font-medium">Messages Archive</p>
                </div>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-calendar-day text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Upcoming Events</h3>
                    <p class="text-2xl font-bold text-gray-900"><?= $stats['total_events'] ?></p>
                    <p class="text-sm text-green-600 font-medium">Scheduled Events</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        
        <!-- Left Column: Charts & Data -->
        <div class="lg:col-span-2 space-y-8">
            
            <?php if (in_array($role_id, [1, 2, 7])): ?>
            <!-- Monthly Donations Chart -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800">Monthly Giving Trends</h3>
                    <span class="text-sm text-gray-500">Last 6 Months</span>
                </div>
                <div class="h-64">
                    <canvas id="monthlyDonationsChart"></canvas>
                </div>
            </div>

            <!-- Recent Donations List -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800">Recent Donations</h3>
                    <a href="<?= APP_URL ?>/admin/finance" class="text-brand-green hover:text-brand-green-dark text-sm font-medium">View All</a>
                </div>
                <div class="space-y-4">
                    <?php if (!empty($stats['recent_donations'])): ?>
                        <?php foreach ($stats['recent_donations'] as $donation): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-800"><?= htmlspecialchars($donation['donor_name'] ?? 'Anonymous') ?></p>
                                    <p class="text-sm text-gray-500"><?= date('M j, Y', strtotime($donation['donation_date'])) ?></p>
                                </div>
                                <span class="text-green-600 font-bold">₦<?= number_format($donation['amount'], 2) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-4">No donations yet</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Member Growth Chart -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800">Member Growth</h3>
                    <span class="text-sm text-gray-500">Last 12 Months</span>
                </div>
                <div class="h-64">
                    <canvas id="memberGrowthChart"></canvas>
                </div>
            </div>

        </div>

        <!-- Right Column: Sidebar Widgets -->
        <div class="space-y-8">
            
            <!-- Birthday Widget -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-pink-500 to-rose-500 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-birthday-cake mr-2"></i> Upcoming Birthdays
                    </h3>
                </div>
                <div class="p-6">
                    <?php if (!empty($upcomingBirthdays)): ?>
                        <ul class="space-y-4">
                            <?php foreach ($upcomingBirthdays as $bday): ?>
                                <?php 
                                    $isToday = date('m-d') === $bday['birth_day'];
                                    $dateObj = DateTime::createFromFormat('!m-d', $bday['birth_day']);
                                    $dateStr = $dateObj->format('M j');
                                ?>
                                <li class="flex items-center justify-between group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold <?= $isToday ? 'bg-pink-100 text-pink-600 animate-pulse' : 'bg-gray-100 text-gray-600' ?>">
                                            <?= substr($bday['first_name'], 0, 1) ?>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800"><?= $bday['first_name'] . ' ' . $bday['last_name'] ?></p>
                                            <p class="text-xs <?= $isToday ? 'text-pink-600 font-bold' : 'text-gray-500' ?>">
                                                <?= $isToday ? 'Today!' : $dateStr ?>
                                            </p>
                                        </div>
                                    </div>
                                    <a href="<?= APP_URL ?>/admin/communication?recipient_type=individual&recipient_id=<?= $bday['id'] ?>&subject=Happy Birthday!&message=Happy Birthday <?= $bday['first_name'] ?>! We pray God's blessings over your new year." 
                                       class="text-xs bg-gray-100 hover:bg-pink-100 text-gray-600 hover:text-pink-600 px-3 py-1 rounded-full transition-colors"
                                       title="Send Greeting">
                                        <i class="fas fa-paper-plane"></i>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-center py-6 text-gray-500">
                            <i class="fas fa-calendar-times text-3xl mb-2 opacity-30"></i>
                            <p class="text-sm">No birthdays in the next 30 days</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Prayer Requests Widget -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800">Prayer Requests</h3>
                    <span class="bg-orange-100 text-orange-700 text-xs px-2 py-1 rounded-full font-bold"><?= $stats['pending_prayers'] ?> New</span>
                </div>
                <div class="text-center py-4">
                    <p class="text-gray-500 mb-4 text-sm">Prayer requests awaiting your attention.</p>
                    <a href="<?= APP_URL ?>/admin/prayers" class="block w-full py-2 bg-orange-50 text-orange-700 hover:bg-orange-100 rounded-lg text-sm font-bold transition-colors">
                        Review Requests
                    </a>
                </div>
            </div>

            <!-- Recent Members Widget -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800">New Members</h3>
                    <a href="<?= APP_URL ?>/admin/members" class="text-brand-green text-xs font-bold hover:underline">See All</a>
                </div>
                <div class="space-y-3">
                    <?php if (!empty($stats['recent_members'])): ?>
                        <?php foreach ($stats['recent_members'] as $member): ?>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">
                                    <?= substr($member['first_name'], 0, 1) ?>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?></p>
                                    <p class="text-xs text-gray-400">Joined <?= date('M d', strtotime($member['joined_at'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500 text-center">No new members recently.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
    
    <!-- Chart.js Scripts (Conditional) -->
    <script>
    <?php if (in_array($role_id, [1, 2, 7])): ?>
    // Monthly Donations Chart
    const monthlyDonationsCtx = document.getElementById('monthlyDonationsChart').getContext('2d');
    new Chart(monthlyDonationsCtx, {
        type: 'bar',
        data: {
            labels: [<?php 
                if (!empty($stats['monthly_donations'])) {
                    $months = array_reverse(array_column($stats['monthly_donations'], 'month'));
                    foreach ($months as $month) echo '"' . date('M Y', strtotime($month . '-01')) . '", ';
                }
            ?>],
            datasets: [{
                label: 'Monthly Donations (₦)',
                data: [<?php 
                    if (!empty($stats['monthly_donations'])) {
                        $amounts = array_reverse(array_column($stats['monthly_donations'], 'total'));
                        foreach ($amounts as $amount) echo $amount . ', ';
                    }
                ?>],
                backgroundColor: '#006838',
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { display: false } }
        }
    });
    <?php endif; ?>

    // Member Growth Chart
    const memberGrowthCtx = document.getElementById('memberGrowthChart').getContext('2d');
    new Chart(memberGrowthCtx, {
        type: 'line',
        data: {
            labels: [<?php 
                if (!empty($stats['member_growth'])) {
                    foreach ($stats['member_growth'] as $growth) echo '"' . date('M Y', strtotime($growth['month'] . '-01')) . '", ';
                }
            ?>],
            datasets: [{
                label: 'New Members',
                data: [<?php 
                    if (!empty($stats['member_growth'])) {
                        foreach ($stats['member_growth'] as $growth) echo $growth['new_members'] . ', ';
                    }
                ?>],
                borderColor: '#3b82f6',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(59, 130, 246, 0.1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            plugins: { legend: { display: false } }
        }
    });
    </script>

<?php endif; ?>

<?php require_once 'app/Views/layouts/admin_footer.php'; ?>
