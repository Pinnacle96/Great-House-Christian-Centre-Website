<?php require_once 'app/Views/layouts/admin_header.php'; ?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-user-plus text-brand-green-600 mr-2"></i>
            Manual Registration
        </h1>
        <a href="<?= APP_URL ?>/admin/registrations" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-1"></i> Back to List
        </a>
    </div>

    <form action="<?= APP_URL ?>/admin/registrations/store" method="POST" class="bg-white rounded-lg shadow-md p-6">
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline"><?= $_SESSION['error'] ?></span>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Event Selection -->
        <div class="mb-8">
            <label class="block text-sm font-bold text-gray-700 mb-2">Select Event <span class="text-red-500">*</span></label>
            <select name="event_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                <option value="">Choose an event...</option>
                <?php foreach ($events as $event): ?>
                    <option value="<?= $event['id'] ?>" <?= ($selected_event_id == $event['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($event['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Section 1: Core Identity -->
        <div class="mb-8 border-b border-gray-200 pb-8">
            <h3 class="text-lg font-bold text-brand-green-600 mb-4">1. Core Identity</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                    <input type="text" name="first_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-green-500 focus:border-brand-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                    <input type="text" name="middle_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-green-500 focus:border-brand-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                    <input type="text" name="last_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-green-500 focus:border-brand-green-500">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                    <select name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-green-500 focus:border-brand-green-500">
                        <option value="">Select...</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                    <input type="date" name="dob" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-green-500 focus:border-brand-green-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                    <input type="tel" name="phone" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-green-500 focus:border-brand-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-green-500 focus:border-brand-green-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input type="text" name="address_city" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-green-500 focus:border-brand-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                    <input type="text" name="address_state" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-green-500 focus:border-brand-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                    <input type="text" name="address_country" value="Nigeria" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-green-500 focus:border-brand-green-500">
                </div>
            </div>
        </div>

        <!-- Section 2: Church Info -->
        <div class="mb-8 border-b border-gray-200 pb-8">
            <h3 class="text-lg font-bold text-brand-green-600 mb-4">2. Church Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Church Name</label>
                    <input type="text" name="church_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-green-500 focus:border-brand-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Church Location</label>
                    <input type="text" name="church_location" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-green-500 focus:border-brand-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role in Church</label>
                    <select name="church_role" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-green-500 focus:border-brand-green-500">
                        <option value="Guest" selected>Guest</option>
                        <option value="Member">Member</option>
                        <option value="Worker">Worker</option>
                        <option value="Leader">Leader</option>
                        <option value="Pastor">Pastor</option>
                        <option value="Minister">Minister</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Section 3: Conference Details -->
        <div class="mb-8">
            <h3 class="text-lg font-bold text-brand-green-600 mb-4">3. Conference Details</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div class="flex items-center mt-6">
                    <input type="checkbox" name="is_first_time" value="1" class="h-4 w-4 text-brand-green-600 focus:ring-brand-green-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-900">First-time Attendee</label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Referral Source</label>
                    <select name="referral_source" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-green-500 focus:border-brand-green-500">
                        <option value="">Select...</option>
                        <option value="Social media">Social Media</option>
                        <option value="Church announcement">Church Announcement</option>
                        <option value="Referral">Referral / Friend</option>
                        <option value="Website">Website</option>
                        <option value="Flyer">Flyer</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Ministry Interests</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    <label class="inline-flex items-center"><input type="checkbox" name="ministry_interests[]" value="Prayer" class="rounded text-brand-green-600"> <span class="ml-2 text-sm">Prayer</span></label>
                    <label class="inline-flex items-center"><input type="checkbox" name="ministry_interests[]" value="Leadership" class="rounded text-brand-green-600"> <span class="ml-2 text-sm">Leadership</span></label>
                    <label class="inline-flex items-center"><input type="checkbox" name="ministry_interests[]" value="Evangelism" class="rounded text-brand-green-600"> <span class="ml-2 text-sm">Evangelism</span></label>
                    <label class="inline-flex items-center"><input type="checkbox" name="ministry_interests[]" value="Worship" class="rounded text-brand-green-600"> <span class="ml-2 text-sm">Worship</span></label>
                    <label class="inline-flex items-center"><input type="checkbox" name="ministry_interests[]" value="Business" class="rounded text-brand-green-600"> <span class="ml-2 text-sm">Business</span></label>
                    <label class="inline-flex items-center"><input type="checkbox" name="ministry_interests[]" value="Youth" class="rounded text-brand-green-600"> <span class="ml-2 text-sm">Youth</span></label>
                    <label class="inline-flex items-center"><input type="checkbox" name="ministry_interests[]" value="Marriage" class="rounded text-brand-green-600"> <span class="ml-2 text-sm">Marriage</span></label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Prayer Request</label>
                <textarea name="prayer_request" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-green-500 focus:border-brand-green-500"></textarea>
            </div>
        </div>

        <div class="flex justify-end pt-6 border-t border-gray-200">
            <button type="submit" class="bg-brand-green text-white px-6 py-3 rounded-lg shadow-md hover:bg-brand-green-dark transition-colors font-bold">
                <i class="fas fa-check mr-2"></i> Register & Check-in
            </button>
        </div>

    </form>
</div>

<?php require_once 'app/Views/layouts/admin_footer.php'; ?>
