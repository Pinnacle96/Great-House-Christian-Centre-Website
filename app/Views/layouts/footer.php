<?php
use App\Models\Branch;
use App\Models\PageContent;
use App\Models\Setting;

$headquarters = (new Branch())->headquarters() ?: [];
$footerContact = PageContent::getPageContent('contact');
$footerValue = function ($contentKey, $settingKey, $branchKey, $fallback) use ($footerContact, $headquarters) {
    $branchValue = trim((string)($headquarters[$branchKey] ?? ''));
    if ($branchValue !== '') {
        return $branchValue;
    }

    $contentValue = trim((string)($footerContact['contact_details'][$contentKey]['value'] ?? ''));
    if ($contentValue !== '') {
        return $contentValue;
    }

    $settingValue = trim((string)Setting::get($settingKey, ''));
    if ($settingValue !== '') {
        return $settingValue;
    }

    return $fallback;
};

$f_address = $footerValue('address', 'address', 'address', 'The Fulfilment Place, 7 Raimi Omole Street, Imo, Ilesa, Osun State');
$f_phone = $footerValue('phone', 'contact_phone', 'phone', '0811 417 3016');
$f_email = $footerValue('email', 'contact_email', 'email', 'info@ghccng.org');
?>
    </main>
    <footer class="bg-brand-green text-white relative overflow-hidden">
        <!-- Decorative Background Element -->
        <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-96 h-96 bg-brand-gold/5 rounded-full blur-3xl"></div>
        
        <div class="container mx-auto px-6 md:px-12 lg:px-16 pt-24 pb-12 relative z-10">
            <!-- Main Footer Content -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-16 mb-20">
                <!-- Church Identity -->
                <div class="space-y-8">
                    <a href="<?= APP_URL ?>" class="inline-flex items-center gap-4 group">
                        <div class="p-2 bg-white/10 rounded-xl group-hover:bg-brand-gold/20 transition-colors duration-300">
                            <img src="<?= APP_URL ?>/assets/logo/ghcc_logo.png" alt="GHCC Logo" class="h-10 brightness-0 invert">
                        </div>
                        <span class="font-heading font-bold text-3xl tracking-tight">GHCC</span>
                    </a>
                    <p class="text-gray-300 text-lg leading-relaxed font-light">
                        Helping men find fulfilment in life through Christ. Join the GHCC family across our centres.
                    </p>
                    
                    <!-- Social Media -->
                    <div class="flex items-center gap-4">
                        <a href="#" class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-brand-gold hover:border-brand-gold hover:text-brand-green transition-all duration-300 group" title="Follow us on Facebook">
                            <svg class="w-6 h-6 text-white group-hover:text-brand-green group-hover:scale-110 transition-all" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-brand-gold hover:border-brand-gold hover:text-brand-green transition-all duration-300 group" title="Subscribe on YouTube">
                            <svg class="w-6 h-6 text-white group-hover:text-brand-green group-hover:scale-110 transition-all" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-brand-gold hover:border-brand-gold hover:text-brand-green transition-all duration-300 group" title="Follow us on Instagram">
                            <svg class="w-6 h-6 text-white group-hover:text-brand-green group-hover:scale-110 transition-all" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204 013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="font-heading font-bold text-xl mb-10 text-brand-gold relative inline-block">
                        Quick Links
                        <span class="absolute -bottom-2 left-0 w-8 h-1 bg-brand-gold rounded-full"></span>
                    </h4>
                    <ul class="space-y-5">
                        <li><a href="<?= APP_URL ?>/about" class="text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 flex items-center gap-2 group">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-gold/40 group-hover:bg-brand-gold transition-colors"></span>
                            About Our Church
                        </a></li>
                        <li><a href="<?= APP_URL ?>/sermons" class="text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 flex items-center gap-2 group">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-gold/40 group-hover:bg-brand-gold transition-colors"></span>
                            Watch Sermons
                        </a></li>
                        <li><a href="<?= APP_URL ?>/events" class="text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 flex items-center gap-2 group">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-gold/40 group-hover:bg-brand-gold transition-colors"></span>
                            Upcoming Events
                        </a></li>
                        <li><a href="<?= APP_URL ?>/give" class="text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 flex items-center gap-2 group">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-gold/40 group-hover:bg-brand-gold transition-colors"></span>
                            Online Giving
                        </a></li>
                        <li><a href="<?= APP_URL ?>/services" class="text-gray-300 hover:text-white hover:translate-x-2 transition-all duration-300 flex items-center gap-2 group">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-gold/40 group-hover:bg-brand-gold transition-colors"></span>
                            Our Services
                        </a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="font-heading font-bold text-xl mb-10 text-brand-gold relative inline-block">
                        Get In Touch
                        <span class="absolute -bottom-2 left-0 w-8 h-1 bg-brand-gold rounded-full"></span>
                    </h4>
                    <ul class="space-y-8">
                        <li class="flex items-start gap-5 group">
                            <div class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center shrink-0 group-hover:border-brand-gold/30 transition-colors">
                                <svg class="w-6 h-6 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-brand-gold uppercase tracking-widest mb-1">Our Location</span>
                                <span class="text-gray-200 text-lg"><?= htmlspecialchars($f_address) ?></span>
                            </div>
                        </li>
                        <li class="flex items-start gap-5 group">
                            <div class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center shrink-0 group-hover:border-brand-gold/30 transition-colors">
                                <svg class="w-6 h-6 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-brand-gold uppercase tracking-widest mb-1">Call Us</span>
                                <span class="text-gray-200 text-lg"><?= htmlspecialchars($f_phone) ?></span>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Newsletter/Service Info -->
                <div>
                    <h4 class="font-heading font-bold text-xl mb-10 text-brand-gold relative inline-block">
                        Join Our Newsletter
                        <span class="absolute -bottom-2 left-0 w-8 h-1 bg-brand-gold rounded-full"></span>
                    </h4>
                    <p class="text-gray-300 mb-6 font-light">Stay updated with our latest news and events.</p>
                    <form id="newsletter-form" class="space-y-4">
                        <div class="relative">
                            <input type="email" name="email" placeholder="Email Address" required
                                   class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-xl text-white placeholder:text-gray-500 focus:outline-none focus:border-brand-gold/50 transition-all">
                            <button type="submit" class="absolute right-2 top-2 bottom-2 px-4 bg-brand-gold text-brand-green rounded-lg font-bold hover:bg-white transition-all">
                                JOIN
                            </button>
                        </div>
                        <p id="newsletter-message" class="text-xs italic"></p>
                    </form>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="pt-12 border-t border-white/10">
                <div class="flex flex-col lg:flex-row justify-between items-center gap-8 text-center lg:text-left">
                    <div class="space-y-2">
                        <p class="text-gray-400 text-sm">
                            &copy; <?= date('Y') ?> Great House Christian Centre. All rights reserved.
                        </p>
                        <p class="text-gray-500 text-xs">
                            A community of believers dedicated to the kingdom mandate.
                        </p>
                    </div>
                    
                    <div class="flex flex-wrap justify-center gap-8 text-sm">
                        <a href="#" class="text-gray-400 hover:text-brand-gold transition-colors">Privacy Policy</a>
                        <a href="#" class="text-gray-400 hover:text-brand-gold transition-colors">Terms of Service</a>
                        <a href="#" class="text-gray-400 hover:text-brand-gold transition-colors">Cookie Policy</a>
                        <div class="flex items-center gap-2 pl-4 border-l border-white/10">
                            <span class="text-gray-500">Design by</span>
                            <a href="https://pinnacletechhub.com.ng" target="_blank" class="text-brand-gold hover:text-white font-bold transition-all">
                                Pinnacle Tech Hub
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        window.GHCC_APP_URL = '<?= APP_URL ?>';
        window.GHCC_CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('form[method="POST"], form[method="post"]').forEach(function(form) {
                if (!form.querySelector('input[name="_csrf_token"]')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = '_csrf_token';
                    input.value = window.GHCC_CSRF_TOKEN;
                    form.appendChild(input);
                }
            });
        });
    </script>
    <script>
        // Initialize Swiper immediately after the script loads
        document.addEventListener('DOMContentLoaded', function() {
            if (document.querySelector('.hero-swiper')) {
                const swiper = new Swiper('.hero-swiper', {
                    loop: true,
                    effect: 'fade',
                    speed: 1000,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                });
            }
        });
    </script>
    <script src="<?= APP_URL ?>/js/main.js"></script>
</body>
</html>
