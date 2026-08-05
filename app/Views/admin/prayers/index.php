<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="px-6 py-8">
    <?php if (!empty($_SESSION['success']) || !empty($_SESSION['error'])): ?>
        <div class="mb-6 space-y-3">
            <?php if (!empty($_SESSION['success'])): ?>
                <div class="alert-auto-dismiss rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <?php if (!empty($_SESSION['error'])): ?>
                <div class="alert-auto-dismiss rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Prayer Requests</h1>
            <p class="text-gray-600">Manage and respond to prayer requests from members</p>
        </div>
        <div class="flex items-center space-x-4">
            <button class="bg-gray-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-gray-700 transition-all duration-200 flex items-center space-x-2">
                <i class="fas fa-filter"></i>
                <span>Filter</span>
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-pray text-lg"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Requests</p>
                    <p class="text-2xl font-bold text-gray-800"><?= count($prayers) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-check-circle text-lg"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Prayed Over</p>
                    <p class="text-2xl font-bold text-gray-800"><?= array_reduce($prayers, function($carry, $prayer) {
                        return $carry + ($prayer['status'] === 'prayed' ? 1 : 0);
                    }, 0) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <i class="fas fa-clock text-lg"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">New</p>
                    <p class="text-2xl font-bold text-gray-800"><?= array_reduce($prayers, function($carry, $prayer) {
                        return $carry + ($prayer['status'] === 'new' ? 1 : 0);
                    }, 0) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-eye text-lg"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Public Requests</p>
                    <p class="text-2xl font-bold text-gray-800"><?= array_reduce($prayers, function($carry, $prayer) {
                        return $carry + ($prayer['is_public'] ? 1 : 0);
                    }, 0) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Prayer Requests Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex-1">
                <div class="relative max-w-xs">
                    <input type="text" placeholder="Search prayer requests..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <select class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                    <option>All Status</option>
                    <option>New</option>
                    <option>Prayed</option>
                    <option>Archived</option>
                </select>
                <select class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                    <option>All Visibility</option>
                    <option>Public</option>
                    <option>Private</option>
                </select>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requester</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prayer Request</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visibility</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($prayers)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-pray text-4xl mb-4 opacity-50"></i>
                                    <p class="text-lg font-medium">No prayer requests found</p>
                                    <p class="text-sm">Prayer requests will appear here when submitted</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($prayers as $prayer): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-blue-700 rounded-full flex items-center justify-center text-white">
                                            <i class="fas fa-user text-sm"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($prayer['name'] ?: 'Anonymous') ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?= date('M j, Y', strtotime($prayer['created_at'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 font-medium mb-1">
                                        <?= htmlspecialchars(substr($prayer['request'], 0, 80)) ?><?= strlen($prayer['request']) > 80 ? '...' : '' ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <?= htmlspecialchars(substr($prayer['request'], 0, 120)) ?><?= strlen($prayer['request']) > 120 ? '...' : '' ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($prayer['is_public']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            <i class="fas fa-eye text-purple-500 mr-1 text-xs"></i>
                                            Public
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-lock text-gray-500 mr-1 text-xs"></i>
                                            Private
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($prayer['status'] === 'prayed'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle text-green-500 mr-1 text-xs"></i>
                                            Prayed
                                        </span>
                                    <?php elseif ($prayer['status'] === 'archived'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-archive text-gray-500 mr-1 text-xs"></i>
                                            Archived
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock text-yellow-500 mr-1 text-xs"></i>
                                            New
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <?php if ($prayer['status'] !== 'prayed'): ?>
                                            <form action="<?= APP_URL ?>/admin/prayers/mark-prayed/<?= $prayer['id'] ?>" method="POST" class="inline">
                                                <button type="submit" class="text-green-600 hover:text-green-900 transition-colors" title="Mark as prayed">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($prayer['status'] !== 'archived'): ?>
                                            <form action="<?= APP_URL ?>/admin/prayers/archive/<?= $prayer['id'] ?>" method="POST" class="inline">
                                                <button type="submit" class="text-gray-600 hover:text-gray-900 transition-colors" title="Archive">
                                                    <i class="fas fa-archive"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form action="<?= APP_URL ?>/admin/prayers/delete/<?= $prayer['id'] ?>" method="POST" class="inline" onsubmit="return confirm('Delete this prayer request?');">
                                            <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="Delete">
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
    </div>
</div>

<?php require_once 'app/Views/layouts/admin_footer.php'; ?>
