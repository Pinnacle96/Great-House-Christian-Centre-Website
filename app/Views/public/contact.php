<?php 
require_once 'app/Views/layouts/header.php'; 
use App\Models\Setting;

$contactBranch = $headquarters ?? [];
$contactValue = function ($contentKey, $settingKey, $branchKey, $fallback) use ($content, $contactBranch) {
    $branchValue = trim((string)($contactBranch[$branchKey] ?? ''));
    if ($branchValue !== '') {
        return $branchValue;
    }

    $contentValue = trim((string)($content['contact_details'][$contentKey]['value'] ?? ''));
    if ($contentValue !== '') {
        return $contentValue;
    }

    $settingValue = trim((string)Setting::get($settingKey, ''));
    return $settingValue !== '' ? $settingValue : $fallback;
};

$contactAddress = $contactValue('address', 'address', 'address', 'The Fulfilment Place, 7 Raimi Omole Street, Imo, Ilesa, Osun State');
$contactPhone = $contactValue('phone', 'contact_phone', 'phone', '0811 417 3016');
$contactEmail = $contactValue('email', 'contact_email', 'email', 'info@ghccng.org');
?>

<?php if (!empty($_SESSION['success']) || !empty($_SESSION['error'])): ?>
    <div class="container mx-auto px-6 md:px-12 lg:px-16 pt-8">
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="rounded-xl border border-green-200 bg-green-50 px-6 py-4 text-green-800 font-medium">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="rounded-xl border border-red-200 bg-red-50 px-6 py-4 text-red-800 font-medium">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Modern Hero Section -->
<section class="relative min-h-[60vh] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 w-full h-full">
        <div class="absolute inset-0 bg-gradient-to-br from-brand-green/90 via-brand-green/70 to-brand-green-dark/90 z-10"></div>
        <div class="w-full h-full bg-gradient-to-br from-brand-green to-brand-green-dark"></div>
        <!-- Decorative Overlays -->
        <div class="absolute inset-0 z-12 opacity-20" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
    </div>
    
    <div class="relative z-20 text-center text-white container mx-auto px-6">
        <span class="inline-block px-5 py-2 bg-white/10 backdrop-blur-md rounded-full text-[10px] font-bold mb-8 border border-white/20 tracking-[0.3em] uppercase animate-fade-in-down">
            <?= $content['hero']['badge']['value'] ?? 'GET IN TOUCH' ?>
        </span>
        <h1 class="font-heading font-bold text-6xl md:text-8xl mb-8 leading-tight tracking-tight animate-fade-in-up">
            <?= isset($content['hero']['title']['value'])
                ? preg_replace('/\*(.*?)\*/', '<span class="text-brand-gold">$1</span>', htmlspecialchars($content['hero']['title']['value']))
                : 'Contact Us' ?>
        </h1>
        <div class="prose prose-lg prose-invert max-w-2xl mx-auto text-white/80 font-light leading-relaxed animate-fade-in-up delay-200">
            <?= nl2br(htmlspecialchars($content['hero']['subtitle']['value'] ?? "We're here to serve and support you on your spiritual journey. Reach out to us anytime.")) ?>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-12 left-1/2 -translate-x-1/2 z-20 hidden md:block">
        <div class="flex flex-col items-center gap-4">
            <span class="text-[10px] font-bold tracking-[0.3em] text-white/40 uppercase rotate-90 mb-8">SCROLL</span>
            <div class="w-[1px] h-24 bg-gradient-to-b from-brand-gold/60 to-transparent"></div>
        </div>
    </div>

    <!-- Bottom Gradient Fade -->
    <div class="absolute bottom-0 left-0 w-full h-48 bg-gradient-to-t from-white via-white/80 to-transparent z-15"></div>
</section>

<!-- Contact Content -->
<section class="py-40 bg-white relative overflow-hidden">
    <!-- Background Accents -->
    <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-brand-green/5 rounded-full blur-[120px] translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-brand-gold/5 rounded-full blur-[120px] -translate-x-1/2 translate-y-1/2"></div>

    <div class="container mx-auto px-6 md:px-12 lg:px-16 relative z-10 max-w-7xl">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-24 lg:gap-32">
            <!-- Contact Information -->
            <div class="space-y-16">
                <div>
                    <span class="inline-block px-4 py-1.5 bg-brand-green/10 text-brand-green rounded-full text-[10px] font-bold mb-8 tracking-[0.4em] uppercase border border-brand-green/20">
                        <?= $content['connect']['badge']['value'] ?? 'CONNECT WITH US' ?>
                    </span>
                    <h2 class="font-heading font-bold text-5xl md:text-6xl text-gray-900 mb-10 leading-tight tracking-tight">
                        <?= isset($content['connect']['title']['value'])
                            ? preg_replace('/\*(.*?)\*/', '<span class="text-brand-green">$1</span>', htmlspecialchars($content['connect']['title']['value']))
                            : 'Get in <span class="text-brand-green">Touch</span>' ?>
                    </h2>
                    <div class="prose prose-lg text-gray-600 font-light leading-relaxed">
                        <?= nl2br(htmlspecialchars($content['connect']['subtitle']['value'] ?? 'Reach out to us for any questions, prayer requests, or to learn more about our ministry and how you can get involved.')) ?>
                    </div>
                </div>

                <!-- Contact Details -->
                <div class="space-y-12">
                    <div class="flex items-start group">
                        <div class="w-16 h-16 bg-brand-green/10 rounded-2xl flex items-center justify-center mr-8 group-hover:bg-brand-green group-hover:rotate-12 transition-all duration-500 shadow-xl shadow-brand-green/5">
                            <span class="text-3xl group-hover:scale-110 transition-transform">📍</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-2xl text-gray-900 mb-2 group-hover:text-brand-green transition-colors"><?= $content['contact_details']['location_title']['value'] ?? 'Our Location' ?></h3>
                            <p class="text-xl text-gray-500 font-light leading-relaxed"><?= htmlspecialchars($contactAddress) ?></p>
                        </div>
                    </div>

                    <div class="flex items-start group">
                        <div class="w-full bg-brand-green/10 rounded-2xl overflow-hidden shadow-xl shadow-brand-green/5 h-96">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63272.19626629624!2d4.687356448632801!3d7.627925400000014!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1038185607a01587%3A0xfc355a72c10c11ec!2sGREAT%20HOUSE%20CHRISTIAN%20CENTER!5e0!3m2!1sen!2sng!4v1772541096361!5m2!1sen!2sng" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>

                    <div class="flex items-start group">
                        <div class="w-16 h-16 bg-brand-green/10 rounded-2xl flex items-center justify-center mr-8 group-hover:bg-brand-green group-hover:-rotate-12 transition-all duration-500 shadow-xl shadow-brand-green/5">
                            <span class="text-3xl group-hover:scale-110 transition-transform">📞</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-2xl text-gray-900 mb-2 group-hover:text-brand-green transition-colors"><?= $content['contact_details']['phone_title']['value'] ?? 'Phone Number' ?></h3>
                            <p class="text-xl text-gray-500 font-light leading-relaxed"><?= htmlspecialchars($contactPhone) ?></p>
                        </div>
                    </div>

                    <div class="flex items-start group">
                        <div class="w-16 h-16 bg-brand-green/10 rounded-2xl flex items-center justify-center mr-8 group-hover:bg-brand-green group-hover:rotate-12 transition-all duration-500 shadow-xl shadow-brand-green/5">
                            <span class="text-3xl group-hover:scale-110 transition-transform">✉️</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-2xl text-gray-900 mb-2 group-hover:text-brand-green transition-colors"><?= $content['contact_details']['email_title']['value'] ?? 'Email Address' ?></h3>
                            <p class="text-xl text-gray-500 font-light leading-relaxed"><?= htmlspecialchars($contactEmail) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Prayer Request Card -->
                <div class="bg-gradient-to-br from-brand-green/5 via-white to-brand-green-dark/5 rounded-[3rem] p-12 border border-brand-green/10 group hover:shadow-2xl transition-all duration-700 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-brand-green/5 rounded-bl-[4rem] -mr-8 -mt-8"></div>
                    <div class="flex items-center mb-8">
                        <div class="w-16 h-16 bg-brand-green/20 rounded-[1.5rem] flex items-center justify-center mr-6 group-hover:rotate-12 transition-transform duration-500 shadow-lg">
                            <span class="text-3xl">🙏</span>
                        </div>
                        <h3 class="font-bold text-3xl text-gray-900 group-hover:text-brand-green transition-colors">
                            <?= isset($content['prayer_card']['title']['value'])
                                ? preg_replace('/\*(.*?)\*/', '<span class="text-brand-green">$1</span>', htmlspecialchars($content['prayer_card']['title']['value']))
                                : 'Need Prayer?' ?>
                        </h3>
                    </div>
                    <div class="prose text-gray-500 font-light leading-relaxed mb-10">
                        <?= nl2br(htmlspecialchars($content['prayer_card']['subtitle']['value'] ?? 'We believe in the power of prayer. Send us your prayer requests and our prayer team will intercede for you.')) ?>
                    </div>
                    <a href="#prayer-request" class="inline-flex items-center gap-4 text-brand-green font-bold hover:text-brand-green-dark transition-all group/link">
                        <?= $content['prayer_card']['cta_text']['value'] ?? 'SUBMIT PRAYER REQUEST' ?>
                        <span class="w-10 h-10 bg-brand-green/10 rounded-full flex items-center justify-center group-hover/link:bg-brand-green group-hover/link:text-white transition-all duration-300 transform group-hover/link:translate-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </span>
                    </a>
                </div>
            </div>

            <!-- Contact Forms -->
            <div class="space-y-16">
                <!-- General Contact Form -->
                <div class="bg-gray-50 rounded-[3.5rem] p-12 lg:p-16 border border-gray-100 shadow-sm relative">
                    <div class="absolute top-12 right-12 text-6xl text-brand-green/5 font-bold pointer-events-none">01</div>
                    <h2 class="font-heading font-bold text-3xl md:text-4xl text-gray-900 mb-10 tracking-tight">Send a Message</h2>
                    <form action="<?= APP_URL ?>/contact/process" method="POST" class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-gray-700 font-bold text-xs uppercase tracking-widest mb-3 ml-1">Full Name</label>
                                <input type="text" name="name" placeholder="John Doe" class="w-full bg-white border-0 rounded-2xl px-6 py-5 focus:outline-none focus:ring-2 focus:ring-brand-green shadow-sm transition-all" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold text-xs uppercase tracking-widest mb-3 ml-1">Email Address</label>
                                <input type="email" name="email" placeholder="john@example.com" class="w-full bg-white border-0 rounded-2xl px-6 py-5 focus:outline-none focus:ring-2 focus:ring-brand-green shadow-sm transition-all" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold text-xs uppercase tracking-widest mb-3 ml-1">Subject</label>
                            <input type="text" name="subject" placeholder="How can we help?" class="w-full bg-white border-0 rounded-2xl px-6 py-5 focus:outline-none focus:ring-2 focus:ring-brand-green shadow-sm transition-all" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold text-xs uppercase tracking-widest mb-3 ml-1">Your Message</label>
                            <textarea name="message" rows="5" placeholder="Write your message here..." class="w-full bg-white border-0 rounded-2xl px-6 py-5 focus:outline-none focus:ring-2 focus:ring-brand-green shadow-sm transition-all resize-none" required></textarea>
                        </div>
                        <button type="submit" class="group relative w-full bg-brand-green text-white font-bold py-6 px-8 rounded-2xl transition-all duration-500 hover:bg-brand-gold hover:text-brand-green overflow-hidden shadow-[0_20px_40px_-10px_rgba(20,68,44,0.3)] tracking-widest uppercase text-xs">
                            <span class="relative z-10">SEND MESSAGE</span>
                            <div class="absolute inset-0 bg-brand-gold translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                        </button>
                    </form>
                </div>

                <!-- Prayer Request Form -->
                <div id="prayer-request" class="bg-gradient-to-br from-brand-green via-brand-green to-brand-green-dark rounded-[3.5rem] p-12 lg:p-16 border border-brand-green/20 shadow-2xl relative overflow-hidden">
                    <div class="absolute top-12 right-12 text-6xl text-white/10 font-bold pointer-events-none">02</div>
                    <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 30px 30px;"></div>
                    
                    <h2 class="font-heading font-bold text-3xl md:text-4xl text-white mb-10 tracking-tight relative z-10">Prayer Request</h2>
                    <form action="<?= APP_URL ?>/prayer/submit" method="POST" class="space-y-8 relative z-10">
                        <div>
                            <label class="block text-white/70 font-bold text-[10px] uppercase tracking-widest mb-3 ml-1">Name (Optional)</label>
                            <input type="text" name="name" placeholder="Leave blank for anonymous" class="w-full bg-white/10 border border-white/20 text-white placeholder-white/40 rounded-2xl px-6 py-5 focus:outline-none focus:ring-2 focus:ring-brand-gold backdrop-blur-md transition-all">
                        </div>
                        <div>
                            <label class="block text-white/70 font-bold text-[10px] uppercase tracking-widest mb-3 ml-1">Your Prayer Request</label>
                            <textarea name="request" rows="5" placeholder="Tell us how we can pray for you..." class="w-full bg-white/10 border border-white/20 text-white placeholder-white/40 rounded-2xl px-6 py-5 focus:outline-none focus:ring-2 focus:ring-brand-gold backdrop-blur-md transition-all resize-none" required></textarea>
                        </div>
                        <div class="flex items-center bg-white/5 p-4 rounded-xl border border-white/10">
                            <input type="checkbox" name="is_public" id="is_public" class="w-5 h-5 text-brand-gold focus:ring-brand-gold border-white/20 bg-transparent rounded">
                            <label for="is_public" class="ml-4 text-white/80 text-sm font-light">Share anonymously on our Prayer Wall?</label>
                        </div>
                        <button type="submit" class="group relative w-full bg-white text-brand-green font-bold py-6 px-8 rounded-2xl transition-all duration-500 hover:bg-brand-gold hover:text-brand-green overflow-hidden shadow-2xl tracking-widest uppercase text-xs">
                            <span class="relative z-10">SUBMIT PRAYER REQUEST</span>
                            <div class="absolute inset-0 bg-brand-gold translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-40 bg-brand-green relative overflow-hidden">
    <!-- Animated background elements -->
    <div class="absolute top-0 left-0 w-[600px] h-[600px] bg-white/5 rounded-full blur-[120px] -translate-x-1/2 -translate-y-1/2 animate-pulse"></div>
    <div class="absolute bottom-0 right-0 w-[700px] h-[700px] bg-brand-gold/10 rounded-full blur-[120px] translate-x-1/3 translate-y-1/3"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full opacity-[0.03] pointer-events-none" 
         style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
    
    <div class="container mx-auto px-6 md:px-12 lg:px-16 text-center relative z-10">
        <div class="max-w-4xl mx-auto">
            <span class="inline-block px-5 py-2 bg-white/10 text-white rounded-full text-[10px] font-bold mb-10 tracking-[0.4em] uppercase backdrop-blur-sm border border-white/20">
                <?= $content['cta']['badge']['value'] ?? 'VISIT US TODAY' ?>
            </span>
            <h2 class="font-heading font-bold text-5xl md:text-7xl lg:text-8xl text-white mb-10 leading-[1.1] tracking-tight">
                <?= isset($content['cta']['title']['value'])
                    ? preg_replace('/\*(.*?)\*/', '<span class="text-brand-gold">$1</span>', htmlspecialchars($content['cta']['title']['value']))
                    : 'Experience God\'s Presence This Weekend' ?>
            </h2>
            <div class="prose prose-lg prose-invert text-white/80 mb-16 font-light leading-relaxed max-w-2xl mx-auto">
                <?= nl2br(htmlspecialchars($content['cta']['subtitle']['value'] ?? "Join us for a transformative experience of worship, word, and community. We can't wait to welcome you home.")) ?>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-center gap-8">
                <a href="<?= APP_URL ?>/services" 
                   class="group relative px-12 py-6 bg-white text-brand-green font-bold rounded-2xl transition-all duration-500 hover:bg-brand-gold hover:text-brand-green overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.3)] tracking-widest uppercase text-xs">
                    <span class="relative z-10">SERVICE TIMES</span>
                    <div class="absolute inset-0 bg-brand-gold translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                </a>
                <a href="<?= APP_URL ?>/events" 
                   class="px-12 py-6 bg-transparent border-2 border-white/30 text-white font-bold rounded-2xl hover:bg-white hover:text-brand-green transition-all duration-500 transform hover:scale-105 backdrop-blur-sm tracking-widest uppercase text-xs">
                    UPCOMING EVENTS
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'app/Views/layouts/footer.php'; ?>
