<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrf_token ?? \App\Core\Security::csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
    <title><?= isset($title) ? $title . ' - ' : '' ?><?= $settings['site_name'] ?? APP_NAME ?></title>
    <link rel="icon" type="image/png" href="<?= APP_URL ?>/<?= $settings['site_favicon'] ?? 'assets/logo/ghcc_logo.png' ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/style.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
</head>
<body class="bg-white text-gray-900 font-body flex flex-col min-h-screen">
    <?php 
    $navItems = [
        ['label' => 'Home', 'url' => APP_URL],
        ['label' => 'About', 'url' => APP_URL . '/about'],
        ['label' => 'Sermons', 'url' => APP_URL . '/sermons'],
        ['label' => 'Events', 'url' => APP_URL . '/events'],
        ['label' => 'Centres', 'url' => APP_URL . '/branches'],
        ['label' => 'Give', 'url' => APP_URL . '/give'],
        ['label' => 'Contact', 'url' => APP_URL . '/contact'],
    ];
    ?>
    <!-- Modern Header with Glassmorphism -->
    <header id="main-header" class="viewport-safe bg-white/80 backdrop-blur-xl border-b border-gray-100/50 sticky top-0 z-[100] transition-all duration-500">
        <div class="w-full max-w-7xl mx-auto px-6 md:px-12 lg:px-16 py-3 flex justify-between items-center">
            <a href="<?= APP_URL ?>" class="flex items-center gap-4 group">
                <div class="relative">
                    <img src="<?= APP_URL ?>/<?= $settings['site_logo'] ?? 'assets/logo/ghcc_logo.png' ?>" alt="Logo" class="h-10 md:h-12 group-hover:scale-110 transition-transform duration-500">
                </div>
                <span class="font-heading font-black text-2xl md:text-3xl tracking-tighter text-brand-green group-hover:text-brand-gold transition-colors duration-500 uppercase">GHCC</span>
            </a>
            
            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex items-center space-x-10">
                <div class="flex items-center space-x-8">
                    <?php foreach ($navItems as $item): ?>
                        <a href="<?= $item['url'] ?>" 
                           class="font-bold text-sm text-gray-600 hover:text-brand-green transition-all duration-300 uppercase tracking-widest relative group/nav">
                            <?= $item['label'] ?>
                            <span class="absolute -bottom-2 left-0 w-0 h-0.5 bg-brand-green transition-all duration-300 group-hover/nav:w-full"></span>
                        </a>
                    <?php endforeach; ?>
                </div>
                
                <div class="h-8 w-[1px] bg-gray-100 mx-2"></div>
                
                <a href="<?= APP_URL ?>/login" 
                   class="group relative px-8 py-3 bg-brand-green text-white text-sm font-black rounded-xl overflow-hidden transition-all duration-300 hover:shadow-[0_10px_30px_rgba(0,104,56,0.3)] uppercase tracking-widest">
                    <span class="relative z-10">Login</span>
                    <div class="absolute inset-0 bg-brand-green-dark translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                </a>
            </nav>

            <!-- Mobile Menu Button -->
            <button id="mobile-menu-btn" class="lg:hidden group p-3 rounded-2xl hover:bg-gray-50 transition-all duration-300 relative z-[60]">
                <div class="w-6 h-5 relative flex flex-col justify-between">
                    <span class="w-full h-0.5 bg-brand-green rounded-full transition-all duration-300 group-[.active]:rotate-45 group-[.active]:translate-y-2"></span>
                    <span class="w-full h-0.5 bg-brand-green rounded-full transition-all duration-300 group-[.active]:opacity-0"></span>
                    <span class="w-full h-0.5 bg-brand-green rounded-full transition-all duration-300 group-[.active]:-rotate-45 group-[.active]:-translate-y-2.5"></span>
                </div>
            </button>
        </div>
    </header>

    <!-- Mobile Navigation Overlay -->
    <div id="mobile-menu" class="fixed left-0 right-0 bottom-0 bg-white/95 backdrop-blur-2xl z-[90] overflow-y-auto hidden">
        <div class="w-full max-w-7xl mx-auto px-6 py-12 flex flex-col space-y-8">
            <?php foreach ($navItems as $item): ?>
                <a href="<?= $item['url'] ?>" 
                   class="text-4xl font-black text-gray-900 hover:text-brand-green transition-all duration-300 tracking-tighter flex items-center justify-between group">
                    <?= $item['label'] ?>
                    <svg class="w-8 h-8 opacity-0 -translate-x-4 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            <?php endforeach; ?>
            
            <div class="pt-8 border-t border-gray-100">
                <a href="<?= APP_URL ?>/login" 
                   class="block w-full py-6 bg-brand-green text-white text-center rounded-2xl font-black text-xl tracking-widest uppercase shadow-xl shadow-brand-green/20">
                    Login to Portal
                </a>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-8">
                <div class="p-6 bg-gray-50 rounded-2xl">
                    <span class="block text-brand-green font-black text-xl mb-1">9:00 AM</span>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Sunday Service</span>
                </div>
                <div class="p-6 bg-gray-50 rounded-2xl">
                    <span class="block text-brand-green font-black text-xl mb-1">6:00 PM</span>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Tue/Fri Service</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const header = document.getElementById('main-header');

        const updateMenuPosition = () => {
            const headerHeight = header.offsetHeight;
            mobileMenu.style.top = `${headerHeight}px`;
        };

        mobileMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            updateMenuPosition();
            mobileMenuBtn.classList.toggle('active');
            mobileMenu.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
        });

        // Close menu on link click
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenuBtn.classList.remove('active');
                mobileMenu.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            });
        });

        // Prevent clicks inside the menu from closing it (if we add a backdrop listener later)
        mobileMenu.addEventListener('click', (e) => {
            e.stopPropagation();
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!mobileMenu.classList.contains('hidden') && !mobileMenuBtn.contains(e.target)) {
                mobileMenuBtn.classList.remove('active');
                mobileMenu.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        });

        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('shadow-xl', 'bg-white/95');
                header.classList.remove('bg-white/80');
            } else {
                header.classList.remove('shadow-xl', 'bg-white/95');
                header.classList.add('bg-white/80');
            }
            if (!mobileMenu.classList.contains('hidden')) {
                updateMenuPosition();
            }
        });
    </script>
    <main class="flex-grow">
