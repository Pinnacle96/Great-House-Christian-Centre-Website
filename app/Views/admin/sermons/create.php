<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Upload New Sermon</h1>
            <p class="text-gray-600">Add a new sermon to the media library</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="<?= APP_URL ?>/admin/sermons" class="bg-gray-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-gray-700 transition-all duration-200 flex items-center space-x-2">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Sermons</span>
            </a>
        </div>
    </div>

    <!-- Sermon Form -->
    <div class="bg-white rounded-xl shadow-md p-8">
        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-microphone-alt text-brand-green-600 mr-3"></i>
            Sermon Information
        </h2>
    
    <form action="<?= APP_URL ?>/admin/sermons/store" method="POST" enctype="multipart/form-data">
        <?php if (!empty($branches)): ?>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Branch</label>
                <select name="branch_id" class="w-full border rounded px-3 py-2" required>
                    <?php foreach ($branches as $branch): ?>
                        <option value="<?= $branch['id'] ?>" <?= (int)($selectedBranchId ?? 0) === (int)$branch['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($branch['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Title</label>
            <input type="text" name="title" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Preacher</label>
            <input type="text" name="preacher" class="w-full border rounded px-3 py-2" placeholder="e.g. Senior Pastor" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Date Preached</label>
            <input type="date" name="date_preached" class="w-full border rounded px-3 py-2" required>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">YouTube Video URL</label>
            <input type="url" name="video_url" class="w-full border rounded px-3 py-2" placeholder="https://www.youtube.com/watch?v=...">
            <p class="text-xs text-gray-500 mt-1">Use a YouTube watch, share, shorts, or embed link. The website will play it inline.</p>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Audio File</label>
            <input type="file" name="audio_file" accept=".mp3,.m4a,.wav,.ogg,audio/*" class="w-full border rounded px-3 py-2">
            <p class="text-xs text-gray-500 mt-1">Upload MP3, M4A, WAV, or OGG. Audio can be played and downloaded on the site.</p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Existing Audio URL / File Path</label>
            <input type="text" name="audio_url" class="w-full border rounded px-3 py-2" placeholder="assets/uploads/sermons/audio/message.mp3">
            <p class="text-xs text-gray-500 mt-1">Optional. Use this only if the audio file is already on the server.</p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Description</label>
            <textarea name="description" rows="4" class="w-full border rounded px-3 py-2"></textarea>
        </div>

        <div class="flex justify-end mt-6">
            <a href="<?= APP_URL ?>/admin/sermons" class="text-gray-600 mr-4 py-2">Cancel</a>
            <button type="submit" class="bg-brand-green text-white px-6 py-2 rounded hover:bg-green-700">Save Sermon</button>
        </div>
    </form>
</div>

<?php require_once 'app/Views/layouts/admin_footer.php'; ?>
