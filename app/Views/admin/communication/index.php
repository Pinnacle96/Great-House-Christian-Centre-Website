<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="px-6 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Communication Center</h1>
            <p class="text-gray-600">Broadcast messages to your church community via Email or SMS</p>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200">
                <i class="fas fa-check-circle text-green-500 mr-1"></i> System Ready
            </span>
        </div>
    </div>

    <!-- Feedback Messages -->
    <?php if(isset($_GET['success'])): ?>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-8 flex items-center shadow-sm">
            <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
            <div>
                <p class="font-bold text-green-800">Message Broadcast Initiated!</p>
                <p class="text-sm text-green-700">Successfully processed for <?= $_GET['success'] ?> recipients.</p>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Compose Message -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-8 py-4">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-pen-nib text-brand-green-600 mr-3"></i>
                        Compose New Broadcast
                    </h2>
                </div>
                
                <form action="<?= APP_URL ?>/admin/communication/send" method="POST" class="p-8 space-y-6">
                    <!-- Channel Selection -->
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer relative">
                            <input type="radio" name="channel" value="email" class="peer sr-only" checked>
                            <div class="p-4 rounded-lg border-2 border-gray-200 hover:border-brand-green-300 peer-checked:border-brand-green-500 peer-checked:bg-brand-green-50 transition-all text-center">
                                <i class="fas fa-envelope text-2xl mb-2 text-gray-400 peer-checked:text-brand-green-600"></i>
                                <span class="block font-bold text-gray-700 peer-checked:text-brand-green-800">Email</span>
                            </div>
                        </label>
                        <label class="cursor-pointer relative">
                            <input type="radio" name="channel" value="sms" class="peer sr-only">
                            <div class="p-4 rounded-lg border-2 border-gray-200 hover:border-brand-green-300 peer-checked:border-brand-green-500 peer-checked:bg-brand-green-50 transition-all text-center">
                                <i class="fas fa-sms text-2xl mb-2 text-gray-400 peer-checked:text-brand-green-600"></i>
                                <span class="block font-bold text-gray-700 peer-checked:text-brand-green-800">SMS</span>
                            </div>
                        </label>
                    </div>

                    <!-- Recipient Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Recipient Audience</label>
                        <select id="recipientType" name="recipient_type" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                            <option value="all">All Active Members</option>
                            <option value="group">Specific Group / Team</option>
                        </select>
                    </div>

                    <div id="groupSelectContainer" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Group</label>
                        <select name="group_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                            <option value="">-- Choose a Group --</option>
                            <?php foreach($groups as $group): ?>
                                <option value="<?= $group['id'] ?>"><?= $group['name'] ?> (<?= $group['type'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Message Content -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject (Email Only)</label>
                        <input type="text" name="subject" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent" placeholder="Important Announcement">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Message Body</label>
                        <textarea name="message" rows="6" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent" placeholder="Type your message here..." required></textarea>
                        <p class="text-xs text-gray-500 mt-2 text-right">SMS Limit: ~160 chars per segment</p>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="bg-gradient-to-r from-brand-green-600 to-brand-green-700 text-white px-8 py-3 rounded-lg shadow-lg hover:from-brand-green-700 hover:to-brand-green-800 transition-all duration-200 flex items-center space-x-2 transform hover:-translate-y-1">
                            <i class="fas fa-paper-plane"></i>
                            <span class="font-bold">Send Broadcast</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column: Recent Logs -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Recent Logs</h3>
                </div>
                
                <div class="divide-y divide-gray-100 max-h-[600px] overflow-y-auto">
                    <?php if(empty($recentLogs)): ?>
                        <div class="p-8 text-center text-gray-500 italic">
                            No messages sent recently.
                        </div>
                    <?php else: ?>
                        <?php foreach($recentLogs as $log): ?>
                            <div class="p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500">
                                        <?= date('M d, h:i A', strtotime($log['created_at'])) ?>
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold <?= $log['channel'] == 'email' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' ?>">
                                        <?= $log['channel'] ?>
                                    </span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm mb-1 truncate">
                                    <?= $log['subject'] ?: '(No Subject)' ?>
                                </h4>
                                <p class="text-xs text-gray-600 mb-2 truncate">
                                    To: <?= $log['recipient_type'] == 'all_members' ? 'Everyone' : ($log['group_name'] ?: 'Group') ?>
                                </p>
                                <div class="flex items-center gap-2 text-xs text-gray-400">
                                    <i class="fas fa-user-circle"></i> <?= $log['sender_name'] ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle Group Select based on Audience Type
    document.getElementById('recipientType').addEventListener('change', function() {
        const groupContainer = document.getElementById('groupSelectContainer');
        if (this.value === 'group') {
            groupContainer.classList.remove('hidden');
        } else {
            groupContainer.classList.add('hidden');
        }
    });
</script>

<?php require_once 'app/Views/layouts/admin_footer.php'; ?>
