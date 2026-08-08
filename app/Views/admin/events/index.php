<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Events Management</h1>
            <p class="text-gray-600">Manage church events and schedules</p>
        </div>
        <a href="<?= APP_URL ?>/admin/events/create" class="bg-gradient-to-r from-brand-green-600 to-brand-green-700 text-white px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center space-x-2">
            <i class="fas fa-calendar-plus"></i>
            <span>Create New Event</span>
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-calendar-alt text-lg"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Events</p>
                    <p class="text-2xl font-bold text-gray-800"><?= (int)($eventStats['total'] ?? 0) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-calendar-check text-lg"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Upcoming Events</p>
                    <p class="text-2xl font-bold text-gray-800"><?= (int)($eventStats['upcoming'] ?? 0) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <i class="fas fa-clipboard-list text-lg"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Require Registration</p>
                    <p class="text-2xl font-bold text-gray-800"><?= (int)($eventStats['requires_registration'] ?? 0) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-users text-lg"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Registrations</p>
                    <p class="text-2xl font-bold text-gray-800"><?= (int)($eventStats['registrations'] ?? 0) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Events Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex-1">
                <div class="relative max-w-xs">
                    <input type="text" placeholder="Search events..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <select class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                    <option>All Events</option>
                    <option>Upcoming</option>
                    <option>Past</option>
                </select>
                <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg transition-colors">
                    <i class="fas fa-filter"></i>
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($events)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-calendar text-4xl mb-4 opacity-50"></i>
                                    <p class="text-lg font-medium">No events found</p>
                                    <p class="text-sm">Create your first event to get started</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($events as $event): ?>
                            <?php 
                            $eventDate = new DateTime($event['start_datetime']);
                            $now = new DateTime();
                            $isUpcoming = $eventDate > $now;
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-r from-brand-green-600 to-brand-green-700 rounded-full flex items-center justify-center text-white">
                                            <i class="fas fa-calendar text-sm"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($event['title']) ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?= htmlspecialchars($event['event_type'] ?? 'General Event') ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= $eventDate->format('M j, Y') ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?= $eventDate->format('g:i A') ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= htmlspecialchars($event['location']) ?></div>
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($event['address'] ?? '') ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($event['requires_registration']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle text-green-500 mr-1 text-xs"></i>
                                            Required
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-times-circle text-gray-500 mr-1 text-xs"></i>
                                            Not Required
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="<?= APP_URL ?>/admin/events/edit/<?= $event['id'] ?>" class="text-brand-green-600 hover:text-brand-green-700 transition-colors" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if($event['requires_registration']): ?>
                                            <a href="<?= APP_URL ?>/admin/registrations?event_id=<?= $event['id'] ?>" class="text-blue-600 hover:text-blue-700 transition-colors" title="Registrations">
                                                <i class="fas fa-users"></i>
                                            </a>
                                        <?php endif; ?>
                                        <form action="<?= APP_URL ?>/admin/events/delete/<?= $event['id'] ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this event?');" class="inline">
                                            <button type="submit" class="text-red-600 hover:text-red-700 transition-colors" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
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
