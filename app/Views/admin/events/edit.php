<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Event</h1>
            <p class="text-gray-600">Update event details and schedule</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="<?= APP_URL ?>/admin/events" class="bg-gray-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-gray-700 transition-all duration-200 flex items-center space-x-2">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Events</span>
            </a>
        </div>
    </div>

    <!-- Event Form -->
    <div class="bg-white rounded-xl shadow-md p-8">
        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-edit text-brand-green-600 mr-3"></i>
            Event Details
        </h2>
        
        <form action="<?= APP_URL ?>/admin/events/update/<?= $event['id'] ?>" method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-code-branch text-gray-400 mr-2"></i>
                    Branch
                </label>
                <select name="branch_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                    <?php foreach ($branches as $branch): ?>
                        <option value="<?= $branch['id'] ?>" <?= (int)($event['branch_id'] ?? 0) === (int)$branch['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($branch['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-heading text-gray-400 mr-2"></i>
                    Event Title *
                </label>
                <input type="text" name="title" value="<?= htmlspecialchars($event['title']) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent" placeholder="Enter event title" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-start text-gray-400 mr-2"></i>
                        Start Date & Time *
                    </label>
                    <input type="datetime-local" name="start_datetime" value="<?= date('Y-m-d\TH:i', strtotime($event['start_datetime'])) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-times text-gray-400 mr-2"></i>
                        End Date & Time
                    </label>
                    <input type="datetime-local" name="end_datetime" value="<?= $event['end_datetime'] ? date('Y-m-d\TH:i', strtotime($event['end_datetime'])) : '' ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                    Location *
                </label>
                <input type="text" name="location" value="<?= htmlspecialchars($event['location']) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent" placeholder="e.g. Main Auditorium, Online, Fellowship Hall" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-align-left text-gray-400 mr-2"></i>
                    Description
                </label>
                <textarea name="description" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent" placeholder="Enter event description, agenda, or special instructions"><?= htmlspecialchars($event['description']) ?></textarea>
            </div>

            <div class="flex items-center pt-2">
                <input type="checkbox" name="requires_registration" id="requires_registration" <?= $event['requires_registration'] ? 'checked' : '' ?> class="h-4 w-4 text-brand-green-600 focus:ring-brand-green-500 border-gray-300 rounded">
                <label for="requires_registration" class="ml-3 block text-sm font-medium text-gray-700">
                    <i class="fas fa-user-check text-gray-400 mr-1"></i>
                    Requires Registration?
                </label>
            </div>
            
            <div class="pt-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-image text-gray-400 mr-2"></i>
                    Event Image URL (Optional)
                </label>
                <input type="text" name="image" value="<?= htmlspecialchars($event['image'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent" placeholder="assets/img/event-1.jpg">
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="bg-gradient-to-r from-brand-green-600 to-brand-green-700 text-white px-8 py-3 rounded-lg shadow-md hover:from-brand-green-700 hover:to-brand-green-800 transition-all duration-200 flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Update Event</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'app/Views/layouts/admin_footer.php'; ?>
