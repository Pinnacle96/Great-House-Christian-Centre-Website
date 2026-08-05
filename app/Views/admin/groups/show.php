<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="px-6 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3">
                <span class="w-12 h-12 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                    <i class="fas fa-users text-2xl"></i>
                </span>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($group['name']) ?></h1>
                    <span class="text-sm px-2 py-0.5 rounded-md bg-gray-100 text-gray-600"><?= $group['type'] ?></span>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="<?= APP_URL ?>/admin/groups/edit/<?= $group['id'] ?>" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2">
                <i class="fas fa-edit"></i> Edit Details
            </a>
            <a href="<?= APP_URL ?>/admin/groups" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Group Info -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Overview</h3>
                <div class="space-y-4">
                    <div>
                        <span class="block text-xs text-gray-500 uppercase tracking-wider mb-1">Description</span>
                        <p class="text-sm text-gray-700 leading-relaxed"><?= $group['description'] ?: 'No description provided.' ?></p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="block text-xs text-gray-500 uppercase tracking-wider mb-1">Schedule</span>
                            <div class="flex items-center gap-2 text-sm font-medium text-gray-800">
                                <i class="fas fa-calendar-alt text-gray-400"></i>
                                <?= $group['schedule_info'] ?: 'TBD' ?>
                            </div>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500 uppercase tracking-wider mb-1">Location</span>
                            <div class="flex items-center gap-2 text-sm font-medium text-gray-800">
                                <i class="fas fa-map-marker-alt text-gray-400"></i>
                                <?= $group['location'] ?: 'TBD' ?>
                            </div>
                        </div>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500 uppercase tracking-wider mb-1">Primary Leader</span>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                                <?= $group['leader_name'] ? substr($group['leader_name'], 0, 1) : '?' ?>
                            </div>
                            <span class="text-sm font-medium text-gray-800"><?= $group['leader_name'] ?: 'Unassigned' ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="bg-indigo-50 rounded-xl p-6 border border-indigo-100">
                <h3 class="font-bold text-indigo-900 mb-4">Membership Stats</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white p-3 rounded-lg shadow-sm">
                        <span class="block text-2xl font-bold text-indigo-600"><?= count($members) ?></span>
                        <span class="text-xs text-gray-500 uppercase">Total Members</span>
                    </div>
                    <div class="bg-white p-3 rounded-lg shadow-sm">
                        <span class="block text-2xl font-bold text-green-600"><?= array_reduce($members, function($c, $m) { return $c + ($m['role'] === 'leader' ? 1 : 0); }, 0) ?></span>
                        <span class="text-xs text-gray-500 uppercase">Leaders</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Members List -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Add Member Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-user-plus text-green-500"></i> Add Member to Group
                </h3>
                <form action="<?= APP_URL ?>/admin/groups/addMember" method="POST" class="flex flex-col md:flex-row gap-4 items-end">
                    <input type="hidden" name="group_id" value="<?= $group['id'] ?>">
                    <div class="flex-1 w-full">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Select Member</label>
                        <select name="member_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500" required>
                            <option value="">-- Search Member --</option>
                            <?php foreach($allMembers as $m): ?>
                                <option value="<?= $m['id'] ?>"><?= $m['first_name'] . ' ' . $m['last_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="w-full md:w-48">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Role</label>
                        <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500">
                            <option value="member">Member</option>
                            <option value="leader">Leader</option>
                            <option value="co-leader">Co-Leader</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 font-medium text-sm w-full md:w-auto">
                        Add
                    </button>
                </form>
            </div>

            <!-- Members Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Group Roster</h3>
                    <div class="relative">
                        <input type="text" placeholder="Filter roster..." class="pl-8 pr-3 py-1 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-indigo-500">
                        <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    </div>
                </div>
                
                <?php if(empty($members)): ?>
                    <div class="p-8 text-center text-gray-500 italic">
                        No members assigned to this group yet.
                    </div>
                <?php else: ?>
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3">Member Name</th>
                                <th class="px-6 py-3">Role</th>
                                <th class="px-6 py-3">Joined</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach($members as $member): ?>
                                <tr class="hover:bg-gray-50 group">
                                    <td class="px-6 py-3 font-medium text-gray-900">
                                        <a href="<?= APP_URL ?>/admin/members/show/<?= $member['id'] ?>" class="hover:underline hover:text-indigo-600 flex items-center gap-3">
                                            <span class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center text-xs font-bold">
                                                <?= substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1) ?>
                                            </span>
                                            <?= $member['first_name'] . ' ' . $member['last_name'] ?>
                                        </a>
                                    </td>
                                    <td class="px-6 py-3">
                                        <form action="<?= APP_URL ?>/admin/groups/updateRole" method="POST" class="inline-block">
                                            <input type="hidden" name="group_id" value="<?= $group['id'] ?>">
                                            <input type="hidden" name="member_id" value="<?= $member['id'] ?>">
                                            <select name="role" onchange="this.form.submit()" class="text-xs border-0 bg-transparent font-semibold cursor-pointer focus:ring-0 py-0 pl-0 pr-6 
                                                <?= $member['role'] === 'leader' ? 'text-green-600' : ($member['role'] === 'co-leader' ? 'text-blue-600' : 'text-gray-600') ?>">
                                                <option value="member" <?= $member['role'] === 'member' ? 'selected' : '' ?>>Member</option>
                                                <option value="leader" <?= $member['role'] === 'leader' ? 'selected' : '' ?>>Leader</option>
                                                <option value="co-leader" <?= $member['role'] === 'co-leader' ? 'selected' : '' ?>>Co-Leader</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-6 py-3 text-gray-500">
                                        <?= $member['joined_at'] ? date('M d, Y', strtotime($member['joined_at'])) : '-' ?>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <form action="<?= APP_URL ?>/admin/groups/removeMember" method="POST" onsubmit="return confirm('Remove this member from the group?');" class="inline-block">
                                            <input type="hidden" name="group_id" value="<?= $group['id'] ?>">
                                            <input type="hidden" name="member_id" value="<?= $member['id'] ?>">
                                            <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors p-1" title="Remove Member">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'app/Views/layouts/admin_footer.php'; ?>
