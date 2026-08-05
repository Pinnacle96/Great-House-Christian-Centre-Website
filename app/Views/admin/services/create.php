<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="px-6 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Schedule New Service</h1>
            <p class="text-gray-600">Plan a future Sunday or midweek service</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="<?= APP_URL ?>/admin/services" class="bg-gray-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-gray-700 transition-all duration-200 flex items-center space-x-2">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Services</span>
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="border-b border-gray-200 bg-gray-50 px-8 py-4">
            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                <i class="fas fa-calendar-plus text-brand-green-600 mr-3"></i>
                Service Details
            </h2>
        </div>
        
        <form action="<?= APP_URL ?>/admin/services/store" method="POST" class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php if (!empty($branches)): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Branch *</label>
                        <select name="branch_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent" required>
                            <?php foreach ($branches as $branch): ?>
                                <option value="<?= $branch['id'] ?>" <?= (int)($selectedBranchId ?? 0) === (int)$branch['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($branch['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Service Title *</label>
                    <input type="text" name="title" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent" placeholder="e.g. Sunday Celebration" value="Sunday Service" required>
                </div>

                <div class="grid grid-cols-2 gap-4 md:col-span-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date *</label>
                        <input type="date" name="service_date" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Time *</label>
                        <input type="time" name="service_time" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent" value="09:00" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Service Type</label>
                    <select name="type" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                        <option value="Sunday Service">Sunday Service</option>
                        <option value="Midweek Service">Midweek Service</option>
                        <option value="Special Event">Special Event</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Series Title</label>
                    <input type="text" name="series_title" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent" placeholder="e.g. Kingdom Foundations">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Internal Notes</label>
                    <textarea name="notes" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent" placeholder="Special instructions, guest speakers, or production notes..."></textarea>
                </div>
            </div>

            <div class="flex justify-end pt-8 border-t border-gray-100">
                <button type="submit" class="bg-gradient-to-r from-brand-green-600 to-brand-green-700 text-white px-8 py-4 rounded-lg shadow-lg hover:from-brand-green-700 hover:to-brand-green-800 transition-all duration-200 flex items-center space-x-2 transform hover:-translate-y-1">
                    <i class="fas fa-save fa-lg"></i>
                    <span class="font-bold text-lg">Schedule Service</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'app/Views/layouts/admin_footer.php'; ?>
