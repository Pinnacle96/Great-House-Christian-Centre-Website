<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="px-6 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Service Planning</h1>
            <p class="text-gray-600">Plan Sunday services and schedule volunteers</p>
        </div>
        <a href="<?= APP_URL ?>/admin/services/create" class="bg-gradient-to-r from-brand-green-600 to-brand-green-700 text-white px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center space-x-2">
            <i class="fas fa-calendar-plus"></i>
            <span>Schedule New Service</span>
        </a>
    </div>

    <!-- Upcoming Services -->
    <div class="mb-12">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-calendar-alt text-brand-gold-500 mr-2"></i> Upcoming Services
        </h2>
        
        <?php if(empty($upcomingServices)): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center text-gray-500">
                <i class="fas fa-calendar-times text-4xl mb-3 opacity-50"></i>
                <p>No upcoming services scheduled.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach($upcomingServices as $service): ?>
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all border-l-4 border-brand-green-500 group">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-brand-green-600 bg-brand-green-50 px-2 py-1 rounded">
                                    <?= date('M d', strtotime($service['service_date'])) ?>
                                </span>
                                <span class="text-xs text-gray-500">
                                    <?= date('g:i A', strtotime($service['service_time'])) ?>
                                </span>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-800 mb-1 group-hover:text-brand-green-600 transition-colors">
                                <a href="<?= APP_URL ?>/admin/services/show/<?= $service['id'] ?>">
                                    <?= $service['title'] ?>
                                </a>
                            </h3>
                            
                            <?php if($service['series_title']): ?>
                                <p class="text-sm text-gray-500 mb-4 italic">Series: <?= $service['series_title'] ?></p>
                            <?php else: ?>
                                <p class="text-sm text-gray-400 mb-4 italic">No series linked</p>
                            <?php endif; ?>
                            
                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                                <div class="flex -space-x-2 overflow-hidden">
                                    <!-- Placeholder for roster avatars -->
                                    <div class="inline-block h-6 w-6 rounded-full ring-2 ring-white bg-gray-200 flex items-center justify-center text-[10px] text-gray-500 font-bold">?</div>
                                </div>
                                <a href="<?= APP_URL ?>/admin/services/show/<?= $service['id'] ?>" class="text-sm font-medium text-brand-green-600 hover:text-brand-green-800">
                                    Manage Plan <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Past Services (Collapsible or simple list) -->
    <div>
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center opacity-75">
            <i class="fas fa-history text-gray-400 mr-2"></i> Past Services
        </h2>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Service Name</th>
                        <th class="px-6 py-3">Series</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach($pastServices as $service): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium text-gray-900">
                                <?= date('M d, Y', strtotime($service['service_date'])) ?>
                            </td>
                            <td class="px-6 py-3 text-gray-700"><?= $service['title'] ?></td>
                            <td class="px-6 py-3 text-gray-500"><?= $service['series_title'] ?: '-' ?></td>
                            <td class="px-6 py-3 text-right">
                                <a href="<?= APP_URL ?>/admin/services/show/<?= $service['id'] ?>" class="text-indigo-600 hover:text-indigo-900 text-xs font-bold uppercase">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'app/Views/layouts/admin_footer.php'; ?>
