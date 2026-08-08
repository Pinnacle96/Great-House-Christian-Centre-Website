<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Members Management</h1>
            <p class="text-gray-600">Manage all church members and their information</p>
        </div>
        <a href="<?= APP_URL ?>/admin/members/create" class="bg-gradient-to-r from-brand-green-600 to-brand-green-700 text-white px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center space-x-2">
            <i class="fas fa-user-plus"></i>
            <span>Add New Member</span>
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-users text-lg"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total People</p>
                    <p class="text-2xl font-bold text-gray-800"><?= (int)($memberStats['total'] ?? 0) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-user-check text-lg"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Members</p>
                    <p class="text-2xl font-bold text-gray-800"><?= (int)($memberStats['members'] ?? 0) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <i class="fas fa-user-clock text-lg"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Regular Attenders</p>
                    <p class="text-2xl font-bold text-gray-800"><?= (int)($memberStats['regular_attenders'] ?? 0) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-user-plus text-lg"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Guests</p>
                    <p class="text-2xl font-bold text-gray-800"><?= (int)($memberStats['guests'] ?? 0) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Members Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex-1">
                <div class="relative max-w-xs">
                    <input type="text" id="memberSearch" placeholder="Search members..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <select class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                    <option>All Types</option>
                    <option>Member</option>
                    <option>Regular Attender</option>
                    <option>Guest</option>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($members)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-users text-4xl mb-4 opacity-50"></i>
                                    <p class="text-lg font-medium">No members found</p>
                                    <p class="text-sm">Get started by adding your first member</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($members as $member): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-r from-brand-green-600 to-brand-green-700 rounded-full flex items-center justify-center text-white font-medium">
                                            <?= strtoupper(substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1)) ?>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                ID: <?= $member['id'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><a href="mailto:<?= $member['email'] ?>" class="hover:underline"><?= htmlspecialchars($member['email']) ?></a></div>
                                    <div class="text-xs text-gray-500"><?= htmlspecialchars($member['phone']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                        <?= $member['membership_type'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($member['status'] === 'active'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-circle text-green-500 mr-1 text-[8px]"></i> Active
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-circle text-gray-500 mr-1 text-[8px]"></i> Inactive
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-3">
                                        <a href="<?= APP_URL ?>/admin/members/show/<?= $member['id'] ?>" class="text-brand-green-600 hover:text-brand-green-900 bg-brand-green-50 p-2 rounded-lg transition-colors" title="View Profile">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= APP_URL ?>/admin/members/edit/<?= $member['id'] ?>" class="text-blue-600 hover:text-blue-900 bg-blue-50 p-2 rounded-lg transition-colors" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
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

<script>
    // Simple client-side search
    document.getElementById('memberSearch').addEventListener('keyup', function() {
        let searchText = this.value.toLowerCase();
        let rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });
</script>

<?php require_once 'app/Views/layouts/admin_footer.php'; ?>
