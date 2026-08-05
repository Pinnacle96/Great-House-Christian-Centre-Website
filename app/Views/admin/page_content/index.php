<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-edit text-brand-green-600 mr-2"></i>
            Edit <?= ucfirst($page) ?> Page Content
        </h1>
        <a href="<?= APP_URL ?>/admin" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= $_SESSION['success'] ?></span>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($content)): ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        No content found for this page. Please run the seeder or check the database.
                    </p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <form action="<?= APP_URL ?>/admin/page-content/update" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="page_name" value="<?= htmlspecialchars($page) ?>">

            <div class="grid grid-cols-1 gap-6">
                <?php foreach ($content as $section => $items): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="bg-brand-green-50 px-6 py-4 border-b border-brand-green-100">
                            <h2 class="text-lg font-semibold text-brand-green-800 capitalize">
                                <i class="fas fa-layer-group mr-2"></i>
                                <?= str_replace(['_', '-'], ' ', $section) ?> Section
                            </h2>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            <?php foreach ($items as $key => $data): ?>
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start border-b border-gray-100 pb-6 last:border-0 last:pb-0">
                                    <div class="md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 capitalize mb-1">
                                            <?= str_replace(['_', '-'], ' ', $key) ?>
                                        </label>
                                        <p class="text-xs text-gray-500">
                                            Key: <?= $key ?>
                                        </p>
                                    </div>
                                    
                                    <div class="md:col-span-9">
                                        <?php if ($data['type'] === 'image'): ?>
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-shrink-0 w-32 h-32 bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                                                    <?php if (!empty($data['value'])): ?>
                                                        <img src="<?= APP_URL . '/' . $data['value'] ?>" alt="<?= $key ?>" class="w-full h-full object-cover">
                                                    <?php else: ?>
                                                        <div class="flex items-center justify-center h-full text-gray-400">
                                                            <i class="fas fa-image fa-2x"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex-1">
                                                    <input type="file" 
                                                           name="images[<?= $data['id'] ?>]" 
                                                           accept="image/*"
                                                           class="block w-full text-sm text-gray-500
                                                                  file:mr-4 file:py-2 file:px-4
                                                                  file:rounded-full file:border-0
                                                                  file:text-sm file:font-semibold
                                                                  file:bg-brand-green-50 file:text-brand-green-700
                                                                  hover:file:bg-brand-green-100
                                                                  transition-colors cursor-pointer"
                                                    >
                                                    <p class="mt-1 text-xs text-gray-500">Allowed formats: JPG, PNG, GIF, WebP. Max size: 2MB.</p>
                                                    <input type="hidden" name="content[<?= $data['id'] ?>]" value="<?= htmlspecialchars($data['value']) ?>">
                                                </div>
                                            </div>
                                        <?php elseif ($data['type'] === 'code'): ?>
                                            <textarea name="content[<?= $data['id'] ?>]" 
                                                      rows="4" 
                                                      class="shadow-sm focus:ring-brand-green-500 focus:border-brand-green-500 block w-full sm:text-sm border-gray-300 rounded-md transition-shadow p-2 font-mono text-xs bg-gray-50"
                                            ><?= htmlspecialchars($data['value']) ?></textarea>
                                            <p class="mt-1 text-xs text-gray-500">Raw HTML/Code Mode (No Visual Editor)</p>
                                        <?php elseif ($data['type'] === 'richtext' || (strlen($data['value']) > 100 && $data['type'] !== 'text')): ?>
                                            <textarea name="content[<?= $data['id'] ?>]" 
                                                      rows="4" 
                                                      class="richtext-editor shadow-sm focus:ring-brand-green-500 focus:border-brand-green-500 block w-full sm:text-sm border-gray-300 rounded-md transition-shadow p-2"
                                            ><?= htmlspecialchars($data['value']) ?></textarea>
                                        <?php else: ?>
                                            <input type="text" 
                                                   name="content[<?= $data['id'] ?>]" 
                                                   value="<?= htmlspecialchars($data['value']) ?>" 
                                                   class="shadow-sm focus:ring-brand-green-500 focus:border-brand-green-500 block w-full sm:text-sm border-gray-300 rounded-md transition-shadow h-10 px-3"
                                            >
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-8 flex justify-end pb-8">
                <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-brand-green-600 hover:bg-brand-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green-500 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Save Changes
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php require_once 'app/Views/layouts/admin_footer.php'; ?>
