<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="px-6 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit User</h1>
            <p class="text-gray-600">Update user profile information</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="<?= APP_URL ?>/admin/users" class="bg-gray-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-gray-700 transition-all duration-200 flex items-center space-x-2">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Users</span>
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?= $_SESSION['error'] ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Profile Information Form -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Profile Information</h2>
            
            <form action="<?= APP_URL ?>/admin/users/update/<?= $user['id'] ?>" method="POST" class="space-y-4">
                <div class="grid grid-cols-1 gap-4">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input type="text" id="name" name="name" required 
                               value="<?= htmlspecialchars($user['name']) ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent transition-all duration-200">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" id="email" name="email" required 
                               value="<?= htmlspecialchars($user['email']) ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent transition-all duration-200">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent transition-all duration-200"
                               placeholder="+1 (555) 123-4567">
                    </div>

                    <!-- Role (only for admins) -->
                    <?php if ($_SESSION['role_id'] == 1): ?>
                        <div>
                            <label for="role_id" class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                            <select id="role_id" name="role_id" required 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent transition-all duration-200">
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>" <?= $user['role_id'] == $role['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($role['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="branch_id" class="block text-sm font-medium text-gray-700 mb-2">Branch</label>
                            <select id="branch_id" name="branch_id"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent transition-all duration-200">
                                <option value="">Global / Superadmin</option>
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?= $branch['id'] ?>" <?= (int)($user['branch_id'] ?? 0) === (int)$branch['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($branch['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" 
                            class="bg-gradient-to-r from-brand-green-600 to-brand-green-700 text-white px-6 py-3 rounded-lg shadow-md hover:from-brand-green-700 hover:to-brand-green-800 transition-all duration-200 flex items-center space-x-2">
                        <i class="fas fa-save"></i>
                        <span>Update Profile</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Change Password Form -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Change Password</h2>
            
            <form action="<?= APP_URL ?>/admin/users/change-password/<?= $user['id'] ?>" method="POST" class="space-y-4">
                <div class="grid grid-cols-1 gap-4">
                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent transition-all duration-200"
                               placeholder="Enter current password">
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <input type="password" id="new_password" name="new_password" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent transition-all duration-200"
                               placeholder="Minimum 6 characters">
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent transition-all duration-200"
                               placeholder="Confirm new password">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" 
                            class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-lg shadow-md hover:from-blue-700 hover:to-blue-800 transition-all duration-200 flex items-center space-x-2">
                        <i class="fas fa-key"></i>
                        <span>Change Password</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- User Info Card -->
    <div class="mt-8 bg-gray-50 rounded-xl p-6">
        <h3 class="text-md font-semibold text-gray-800 mb-4">User Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-600">User ID:</span>
                <span class="font-medium">#<?= $user['id'] ?></span>
            </div>
            <div>
                <span class="text-gray-600">Created:</span>
                <span class="font-medium"><?= date('M j, Y \a\t g:i A', strtotime($user['created_at'])) ?></span>
            </div>
            <div>
                <span class="text-gray-600">Last Updated:</span>
                <span class="font-medium"><?= date('M j, Y \a\t g:i A', strtotime($user['updated_at'])) ?></span>
            </div>
        </div>
    </div>
</div>

<?php require_once 'app/Views/layouts/admin_footer.php'; ?>
