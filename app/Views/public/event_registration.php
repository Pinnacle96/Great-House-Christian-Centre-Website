<?php require_once 'app/Views/layouts/header.php'; ?>

<!-- Hero Section (Mini) -->
<section class="relative bg-brand-green py-20">
    <div class="container mx-auto px-6 text-center text-white relative z-10">
        <h1 class="font-heading font-bold text-4xl mb-2">Event Registration</h1>
        <p class="text-xl opacity-90"><?= htmlspecialchars($event['title']) ?></p>
    </div>
</section>

<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="max-w-3xl mx-auto bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            
            <!-- Progress Bar -->
            <div class="bg-gray-100 h-2 w-full">
                <div class="bg-brand-gold h-full w-1/3 transition-all duration-500" id="progressBar"></div>
            </div>

            <div class="p-8 md:p-12">
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                        <span class="block sm:inline"><?= $_SESSION['error'] ?></span>
                        <?php unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <form action="<?= APP_URL ?>/events/<?= $event['slug'] ?>/register" method="POST" id="registrationForm" class="space-y-6">
                    
                    <!-- Step 1: Core Identity -->
                    <div id="step1" class="step-content">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-brand-green text-white flex items-center justify-center text-sm">1</span>
                            Core Identity
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">First Name <span class="text-red-500">*</span></label>
                                <input type="text" name="first_name" required class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Middle Name</label>
                                <input type="text" name="middle_name" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Last Name <span class="text-red-500">*</span></label>
                                <input type="text" name="last_name" required class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition-colors">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Gender <span class="text-red-500">*</span></label>
                                <select name="gender" required class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition-colors">
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Date of Birth</label>
                                <input type="date" name="dob" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition-colors">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                                <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Phone Number <span class="text-red-500">*</span></label>
                                <input type="tel" name="phone" required class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition-colors">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Home Address</label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <input type="text" name="address_city" placeholder="City" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition-colors">
                                <input type="text" name="address_state" placeholder="State" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition-colors">
                                <input type="text" name="address_country" placeholder="Country" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition-colors">
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="button" onclick="nextStep(2)" class="px-8 py-3 bg-brand-green text-white font-bold rounded-xl hover:bg-brand-green-dark transition-colors">
                                Next Step &rarr;
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Church & Role -->
                    <div id="step2" class="step-content hidden">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-brand-green text-white flex items-center justify-center text-sm">2</span>
                            Church Information
                        </h3>

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Church Name</label>
                            <input type="text" name="church_name" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition-colors">
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Church Location</label>
                            <input type="text" name="church_location" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition-colors">
                        </div>

                        <div class="mb-8">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Role in Church</label>
                            <select name="church_role" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition-colors">
                                <option value="Member">Member</option>
                                <option value="Worker">Worker</option>
                                <option value="Leader">Leader</option>
                                <option value="Pastor">Pastor</option>
                                <option value="Minister">Minister</option>
                                <option value="Guest" selected>Guest</option>
                            </select>
                        </div>

                        <div class="flex justify-between">
                            <button type="button" onclick="nextStep(1)" class="px-6 py-3 text-gray-600 font-bold hover:text-gray-900 transition-colors">
                                &larr; Back
                            </button>
                            <button type="button" onclick="nextStep(3)" class="px-8 py-3 bg-brand-green text-white font-bold rounded-xl hover:bg-brand-green-dark transition-colors">
                                Next Step &rarr;
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Conference Specifics -->
                    <div id="step3" class="step-content hidden">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-brand-green text-white flex items-center justify-center text-sm">3</span>
                            Conference Details
                        </h3>

                        <div class="mb-6">
                            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                                <input type="checkbox" name="is_first_time" value="1" class="w-5 h-5 text-brand-green border-gray-300 rounded focus:ring-brand-green">
                                <span class="text-gray-700 font-medium">Is this your first time attending?</span>
                            </label>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">How did you hear about this conference?</label>
                            <select name="referral_source" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition-colors">
                                <option value="">Select Option</option>
                                <option value="Social media">Social Media</option>
                                <option value="Church announcement">Church Announcement</option>
                                <option value="Referral">Referral / Friend</option>
                                <option value="Website">Website</option>
                                <option value="Flyer">Flyer</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Special Ministry Interests</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                <label class="flex items-center gap-2"><input type="checkbox" name="ministry_interests[]" value="Prayer" class="text-brand-green rounded"> Prayer</label>
                                <label class="flex items-center gap-2"><input type="checkbox" name="ministry_interests[]" value="Leadership" class="text-brand-green rounded"> Leadership</label>
                                <label class="flex items-center gap-2"><input type="checkbox" name="ministry_interests[]" value="Evangelism" class="text-brand-green rounded"> Evangelism</label>
                                <label class="flex items-center gap-2"><input type="checkbox" name="ministry_interests[]" value="Worship" class="text-brand-green rounded"> Worship</label>
                                <label class="flex items-center gap-2"><input type="checkbox" name="ministry_interests[]" value="Business" class="text-brand-green rounded"> Business</label>
                                <label class="flex items-center gap-2"><input type="checkbox" name="ministry_interests[]" value="Youth" class="text-brand-green rounded"> Youth</label>
                                <label class="flex items-center gap-2"><input type="checkbox" name="ministry_interests[]" value="Marriage" class="text-brand-green rounded"> Marriage</label>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Prayer Request</label>
                            <textarea name="prayer_request" rows="3" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition-colors" placeholder="Share your prayer request..."></textarea>
                        </div>
                        
                        <div class="mb-8">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Attendance Mode <span class="text-red-500">*</span></label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="attendance_mode" value="onsite" checked class="text-brand-green focus:ring-brand-green">
                                    <span>Onsite (In Person)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="attendance_mode" value="online" class="text-brand-green focus:ring-brand-green">
                                    <span>Online (Virtual)</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-between">
                            <button type="button" onclick="nextStep(2)" class="px-6 py-3 text-gray-600 font-bold hover:text-gray-900 transition-colors">
                                &larr; Back
                            </button>
                            <button type="submit" class="px-8 py-3 bg-brand-gold text-brand-green font-bold rounded-xl hover:bg-yellow-400 transition-colors shadow-lg">
                                Complete Registration
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</section>

<script>
function nextStep(step) {
    // Validate current step before moving forward
    if (step > 1) {
        const currentStep = document.getElementById('step' + (step - 1));
        const inputs = currentStep.querySelectorAll('input[required], select[required]');
        let valid = true;
        
        inputs.forEach(input => {
            if (!input.value) {
                valid = false;
                input.classList.add('border-red-500', 'bg-red-50');
            } else {
                input.classList.remove('border-red-500', 'bg-red-50');
            }
        });

        if (!valid) return;
    }

    // Update Progress Bar
    const progress = (step / 3) * 100;
    document.getElementById('progressBar').style.width = progress + '%';

    // Show/Hide Steps
    document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
    document.getElementById('step' + step).classList.remove('hidden');

    // Scroll to top of form
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

<?php require_once 'app/Views/layouts/footer.php'; ?>
