<?php require_once 'app/Views/layouts/header.php'; ?>

<section class="relative bg-brand-green py-24 md:py-32 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-brand-green via-brand-green to-brand-green-dark"></div>
    <div class="container mx-auto px-6 md:px-12 lg:px-16 relative z-10 text-white">
        <div class="max-w-3xl">
            <span class="inline-block px-4 py-1.5 bg-white/10 border border-white/20 rounded-full text-xs font-bold uppercase tracking-wider mb-6">
                Member Registration
            </span>
            <h1 class="font-heading font-bold text-4xl md:text-6xl leading-tight mb-6">
                <?= htmlspecialchars($branch['name']) ?>
            </h1>
            <p class="text-white/80 text-lg md:text-xl leading-relaxed">
                Complete the form below to register your membership information with this branch.
            </p>
        </div>
    </div>
</section>

<section class="py-16 md:py-24 bg-gray-50">
    <div class="container mx-auto px-6 md:px-12 lg:px-16 max-w-5xl">
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="mb-8 rounded-lg border border-green-200 bg-green-50 px-6 py-4 text-green-800 font-medium">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="mb-8 rounded-lg border border-red-200 bg-red-50 px-6 py-4 text-red-800 font-medium">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 md:px-8 py-5 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-900">Your Information</h2>
                <p class="text-sm text-gray-500 mt-1">Fields marked with * are required.</p>
            </div>

            <form action="<?= APP_URL ?>/b/<?= htmlspecialchars($branch['registration_token']) ?>/register" method="POST" class="p-6 md:p-8 space-y-8">
                <div>
                    <h3 class="font-bold text-gray-800 mb-4">Personal Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                            <input type="text" name="first_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                            <input type="text" name="last_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                            <select name="gender" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                            <input type="date" name="dob" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Marital Status</label>
                            <select name="marital_status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                                <option value="">Select Status</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Divorced">Divorced</option>
                                <option value="Widowed">Widowed</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Occupation</label>
                            <input type="text" name="occupation" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="font-bold text-gray-800 mb-4">Contact Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                            <input type="text" name="phone" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea name="address" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-green-500 focus:border-transparent"></textarea>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <p class="text-sm text-gray-500">Your information will be submitted directly to <?= htmlspecialchars($branch['name']) ?>.</p>
                    <button type="submit" class="bg-brand-green text-white px-8 py-4 rounded-lg font-bold hover:bg-brand-green-dark transition">
                        Submit Registration
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require_once 'app/Views/layouts/footer.php'; ?>
