<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-clipboard-list text-brand-green-600 mr-2"></i>
            Event Registrations
        </h1>
        <div class="flex gap-2 mt-4 md:mt-0">
            <a href="<?= APP_URL ?>/admin/registrations/create" class="bg-brand-green text-white px-4 py-2 rounded-lg hover:bg-brand-green-dark transition-colors text-sm flex items-center">
                <i class="fas fa-plus mr-2"></i> Register New
            </a>
            <?php if ($selectedEventId): ?>
                <?php if (in_array($_SESSION['role_id'], [1, 2, 3, 5, 7])): ?>
                <a href="<?= APP_URL ?>/admin/registrations/export-pdf?event_id=<?= $selectedEventId ?>" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors text-sm flex items-center">
                    <i class="fas fa-file-pdf mr-2"></i> PDF
                </a>
                <a href="<?= APP_URL ?>/admin/registrations/export-csv?event_id=<?= $selectedEventId ?>" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm flex items-center">
                    <i class="fas fa-file-csv mr-2"></i> CSV
                </a>
                <button onclick="document.getElementById('reminderModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm flex items-center">
                    <i class="fas fa-envelope mr-2"></i> Send Reminder
                </button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form action="" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Event</label>
                <select name="event_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green-500 focus:ring-brand-green-500" onchange="this.form.submit()">
                    <?php foreach ($events as $event): ?>
                        <option value="<?= $event['id'] ?>" <?= $event['id'] == $selectedEventId ? 'selected' : '' ?>>
                            <?= htmlspecialchars($event['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="w-full md:w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Status</label>
                <select name="filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green-500 focus:ring-brand-green-500" onchange="this.form.submit()">
                    <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>All Registrants</option>
                    <option value="onsite" <?= $filter == 'onsite' ? 'selected' : '' ?>>Onsite Only</option>
                    <option value="online" <?= $filter == 'online' ? 'selected' : '' ?>>Online Only</option>
                    <option value="checked_in" <?= $filter == 'checked_in' ? 'selected' : '' ?>>Checked In</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
            <div class="text-sm text-gray-500 font-bold uppercase">Total Registered</div>
            <div class="text-2xl font-bold text-gray-800"><?= $stats['total'] ?></div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-purple-500">
            <div class="text-sm text-gray-500 font-bold uppercase">Onsite</div>
            <div class="text-2xl font-bold text-gray-800"><?= $stats['onsite'] ?></div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-indigo-500">
            <div class="text-sm text-gray-500 font-bold uppercase">Online</div>
            <div class="text-2xl font-bold text-gray-800"><?= $stats['online'] ?></div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
            <div class="text-sm text-gray-500 font-bold uppercase">Checked In</div>
            <div class="text-2xl font-bold text-gray-800"><?= $stats['checked_in'] ?></div>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= $_SESSION['success'] ?></span>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($registrations)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No registrations found matching your criteria.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($registrations as $reg): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($reg['first_name'] . ' ' . $reg['last_name']) ?></div>
                                    <div class="text-xs text-gray-500">Registered: <?= date('M d, H:i', strtotime($reg['created_at'])) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= htmlspecialchars($reg['email']) ?></div>
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($reg['phone']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($reg['attendance_mode'] == 'onsite'): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Onsite</span>
                                    <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Online</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500">
                                    <?= htmlspecialchars($reg['registration_code']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($reg['checked_in_at']): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Checked In (<?= date('H:i', strtotime($reg['checked_in_at'])) ?>)
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <?php if (!$reg['checked_in_at'] && $reg['attendance_mode'] == 'onsite'): ?>
                                        <form action="<?= APP_URL ?>/admin/registrations/check-in/<?= $reg['id'] ?>" method="POST" class="inline">
                                            <button type="submit" class="text-brand-green-600 hover:text-brand-green-900 bg-brand-green-50 px-3 py-1 rounded-md border border-brand-green-200">Check In</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reminder Modal -->
<div id="reminderModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Send Reminder Email</h3>
            <form action="<?= APP_URL ?>/admin/registrations/send-reminder" method="POST">
                <input type="hidden" name="event_id" value="<?= $selectedEventId ?>">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Audience</label>
                    <select name="target" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green-500 focus:ring-brand-green-500">
                        <option value="all">All Registrants</option>
                        <option value="onsite">Onsite Only</option>
                        <option value="online">Online Only</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                    <textarea name="message" rows="4" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green-500 focus:ring-brand-green-500" placeholder="Enter your reminder message here..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">This message will be prefixed with "Dear [Name],"</p>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('reminderModal').classList.add('hidden')" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="bg-brand-green-600 text-white px-4 py-2 rounded-md hover:bg-brand-green-700">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'app/Views/layouts/admin_footer.php'; ?>
