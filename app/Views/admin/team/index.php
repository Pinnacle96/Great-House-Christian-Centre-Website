<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-users text-brand-green-600 mr-2"></i>
            Manage Team Members
        </h1>
        <a href="<?= APP_URL ?>/admin/team/create" class="bg-brand-green text-white px-4 py-2 rounded-lg hover:bg-brand-green-dark transition-colors">
            <i class="fas fa-plus mr-2"></i> Add Member
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($members as $member): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($member['image']): ?>
                                <img class="h-10 w-10 rounded-full object-cover" src="<?= APP_URL ?>/<?= $member['image'] ?>" alt="">
                            <?php else: ?>
                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($member['name']) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500"><?= htmlspecialchars($member['role']) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= $member['display_order'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="<?= APP_URL ?>/admin/team/edit/<?= $member['id'] ?>" class="text-brand-green-600 hover:text-brand-green-900 mr-4">Edit</a>
                            <form action="<?= APP_URL ?>/admin/team/delete/<?= $member['id'] ?>" method="POST" class="inline" data-confirm-title="Delete team member" data-confirm="Delete <?= htmlspecialchars($member['name']) ?> from the leadership/team list?" data-confirm-button="Delete member">
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php require 'app/Views/partials/pagination.php'; ?>
    </div>
</div>

<?php require_once 'app/Views/layouts/admin_footer.php'; ?>
