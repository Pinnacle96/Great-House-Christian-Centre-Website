<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Small Groups & Ministries</h1>
            <p class="text-gray-600">Manage church groups, teams, and classes</p>
        </div>
        <a href="<?= APP_URL ?>/admin/groups/create" class="bg-gradient-to-r from-brand-green-600 to-brand-green-700 text-white px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center space-x-2">
            <i class="fas fa-plus"></i>
            <span>Create New Group</span>
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-indigo-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-4">
                    <i class="fas fa-layer-group text-lg"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Groups</p>
                    <p class="text-2xl font-bold text-gray-800"><?= (int)($groupStats['total'] ?? 0) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-teal-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-teal-100 text-teal-600 mr-4">
                    <i class="fas fa-users text-lg"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Ministry Teams</p>
                    <p class="text-2xl font-bold text-gray-800"><?= (int)($groupStats['ministry_teams'] ?? 0) ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600 mr-4">
                    <i class="fas fa-home text-lg"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Small Groups</p>
                    <p class="text-2xl font-bold text-gray-800"><?= (int)($groupStats['small_groups'] ?? 0) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Groups Grid -->
    <?php if (empty($groups)): ?>
        <div class="bg-white rounded-xl shadow-md p-12 text-center">
            <div class="text-gray-500">
                <i class="fas fa-layer-group text-4xl mb-4 opacity-50"></i>
                <p class="text-lg font-medium">No groups found</p>
                <p class="text-sm mb-6">Create your first small group or ministry team</p>
                <a href="<?= APP_URL ?>/admin/groups/create" class="inline-flex items-center space-x-2 bg-brand-green-600 text-white px-6 py-3 rounded-lg hover:bg-brand-green-700 transition-colors">
                    <i class="fas fa-plus"></i>
                    <span>Create Group</span>
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($groups as $group): ?>
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 border border-gray-100">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span class="inline-block px-2 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-600 mb-2">
                                    <?= $group['type'] ?>
                                </span>
                                <h3 class="text-xl font-bold text-gray-800 line-clamp-1"><?= $group['name'] ?></h3>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-brand-green-50 text-brand-green-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2 h-10">
                            <?= $group['description'] ?: 'No description provided.' ?>
                        </p>
                        
                        <div class="space-y-2 mb-6 text-sm text-gray-500">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user-tie w-4 text-center"></i>
                                <span>Leader: <span class="text-gray-800 font-medium"><?= $group['leader_name'] ?: 'Unassigned' ?></span></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calendar-alt w-4 text-center"></i>
                                <span><?= $group['schedule_info'] ?: 'TBD' ?></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-map-marker-alt w-4 text-center"></i>
                                <span><?= $group['location'] ?: 'TBD' ?></span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100 flex justify-between items-center">
                            <a href="<?= APP_URL ?>/admin/groups/show/<?= $group['id'] ?>" class="text-brand-green-600 font-semibold text-sm hover:underline">
                                View Details <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                            <a href="<?= APP_URL ?>/admin/groups/edit/<?= $group['id'] ?>" class="text-gray-400 hover:text-blue-600 transition-colors">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-6 overflow-hidden rounded-xl shadow-md">
            <?php require 'app/Views/partials/pagination.php'; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'app/Views/layouts/admin_footer.php'; ?>
