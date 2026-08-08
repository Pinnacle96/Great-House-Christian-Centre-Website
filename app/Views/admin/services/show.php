<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="px-6 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3">
                <span class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center text-green-600">
                    <i class="fas fa-calendar-check text-2xl"></i>
                </span>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($service['title']) ?></h1>
                    <span class="text-sm font-medium text-gray-500">
                        <?= date('l, M jS, Y', strtotime($service['service_date'])) ?> at <?= date('g:i A', strtotime($service['service_time'])) ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="<?= APP_URL ?>/admin/services/edit/<?= $service['id'] ?>" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2">
                <i class="fas fa-edit"></i> Edit Info
            </a>
            <a href="<?= APP_URL ?>/admin/services" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Service Overview & Plan -->
        <div class="space-y-6">
            <!-- Service Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Service Details</h3>
                <div class="space-y-4">
                    <div>
                        <span class="block text-xs text-gray-500 uppercase tracking-wider mb-1">Series</span>
                        <p class="text-sm font-medium text-indigo-600"><?= $service['series_title'] ?: 'No series assigned' ?></p>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500 uppercase tracking-wider mb-1">Type</span>
                        <p class="text-sm text-gray-700"><?= $service['type'] ?></p>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500 uppercase tracking-wider mb-1">Internal Notes</span>
                        <div class="text-sm text-gray-600 bg-yellow-50 p-3 rounded-lg border border-yellow-100 whitespace-pre-wrap">
                            <?= $service['notes'] ?: 'No notes available.' ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Roster Panel -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-user-plus text-green-500"></i> Schedule Volunteer
                </h3>
                <form action="<?= APP_URL ?>/admin/services/addRoster" method="POST" class="space-y-4">
                    <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Ministry Team</label>
                        <select id="teamSelect" name="team_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500" required>
                            <option value="">-- Select Team --</option>
                            <?php foreach($teams as $team): ?>
                                <option value="<?= $team['id'] ?>"><?= $team['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Team Member</label>
                        <select id="memberSelect" name="member_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500" required disabled>
                            <option value="">-- Select Team First --</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Specific Role/Position</label>
                        <input type="text" name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500" placeholder="e.g. Lead Vocals, Camera 1">
                    </div>

                    <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 font-medium text-sm transition-colors">
                        Add to Schedule
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Column: Roster & Schedule -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-clipboard-list text-gray-400"></i> Service Roster
                    </h3>
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500"></span> Confirmed</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-yellow-400"></span> Pending</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-400"></span> Declined</span>
                    </div>
                </div>

                <?php if(empty($roster)): ?>
                    <div class="p-12 text-center text-gray-500">
                        <i class="fas fa-users-slash text-4xl mb-4 opacity-30"></i>
                        <p class="font-medium">No volunteers scheduled yet</p>
                        <p class="text-sm text-gray-400">Use the form on the left to add people to teams.</p>
                    </div>
                <?php else: ?>
                    <?php 
                        // Group roster by Team
                        $groupedRoster = [];
                        foreach($roster as $r) {
                            $groupedRoster[$r['team_name']][] = $r;
                        }
                    ?>

                    <div class="divide-y divide-gray-100">
                        <?php foreach($groupedRoster as $teamName => $members): ?>
                            <div class="p-0">
                                <div class="px-6 py-2 bg-gray-50/50 font-bold text-xs uppercase tracking-wider text-gray-500 border-b border-gray-50">
                                    <?= $teamName ?>
                                </div>
                                <div class="divide-y divide-gray-50">
                                    <?php foreach($members as $person): ?>
                                        <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 group">
                                            <div class="flex items-center gap-4">
                                                <div class="relative">
                                                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold">
                                                        <?= substr($person['first_name'], 0, 1) . substr($person['last_name'], 0, 1) ?>
                                                    </div>
                                                    <!-- Status Dot -->
                                                    <?php 
                                                        $statusColor = 'bg-yellow-400';
                                                        if($person['status'] === 'confirmed') $statusColor = 'bg-green-500';
                                                        if($person['status'] === 'declined') $statusColor = 'bg-red-500';
                                                    ?>
                                                    <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-white <?= $statusColor ?>"></div>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?= $person['first_name'] . ' ' . $person['last_name'] ?>
                                                    </div>
                                                    <div class="text-xs text-gray-500 font-medium">
                                                        <?= $person['role'] ?: 'Member' ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <!-- Status Toggle Form -->
                                                <form action="<?= APP_URL ?>/admin/services/updateStatus" method="POST" class="mr-2">
                                                    <input type="hidden" name="roster_id" value="<?= $person['id'] ?>">
                                                    <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                                                    <select name="status" onchange="this.form.submit()" class="text-xs border-gray-200 rounded py-1 px-2 focus:ring-indigo-500">
                                                        <option value="pending" <?= $person['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                        <option value="confirmed" <?= $person['status'] == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                                        <option value="declined" <?= $person['status'] == 'declined' ? 'selected' : '' ?>>Declined</option>
                                                    </select>
                                                </form>

                                                <form action="<?= APP_URL ?>/admin/services/removeRoster" method="POST" data-confirm-title="Remove from schedule" data-confirm="Remove this person from the service schedule?" data-confirm-button="Remove">
                                                    <input type="hidden" name="roster_id" value="<?= $person['id'] ?>">
                                                    <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                                                    <button type="submit" class="text-gray-400 hover:text-red-600 p-1" title="Remove">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Dynamic Team Member Loading
    document.getElementById('teamSelect').addEventListener('change', function() {
        const teamId = this.value;
        const memberSelect = document.getElementById('memberSelect');
        
        // Reset
        memberSelect.innerHTML = '<option value="">Loading...</option>';
        memberSelect.disabled = true;

        if(!teamId) {
            memberSelect.innerHTML = '<option value="">-- Select Team First --</option>';
            return;
        }

        // Fetch members via AJAX
        fetch(`<?= APP_URL ?>/admin/services/getTeamMembers/${teamId}`)
            .then(response => response.json())
            .then(data => {
                memberSelect.innerHTML = '<option value="">-- Select Member --</option>';
                if(data.length > 0) {
                    data.forEach(member => {
                        const option = document.createElement('option');
                        option.value = member.id;
                        option.textContent = `${member.first_name} ${member.last_name}`;
                        memberSelect.appendChild(option);
                    });
                    memberSelect.disabled = false;
                } else {
                    memberSelect.innerHTML = '<option value="">No members in this team</option>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                memberSelect.innerHTML = '<option value="">Error loading members</option>';
            });
    });
</script>

<?php require_once 'app/Views/layouts/admin_footer.php'; ?>
