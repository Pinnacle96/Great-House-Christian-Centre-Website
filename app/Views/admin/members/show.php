<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-brand-green-100 flex items-center justify-center text-brand-green-600 text-2xl font-bold">
                <?= substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1) ?>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800"><?= $member['first_name'] . ' ' . $member['last_name'] ?></h1>
                <div class="flex items-center gap-3 mt-1">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                        <?= $member['membership_type'] ?>
                    </span>
                    <span class="text-gray-500 text-sm"><i class="fas fa-venus-mars mr-1"></i> <?= $member['gender'] ?></span>
                    <span class="text-gray-500 text-sm"><i class="fas fa-heart mr-1"></i> <?= $member['marital_status'] ?></span>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="<?= APP_URL ?>/admin/members/edit/<?= $member['id'] ?>" class="bg-white text-gray-700 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition-all flex items-center gap-2">
                <i class="fas fa-edit"></i> Edit Profile
            </a>
            <a href="<?= APP_URL ?>/admin/members" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-all flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Quick Info & Family -->
        <div class="space-y-8">
            <!-- Contact Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-address-card text-brand-green-500 mr-2"></i> Contact Info
                </h3>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 shrink-0">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500 uppercase tracking-wider">Email</span>
                            <a href="mailto:<?= $member['email'] ?>" class="text-brand-green-600 hover:underline"><?= $member['email'] ?></a>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 shrink-0">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500 uppercase tracking-wider">Phone</span>
                            <a href="tel:<?= $member['phone'] ?>" class="text-gray-800 hover:text-brand-green-600"><?= $member['phone'] ?></a>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 shrink-0">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500 uppercase tracking-wider">Address</span>
                            <span class="text-gray-800 block"><?= nl2br($member['address']) ?></span>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Family Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-users text-blue-500 mr-2"></i> Family Household
                </h3>
                <?php if ($family): ?>
                    <div class="mb-4">
                        <h4 class="font-bold text-gray-900"><?= $family['name'] ?></h4>
                        <span class="text-sm text-gray-500">Role: <?= $member['family_role'] ?></span>
                    </div>
                    <div class="space-y-3">
                        <?php foreach ($familyMembers as $famMember): ?>
                            <?php if ($famMember['id'] != $member['id']): ?>
                                <a href="<?= APP_URL ?>/admin/members/show/<?= $famMember['id'] ?>" class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg transition-colors group">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">
                                        <?= substr($famMember['first_name'], 0, 1) ?>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-800 group-hover:text-blue-600 transition-colors">
                                            <?= $famMember['first_name'] . ' ' . $famMember['last_name'] ?>
                                        </div>
                                        <div class="text-xs text-gray-500"><?= $famMember['family_role'] ?></div>
                                    </div>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 italic text-sm">Not assigned to a family unit.</p>
                    <a href="<?= APP_URL ?>/admin/members/edit/<?= $member['id'] ?>" class="text-sm text-blue-600 hover:underline mt-2 inline-block">Assign Family</a>
                <?php endif; ?>
            </div>

            <!-- Stats Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-line text-purple-500 mr-2"></i> Engagement
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-3 bg-purple-50 rounded-lg">
                        <div class="text-2xl font-bold text-purple-700"><?= $attendance['total_attended'] ?? 0 ?></div>
                        <div class="text-xs text-purple-600 uppercase tracking-wide">Services Attended</div>
                    </div>
                    <div class="text-center p-3 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-700"><?= count($groups) ?></div>
                        <div class="text-xs text-green-600 uppercase tracking-wide">Active Groups</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Middle & Right Column: Tabs for Notes, History, etc. -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Pastoral Notes Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 flex items-center">
                        <i class="fas fa-sticky-note text-yellow-500 mr-2"></i> Pastoral Notes
                    </h3>
                    <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded border">Private & Confidential</span>
                </div>
                
                <div class="p-6">
                    <!-- Add Note Form -->
                    <form action="<?= APP_URL ?>/admin/members/addNote" method="POST" class="mb-8 bg-yellow-50/50 p-4 rounded-xl border border-yellow-100">
                        <input type="hidden" name="member_id" value="<?= $member['id'] ?>">
                        <textarea name="note_content" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-transparent bg-white" placeholder="Add a pastoral note, prayer request, or observation..." required></textarea>
                        <div class="flex justify-between items-center mt-3">
                            <select name="visibility" class="text-sm border-gray-300 rounded-lg focus:ring-yellow-400">
                                <option value="private">Private (Me Only)</option>
                                <option value="pastor">Pastors Only</option>
                                <option value="admin">Admins Only</option>
                            </select>
                            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition-colors text-sm font-bold shadow-sm">
                                Add Note
                            </button>
                        </div>
                    </form>

                    <!-- Notes List -->
                    <div class="space-y-6">
                        <?php if (empty($notes)): ?>
                            <p class="text-center text-gray-400 py-4 italic">No notes recorded yet.</p>
                        <?php else: ?>
                            <?php foreach ($notes as $note): ?>
                                <div class="relative pl-6 border-l-2 border-gray-200 hover:border-brand-gold transition-colors">
                                    <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-gray-200 border-2 border-white"></div>
                                    <div class="mb-1 flex items-center gap-2">
                                        <span class="font-bold text-gray-800 text-sm"><?= $note['author_name'] ?></span>
                                        <span class="text-xs text-gray-500"><?= date('M j, Y \a\t g:i a', strtotime($note['created_at'])) ?></span>
                                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 border border-gray-200 capitalize"><?= $note['visibility'] ?></span>
                                    </div>
                                    <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-wrap"><?= $note['note_content'] ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Groups & Volunteer Roles -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Groups -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center justify-between">
                        <span><i class="fas fa-layer-group text-indigo-500 mr-2"></i> Small Groups</span>
                        <a href="<?= APP_URL ?>/admin/groups" class="text-xs text-indigo-600 hover:underline">Manage</a>
                    </h3>
                    <?php 
                    $smallGroups = array_filter($groups, function($g) { return $g['type'] !== 'Ministry Team'; });
                    if (empty($smallGroups)): 
                    ?>
                        <p class="text-sm text-gray-500 italic">Not in any groups.</p>
                    <?php else: ?>
                        <ul class="space-y-3">
                            <?php foreach ($smallGroups as $group): ?>
                                <li class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="font-medium text-gray-800"><?= $group['name'] ?></span>
                                    <span class="text-xs px-2 py-1 bg-indigo-100 text-indigo-700 rounded-full capitalize"><?= $group['role'] ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <!-- Volunteer Roles -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center justify-between">
                        <span><i class="fas fa-hands-helping text-orange-500 mr-2"></i> Serving</span>
                        <a href="<?= APP_URL ?>/admin/ministries" class="text-xs text-orange-600 hover:underline">Manage</a>
                    </h3>
                    <?php 
                    $ministryTeams = array_filter($groups, function($g) { return $g['type'] === 'Ministry Team'; });
                    if (empty($ministryTeams)): 
                    ?>
                        <p class="text-sm text-gray-500 italic">No volunteer roles assigned.</p>
                    <?php else: ?>
                        <ul class="space-y-3">
                            <?php foreach ($ministryTeams as $team): ?>
                                <li class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="font-medium text-gray-800"><?= $team['name'] ?></span>
                                    <span class="text-xs px-2 py-1 bg-orange-100 text-orange-700 rounded-full capitalize"><?= $team['role'] ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once 'app/Views/layouts/admin_footer.php'; ?>
