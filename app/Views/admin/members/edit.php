<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Member</h1>
            <p class="text-gray-600">Update member information</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="<?= APP_URL ?>/admin/members/show/<?= $member['id'] ?>" class="bg-gray-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-gray-700 transition-all duration-200 flex items-center space-x-2">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Profile</span>
            </a>
        </div>
    </div>

    <!-- Member Form -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="border-b border-gray-200 bg-gray-50 px-8 py-4">
            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                <i class="fas fa-user-edit text-brand-green-600 mr-3"></i>
                Edit Member Profile
            </h2>
        </div>
        
        <form action="<?= APP_URL ?>/admin/members/update/<?= $member['id'] ?>" method="POST" class="p-8 space-y-8">
            
            <!-- Section 1: Personal Information -->
            <div>
                <h3 class="text-md font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">
                    <i class="fas fa-id-card text-brand-gold-500 mr-2"></i> Personal Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                        <input type="text" name="first_name" value="<?= htmlspecialchars($member['first_name']) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                        <input type="text" name="last_name" value="<?= htmlspecialchars($member['last_name']) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                        <select name="gender" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                            <option value="">Select Gender</option>
                            <option value="Male" <?= $member['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= $member['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                        <input type="date" name="dob" value="<?= $member['dob'] ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Marital Status</label>
                        <select name="marital_status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                            <option value="Single" <?= $member['marital_status'] == 'Single' ? 'selected' : '' ?>>Single</option>
                            <option value="Married" <?= $member['marital_status'] == 'Married' ? 'selected' : '' ?>>Married</option>
                            <option value="Divorced" <?= $member['marital_status'] == 'Divorced' ? 'selected' : '' ?>>Divorced</option>
                            <option value="Widowed" <?= $member['marital_status'] == 'Widowed' ? 'selected' : '' ?>>Widowed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                            <option value="active" <?= $member['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $member['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 2: Contact Information -->
            <div>
                <h3 class="text-md font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">
                    <i class="fas fa-address-book text-brand-gold-500 mr-2"></i> Contact Details
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($member['email']) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($member['phone']) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Residential Address</label>
                        <textarea name="address" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent"><?= htmlspecialchars($member['address']) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Section 3: Family & Relationships -->
            <div class="bg-blue-50 p-6 rounded-lg border border-blue-100">
                <h3 class="text-md font-semibold text-blue-800 mb-4">
                    <i class="fas fa-users text-blue-600 mr-2"></i> Family Grouping
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Family Unit</label>
                        <select name="family_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                            <option value="">-- No Family Assigned --</option>
                            <?php foreach($families as $f): ?>
                                <option value="<?= $f['id'] ?>" <?= $member['family_id'] == $f['id'] ? 'selected' : '' ?>><?= $f['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Family Role</label>
                        <select name="family_role" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                            <option value="Head" <?= $member['family_role'] == 'Head' ? 'selected' : '' ?>>Head of Household</option>
                            <option value="Spouse" <?= $member['family_role'] == 'Spouse' ? 'selected' : '' ?>>Spouse</option>
                            <option value="Child" <?= $member['family_role'] == 'Child' ? 'selected' : '' ?>>Child</option>
                            <option value="Other" <?= $member['family_role'] == 'Other' ? 'selected' : '' ?>>Other Relative</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 4: Church Membership Info -->
            <div>
                <h3 class="text-md font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">
                    <i class="fas fa-church text-brand-gold-500 mr-2"></i> Membership Details
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Branch</label>
                        <select name="branch_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                            <?php foreach($branches as $branch): ?>
                                <option value="<?= $branch['id'] ?>" <?= (int)($member['branch_id'] ?? 0) === (int)$branch['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($branch['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Membership Type</label>
                        <select name="membership_type" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                            <option value="Guest" <?= $member['membership_type'] == 'Guest' ? 'selected' : '' ?>>Guest</option>
                            <option value="Regular Attender" <?= $member['membership_type'] == 'Regular Attender' ? 'selected' : '' ?>>Regular Attender</option>
                            <option value="Member" <?= $member['membership_type'] == 'Member' ? 'selected' : '' ?>>Member</option>
                            <option value="Leader" <?= $member['membership_type'] == 'Leader' ? 'selected' : '' ?>>Leader</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Occupation</label>
                        <input type="text" name="occupation" value="<?= htmlspecialchars($member['occupation']) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Joined Date</label>
                        <input type="date" name="joined_at" value="<?= $member['joined_at'] ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-8 border-t border-gray-100">
                <button type="submit" class="bg-gradient-to-r from-brand-green-600 to-brand-green-700 text-white px-8 py-4 rounded-lg shadow-lg hover:from-brand-green-700 hover:to-brand-green-800 transition-all duration-200 flex items-center space-x-2 transform hover:-translate-y-1">
                    <i class="fas fa-save fa-lg"></i>
                    <span class="font-bold text-lg">Update Member Profile</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'app/Views/layouts/admin_footer.php'; ?>
