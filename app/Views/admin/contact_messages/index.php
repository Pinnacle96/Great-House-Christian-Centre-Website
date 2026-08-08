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

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Contact Messages</h1>
            <p class="text-gray-600">Review and manage messages submitted from the public contact form</p>
        </div>
        <form action="<?= APP_URL ?>/admin/contact-messages" method="GET" class="flex items-center gap-3">
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand-green-500 focus:border-transparent" onchange="this.form.submit()">
                <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All</option>
                <option value="new" <?= $filter === 'new' ? 'selected' : '' ?>>New</option>
                <option value="read" <?= $filter === 'read' ? 'selected' : '' ?>>Read</option>
                <option value="archived" <?= $filter === 'archived' ? 'selected' : '' ?>>Archived</option>
            </select>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sender</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Received</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($messages)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4 opacity-40"></i>
                                <p class="font-medium">No contact messages found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($messages as $message): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 align-top">
                                    <div class="font-medium text-gray-900"><?= htmlspecialchars($message['name']) ?></div>
                                    <a href="mailto:<?= htmlspecialchars($message['email']) ?>" class="text-sm text-brand-green-600 hover:text-brand-green-800">
                                        <?= htmlspecialchars($message['email']) ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4 align-top max-w-xl">
                                    <div class="font-semibold text-gray-900 mb-1"><?= htmlspecialchars($message['subject']) ?></div>
                                    <div class="text-sm text-gray-600 whitespace-pre-line"><?= nl2br(htmlspecialchars($message['message'])) ?></div>
                                </td>
                                <td class="px-6 py-4 align-top">
                                    <?php
                                        $statusClasses = [
                                            'new' => 'bg-yellow-100 text-yellow-800',
                                            'read' => 'bg-green-100 text-green-800',
                                            'archived' => 'bg-gray-100 text-gray-800'
                                        ];
                                    ?>
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusClasses[$message['status']] ?? 'bg-gray-100 text-gray-800' ?>">
                                        <?= ucfirst($message['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 align-top text-sm text-gray-500 whitespace-nowrap">
                                    <?= date('M j, Y g:i A', strtotime($message['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 align-top text-right">
                                    <div class="flex justify-end gap-3">
                                        <?php if ($message['status'] !== 'read'): ?>
                                            <form action="<?= APP_URL ?>/admin/contact-messages/read/<?= $message['id'] ?>" method="POST">
                                                <button type="submit" class="text-green-600 hover:text-green-900" title="Mark read">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($message['status'] !== 'archived'): ?>
                                            <form action="<?= APP_URL ?>/admin/contact-messages/archive/<?= $message['id'] ?>" method="POST">
                                                <button type="submit" class="text-gray-600 hover:text-gray-900" title="Archive">
                                                    <i class="fas fa-archive"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form action="<?= APP_URL ?>/admin/contact-messages/delete/<?= $message['id'] ?>" method="POST" onsubmit="return confirm('Delete this contact message?');">
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
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
