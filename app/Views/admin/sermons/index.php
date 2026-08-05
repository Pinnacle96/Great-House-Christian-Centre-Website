<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Sermon Library</h1>
            <p class="text-gray-600">Manage and organize your church sermons</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="<?= APP_URL ?>/admin/sermons/create" class="bg-gradient-to-r from-brand-green-600 to-brand-green-700 text-white px-6 py-3 rounded-lg shadow-md hover:from-brand-green-700 hover:to-brand-green-800 transition-all duration-200 flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Upload Sermon</span>
            </a>
        </div>
    </div>

<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sermon</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preacher</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Preached</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($sermons)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-microphone-slash text-5xl mb-4 opacity-40"></i>
                                    <p class="text-lg font-medium text-gray-500 mb-2">No sermons yet</p>
                                    <p class="text-sm text-gray-400">Get started by uploading your first sermon</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sermons as $sermon): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <?php
                                    $audioUrl = !empty($sermon['audio_url'])
                                        ? (preg_match('/^https?:\/\//i', $sermon['audio_url']) ? $sermon['audio_url'] : APP_URL . '/' . ltrim($sermon['audio_url'], '/'))
                                        : '';
                                    $videoUrl = $sermon['video_url'] ?? '';
                                ?>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-gradient-to-r from-brand-green-600 to-brand-green-700 rounded-lg flex items-center justify-center text-white">
                                            <i class="fas fa-play text-lg"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($sermon['title']) ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?= htmlspecialchars($sermon['series'] ?? 'Standalone') ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-blue-700 rounded-full flex items-center justify-center text-white text-xs font-medium">
                                            <?= strtoupper(substr($sermon['preacher'], 0, 1)) ?>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($sermon['preacher']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?= date('M j, Y', strtotime($sermon['date_preached'])) ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?= date('g:i A', strtotime($sermon['date_preached'])) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <?php if ($videoUrl): ?>
                                            <a href="<?= htmlspecialchars($videoUrl) ?>" target="_blank" rel="noopener" class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center hover:bg-blue-200 transition-colors" title="Watch video">
                                                <i class="fas fa-play text-xs"></i>
                                            </a>
                                            <a href="<?= htmlspecialchars($videoUrl) ?>" target="_blank" rel="noopener" class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center hover:bg-green-200 transition-colors" title="Open on YouTube">
                                                <i class="fab fa-youtube text-xs"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($audioUrl): ?>
                                            <a href="<?= htmlspecialchars($audioUrl) ?>" target="_blank" class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center hover:bg-blue-200 transition-colors" title="Listen to audio">
                                                <i class="fas fa-headphones text-xs"></i>
                                            </a>
                                            <a href="<?= htmlspecialchars($audioUrl) ?>" download class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center hover:bg-green-200 transition-colors" title="Download audio">
                                                <i class="fas fa-download text-xs"></i>
                                            </a>
                                        <?php endif; ?>
                                        <button class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center hover:bg-indigo-200 transition-colors" title="Edit">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        <button class="w-8 h-8 bg-red-100 text-red-600 rounded-full flex items-center justify-center hover:bg-red-200 transition-colors" title="Delete">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
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
