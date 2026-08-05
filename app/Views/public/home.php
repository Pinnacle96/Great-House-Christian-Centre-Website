<?php
require_once 'app/Views/layouts/header.php';

if (!function_exists('ghcc_home_media_url')) {
    function ghcc_home_media_url($path) {
        if (!$path) {
            return '';
        }

        return preg_match('/^https?:\/\//i', $path)
            ? $path
            : APP_URL . '/' . ltrim($path, '/');
    }
}

if (!function_exists('ghcc_home_youtube_embed_url')) {
    function ghcc_home_youtube_embed_url($url) {
        if (!$url) {
            return '';
        }

        $parts = parse_url($url);
        $host = strtolower($parts['host'] ?? '');
        $path = trim($parts['path'] ?? '', '/');
        $videoId = '';

        if (strpos($host, 'youtu.be') !== false) {
            $videoId = explode('/', $path)[0] ?? '';
        } elseif (strpos($host, 'youtube.com') !== false) {
            if (strpos($path, 'embed/') === 0) {
                $videoId = explode('/', substr($path, 6))[0] ?? '';
            } elseif (strpos($path, 'shorts/') === 0) {
                $videoId = explode('/', substr($path, 7))[0] ?? '';
            } else {
                parse_str($parts['query'] ?? '', $query);
                $videoId = $query['v'] ?? '';
            }
        }

        $videoId = preg_replace('/[^a-zA-Z0-9_-]/', '', $videoId);
        return $videoId ? 'https://www.youtube.com/embed/' . $videoId : '';
    }
}
?>

<!-- Modern Hero Slider Section -->
<section class="viewport-safe relative min-h-[760px] lg:min-h-[820px] overflow-hidden bg-brand-green">
    <!-- Swiper Slider -->
    <div class="swiper hero-swiper absolute inset-0 h-full w-full">
        <div class="swiper-wrapper">
            <?php for($i=1; $i<=3; $i++): 
                $title = $content['hero']["slide{$i}_title"]['value'] ?? '';
                $subtitle = $content['hero']["slide{$i}_subtitle"]['value'] ?? '';
                $image = $content['hero']["slide{$i}_image"]['value'] ?? "assets/img/bg.jpg";
            ?>
            <div class="swiper-slide relative">
                <div class="absolute inset-0 bg-brand-green">
                    <img src="<?= APP_URL ?>/<?= $image ?>" alt="Slide <?= $i ?>" class="absolute inset-0 w-full h-full object-cover opacity-55">
                    <div class="absolute inset-0 bg-gradient-to-r from-brand-green-dark/95 via-brand-green/70 to-brand-green/20"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-green-dark/80 via-transparent to-black/20"></div>
                </div>
                <div class="relative z-20 flex min-h-[760px] lg:min-h-[820px] w-full max-w-full items-center text-white">
                    <div class="w-full max-w-7xl mx-auto px-6 md:px-12 lg:px-16 pb-64 pt-32 md:pb-48 md:pt-28">
                        <div class="w-full max-w-[42rem] lg:max-w-4xl text-left">
                    <h1 class="font-heading font-extrabold text-4xl sm:text-5xl md:text-7xl lg:text-8xl mb-6 md:mb-8 leading-[0.98] tracking-tight animate-fade-up">
                        <?= $title ?>
                    </h1>
                    <p class="text-base md:text-xl lg:text-2xl mb-8 md:mb-10 max-w-[34rem] leading-relaxed text-white/90 font-light animate-fade-up delay-200 break-words">
                        <?= $subtitle ?>
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 animate-fade-up delay-500">
                        <a href="<?= APP_URL ?>/contact" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-4 md:px-9 md:py-4 bg-white text-brand-green font-bold rounded-lg hover:bg-brand-gold hover:text-brand-green transition-all duration-300 shadow-xl text-sm md:text-base">
                            JOIN US SUNDAY
                        </a>
                        <a href="<?= APP_URL ?>/sermons" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-4 md:px-9 md:py-4 bg-transparent border border-white/80 text-white font-bold rounded-lg hover:bg-white hover:text-brand-green transition-all duration-300 text-sm md:text-base">
                            WATCH LIVE
                        </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>

        <!-- Navigation Buttons -->
        <div class="swiper-button-next !text-white/50 hover:!text-brand-gold transition-colors !hidden md:!flex"></div>
        <div class="swiper-button-prev !text-white/50 hover:!text-brand-gold transition-colors !hidden md:!flex"></div>
        
        <!-- Pagination -->
        <div class="swiper-pagination !bottom-72 md:!bottom-40"></div>
    </div>

    <!-- Floating Quick Info Cards (Stays over slider) -->
    <div class="absolute bottom-6 md:bottom-10 left-0 right-0 z-30 pointer-events-none">
        <div class="w-full max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4 w-full max-w-5xl mx-auto pointer-events-auto">
                <div class="bg-white/95 backdrop-blur-md rounded-lg p-4 md:p-5 border border-white/50 shadow-xl transition-all duration-300 group flex md:block items-center justify-between">
                    <div>
                        <div class="text-lg md:text-2xl font-bold text-brand-green mb-0.5 transition-transform duration-300 inline-block">9:00 AM</div>
                        <div class="text-gray-600 font-bold text-[10px] md:text-sm uppercase tracking-wider">Sunday Service</div>
                    </div>
                    <i class="fas fa-clock text-brand-green/40 md:hidden text-sm"></i>
                </div>
                <div class="bg-white/95 backdrop-blur-md rounded-lg p-4 md:p-5 border border-white/50 shadow-xl transition-all duration-300 group flex md:block items-center justify-between">
                    <div>
                        <div class="text-lg md:text-2xl font-bold text-brand-green mb-0.5 transition-transform duration-300 inline-block">5:00 PM</div>
                        <div class="text-gray-600 font-bold text-[10px] md:text-sm uppercase tracking-wider">Tuesday WWGS</div>
                    </div>
                    <i class="fas fa-book text-brand-green/40 md:hidden text-sm"></i>
                </div>
                <div class="bg-white/95 backdrop-blur-md rounded-lg p-4 md:p-5 border border-white/50 shadow-xl transition-all duration-300 group flex md:block items-center justify-between">
                    <div>
                        <div class="text-lg md:text-2xl font-bold text-brand-green mb-0.5 transition-transform duration-300 inline-block">5:30 PM</div>
                        <div class="text-gray-600 font-bold text-[10px] md:text-sm uppercase tracking-wider">Friday Travail</div>
                    </div>
                    <i class="fas fa-hands-praying text-brand-green/40 md:hidden text-sm"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll Indicator -->
    <div class="hidden">
        <div class="w-1 h-12 rounded-full bg-gradient-to-b from-brand-gold to-transparent"></div>
    </div>
</section>

<style>
    @keyframes fade-up {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-fade-up {
        animation: fade-up 0.8s ease-out forwards;
    }
    .delay-200 { animation-delay: 0.2s; }
    .delay-500 { animation-delay: 0.5s; }
</style>

<!-- About Preview Section -->
<section class="py-20 md:py-32 bg-white relative overflow-hidden" id="about-preview">
    <!-- Subtle decorative background element -->
    <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[600px] h-[600px] bg-brand-green/5 rounded-full blur-3xl -z-10"></div>
    <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/4 w-[400px] h-[400px] bg-brand-gold/5 rounded-full blur-3xl -z-10"></div>

    <div class="container mx-auto px-6 md:px-12 lg:px-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-24 items-center">
            <div class="order-2 lg:order-1">
                <span class="inline-block px-4 py-1.5 bg-brand-green/10 text-brand-green rounded-full text-xs font-bold mb-6 tracking-[0.2em] uppercase">
                    <?= $content['about_preview']['badge']['value'] ?? 'OUR STORY' ?>
                </span>
                <h2 class="font-heading font-bold text-3xl md:text-5xl lg:text-6xl text-gray-900 mb-8 leading-[1.1] tracking-tight">
                    <?= isset($content['about_preview']['title']['value'])
                        ? preg_replace('/\*(.*?)\*/', '<span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-green to-brand-green-dark">$1</span>', htmlspecialchars($content['about_preview']['title']['value']))
                        : 'A House of<br><span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-green to-brand-green-dark">Power & Purpose</span>' ?>
                </h2>
                <div class="prose prose-lg text-gray-600 mb-10 leading-[1.8] font-light">
                    <?= nl2br(htmlspecialchars($content['about_preview']['content']['value'] ?? 'Great House Christian Centre is committed to raising believers who walk in power, live with purpose, and carry the passion of God\'s Kingdom. We are a family of faith dedicated to transforming lives through the Gospel of Jesus Christ.')) ?>
                </div>
                <div class="flex flex-wrap gap-4 md:gap-6 items-center">
                    <a href="<?= APP_URL ?>/about" 
                       class="px-8 py-4 md:px-10 md:py-5 bg-brand-green text-white rounded-2xl hover:bg-brand-green-dark transition-all duration-300 font-bold shadow-xl shadow-brand-green/20 hover:-translate-y-1">
                        LEARN MORE
                    </a>
                    <a href="<?= APP_URL ?>/contact" 
                       class="px-8 py-4 md:px-10 md:py-5 border-2 border-brand-green text-brand-green rounded-2xl hover:bg-brand-green hover:text-white transition-all duration-300 font-bold hover:-translate-y-1">
                        VISIT US
                    </a>
                </div>
            </div>
            <div class="order-1 lg:order-2 relative group">
                <!-- Decorative element -->
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-brand-gold/10 rounded-full blur-3xl group-hover:bg-brand-gold/20 transition-all duration-700"></div>
                <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-brand-green/10 rounded-full blur-3xl group-hover:bg-brand-green/20 transition-all duration-700"></div>
                
                <!-- Main Image Container -->
                <div class="relative bg-white p-4 rounded-[2rem] md:rounded-[2.5rem] shadow-2xl transform group-hover:scale-[1.02] transition-all duration-700">
                    <div class="relative bg-gradient-to-br from-brand-green to-brand-green-dark rounded-[1.5rem] md:rounded-[2rem] overflow-hidden aspect-[4/3] flex items-center justify-center">
                        <div class="text-center text-white p-8">
                            <div class="w-20 h-20 md:w-24 md:h-24 bg-white/10 backdrop-blur-md rounded-2xl md:rounded-3xl flex items-center justify-center mx-auto mb-6 md:mb-8 transform group-hover:rotate-12 transition-all duration-700">
                                <svg class="w-10 h-10 md:w-12 md:h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                            <p class="text-base md:text-lg font-medium tracking-widest uppercase opacity-80">Our Community</p>
                        </div>
                    </div>
                </div>
                
                <!-- Floating Card -->
                <div class="absolute -bottom-6 -right-4 md:-bottom-10 md:-right-6 lg:-right-12 bg-white p-4 md:p-6 rounded-2xl shadow-2xl max-w-[160px] md:max-w-[200px] transform group-hover:-translate-y-4 transition-all duration-700 delay-100">
                    <div class="flex items-center gap-3 md:gap-4 mb-2 md:mb-3">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-brand-gold/10 rounded-lg flex items-center justify-center text-brand-gold">
                            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <span class="font-bold text-gray-900 text-base md:text-xl"><?= $content['about_preview']['stat_number']['value'] ?? '1000+' ?></span>
                    </div>
                    <p class="text-xs md:text-sm text-gray-500 font-medium"><?= $content['about_preview']['stat_label']['value'] ?? 'Lives Transformed' ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Bento Grid -->
<section class="py-20 md:py-32 bg-gray-50 relative overflow-hidden" id="services">
    <!-- Background accents -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full pointer-events-none">
        <div class="absolute top-0 left-0 w-64 h-64 bg-brand-green/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-brand-gold/5 rounded-full blur-3xl"></div>
    </div>

    <div class="container mx-auto px-6 md:px-12 lg:px-16 relative z-10">
        <div class="text-center mb-16 md:mb-24 max-w-3xl mx-auto">
            <span class="inline-block px-4 py-1.5 bg-brand-green/10 text-brand-green rounded-full text-xs font-bold mb-6 tracking-[0.2em] uppercase">
                <?= $content['services_intro']['badge']['value'] ?? 'WEEKLY SERVICES' ?>
            </span>
            <h2 class="font-heading font-bold text-3xl md:text-5xl lg:text-6xl text-gray-900 mb-8 leading-tight tracking-tight">
                <?= isset($content['services_intro']['title']['value'])
                    ? preg_replace('/\*(.*?)\*/', '<span class="text-brand-green">$1</span>', htmlspecialchars($content['services_intro']['title']['value']))
                    : 'Join Us In <span class="text-brand-green">Worship</span>' ?>
            </h2>
            <div class="prose prose-xl text-gray-600 font-light leading-relaxed">
                <?= nl2br(htmlspecialchars($content['services_intro']['subtitle']['value'] ?? 'Experience the presence of God through our dynamic services designed to equip, empower, and encourage you in your faith journey.')) ?>
            </div>
        </div>
        
        <!-- Bento Grid Layout -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 lg:gap-10 max-w-7xl mx-auto">
            <!-- Sunday Service -->
            <div class="bg-white rounded-[2rem] md:rounded-[2.5rem] p-8 md:p-12 border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-500 group relative overflow-hidden flex flex-col h-full">
                <div class="absolute top-0 right-0 w-32 h-32 md:w-40 md:h-40 bg-brand-green/5 rounded-bl-full -mr-8 -mt-8 md:-mr-10 md:-mt-10 group-hover:bg-brand-green/10 transition-all duration-500"></div>
                <div class="w-16 h-16 md:w-20 md:h-20 bg-brand-green/10 rounded-2xl md:rounded-3xl flex items-center justify-center mb-8 md:mb-10 group-hover:bg-brand-green group-hover:rotate-6 transition-all duration-500">
                    <span class="text-3xl md:text-4xl group-hover:scale-110 transition-transform">🙌</span>
                </div>
                <h3 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4 md:mb-6 group-hover:text-brand-green transition-colors">Sunday Fulfilment</h3>
                <p class="text-gray-600 mb-8 md:mb-12 leading-[1.8] font-light text-base md:text-lg flex-grow">Join us for powerful worship and life-changing messages every Sunday morning.</p>
                <div class="flex items-center justify-between mt-auto pt-6 md:pt-8 border-t border-gray-50">
                    <div>
                        <p class="text-[10px] md:text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Time</p>
                        <span class="text-2xl md:text-3xl font-bold text-brand-green">9:00 AM</span>
                    </div>
                    <span class="w-12 h-12 md:w-14 md:h-14 bg-brand-green/10 rounded-xl md:rounded-2xl flex items-center justify-center group-hover:bg-brand-green group-hover:text-white transition-all duration-500 transform group-hover:translate-x-2">
                        <svg class="w-6 h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </span>
                </div>
            </div>
            
            <!-- Tuesday Bible Study -->
            <div class="bg-white rounded-[2rem] md:rounded-[2.5rem] p-8 md:p-12 border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-500 group relative overflow-hidden flex flex-col h-full">
                <div class="absolute top-0 right-0 w-32 h-32 md:w-40 md:h-40 bg-brand-green/5 rounded-bl-full -mr-8 -mt-8 md:-mr-10 md:-mt-10 group-hover:bg-brand-green/10 transition-all duration-500"></div>
                <div class="w-16 h-16 md:w-20 md:h-20 bg-brand-green/10 rounded-2xl md:rounded-3xl flex items-center justify-center mb-8 md:mb-10 group-hover:bg-brand-green group-hover:rotate-6 transition-all duration-500">
                    <span class="text-3xl md:text-4xl group-hover:scale-110 transition-transform">📖</span>
                </div>
                <h3 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4 md:mb-6 group-hover:text-brand-green transition-colors">Tuesday WWGS</h3>
                <p class="text-gray-600 mb-8 md:mb-12 leading-[1.8] font-light text-base md:text-lg flex-grow">Deep dive into God's Word with our Walking With God Service.</p>
                <div class="flex items-center justify-between mt-auto pt-6 md:pt-8 border-t border-gray-50">
                    <div>
                        <p class="text-[10px] md:text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Time</p>
                        <span class="text-2xl md:text-3xl font-bold text-brand-green">5:00 PM</span>
                    </div>
                    <span class="w-12 h-12 md:w-14 md:h-14 bg-brand-green/10 rounded-xl md:rounded-2xl flex items-center justify-center group-hover:bg-brand-green group-hover:text-white transition-all duration-500 transform group-hover:translate-x-2">
                        <svg class="w-6 h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </span>
                </div>
            </div>
            
            <!-- Friday Prayer -->
            <div class="bg-white rounded-[2rem] md:rounded-[2.5rem] p-8 md:p-12 border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-500 group relative overflow-hidden flex flex-col h-full md:col-span-2 lg:col-span-1">
                <div class="absolute top-0 right-0 w-32 h-32 md:w-40 md:h-40 bg-brand-green/5 rounded-bl-full -mr-8 -mt-8 md:-mr-10 md:-mt-10 group-hover:bg-brand-green/10 transition-all duration-500"></div>
                <div class="w-16 h-16 md:w-20 md:h-20 bg-brand-green/10 rounded-2xl md:rounded-3xl flex items-center justify-center mb-8 md:mb-10 group-hover:bg-brand-green group-hover:rotate-6 transition-all duration-500">
                    <span class="text-3xl md:text-4xl group-hover:scale-110 transition-transform">🙏</span>
                </div>
                <h3 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4 md:mb-6 group-hover:text-brand-green transition-colors">Friday Travail</h3>
                <p class="text-gray-600 mb-8 md:mb-12 leading-[1.8] font-light text-base md:text-lg flex-grow">Experience the power of corporate prayer and intercession in our Travail Service.</p>
                <div class="flex items-center justify-between mt-auto pt-6 md:pt-8 border-t border-gray-50">
                    <div>
                        <p class="text-[10px] md:text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Time</p>
                        <span class="text-2xl md:text-3xl font-bold text-brand-green">5:30 PM</span>
                    </div>
                    <span class="w-12 h-12 md:w-14 md:h-14 bg-brand-green/10 rounded-xl md:rounded-2xl flex items-center justify-center group-hover:bg-brand-green group-hover:text-white transition-all duration-500 transform group-hover:translate-x-2">
                        <svg class="w-6 h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Latest Sermon Section -->
<section class="py-20 md:py-32 bg-white relative overflow-hidden" id="latest-sermon">
    <div class="container mx-auto px-6 md:px-12 lg:px-16">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 md:mb-20 gap-8">
            <div class="max-w-2xl">
                <span class="inline-block px-4 py-1.5 bg-brand-green/10 text-brand-green rounded-full text-xs font-bold mb-6 tracking-[0.2em] uppercase">
                    <?= $content['latest_sermon']['badge']['value'] ?? 'LATEST MESSAGE' ?>
                </span>
                <h2 class="font-heading font-bold text-3xl md:text-5xl lg:text-6xl text-gray-900 leading-tight tracking-tight">
                    <?= isset($content['latest_sermon']['title']['value'])
                        ? preg_replace('/\*(.*?)\*/', '<span class="text-brand-green">$1</span>', htmlspecialchars($content['latest_sermon']['title']['value']))
                        : 'Word for the <span class="text-brand-green">Season</span>' ?>
                </h2>
            </div>
            <a href="<?= APP_URL ?>/sermons" 
               class="inline-flex items-center gap-4 text-brand-green font-bold hover:text-brand-green-dark transition-all group">
                EXPLORE ALL SERMONS
                <span class="w-10 h-10 md:w-12 md:h-12 bg-brand-green/10 rounded-full flex items-center justify-center group-hover:bg-brand-green group-hover:text-white transition-all duration-300 transform group-hover:translate-x-2">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </span>
            </a>
        </div>
        
        <!-- Featured Sermon Card -->
        <?php
            $sermon = $latestSermon ?? null;
            $sermonTitle = $sermon['title'] ?? 'Voice Of God';
            $sermonPreacher = $sermon['preacher'] ?? 'Pastor Segun Oduyebo';
            $sermonDescription = $sermon['description'] ?? 'Listen to the latest message from Great House Christian Center.';
            $sermonDate = !empty($sermon['date_preached']) ? date('M j, Y', strtotime($sermon['date_preached'])) : 'Latest Message';
            $sermonImage = !empty($sermon['thumbnail']) ? $sermon['thumbnail'] : 'assets/img/sermon-placeholder.jpg';
            $sermonAudioUrl = ghcc_home_media_url($sermon['audio_url'] ?? '');
            $sermonVideoEmbed = ghcc_home_youtube_embed_url($sermon['video_url'] ?? '');
            $sermonUrl = !empty($sermon['video_url']) ? $sermon['video_url'] : ($sermonAudioUrl ?: APP_URL . '/sermons');
            $sermonPrimaryAction = !empty($sermon['video_url']) ? 'WATCH NOW' : ($sermonAudioUrl ? 'LISTEN NOW' : 'VIEW MESSAGE');
            $sermonLinkIsExternal = preg_match('/^https?:\/\//', $sermonUrl);
        ?>
        <div class="bg-gray-50 rounded-[2rem] md:rounded-[3rem] overflow-hidden group border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-700">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                <!-- Video Thumbnail -->
                <div class="relative overflow-hidden aspect-video lg:aspect-auto min-h-[300px] md:min-h-[400px]">
                    <?php if ($sermonVideoEmbed): ?>
                        <iframe src="<?= htmlspecialchars($sermonVideoEmbed) ?>" title="<?= htmlspecialchars($sermonTitle) ?>" class="absolute inset-0 w-full h-full" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    <?php else: ?>
                        <div class="absolute inset-0 bg-gradient-to-br from-brand-green/40 to-brand-green-dark/60 z-10"></div>
                        <img src="<?= APP_URL ?>/<?= htmlspecialchars($sermonImage) ?>" alt="<?= htmlspecialchars($sermonTitle) ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-1000">
                        <div class="absolute inset-0 z-20 flex items-center justify-center px-6 text-white">
                            <?php if ($sermonAudioUrl): ?>
                                <div class="w-full max-w-lg text-center">
                                    <div class="w-20 h-20 md:w-28 md:h-28 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center mx-auto mb-6 border border-white/30 shadow-2xl">
                                        <i class="fas fa-headphones text-3xl"></i>
                                    </div>
                                    <audio controls preload="metadata" class="w-full">
                                        <source src="<?= htmlspecialchars($sermonAudioUrl) ?>">
                                    </audio>
                                </div>
                            <?php else: ?>
                                <div class="w-20 h-20 md:w-28 md:h-28 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center border border-white/30 shadow-2xl">
                                    <i class="fas fa-microphone-alt text-3xl"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Sermon Details -->
                <div class="p-8 md:p-12 lg:p-20 flex flex-col justify-center">
                    <div class="flex items-center gap-4 mb-6 md:mb-8">
                        <span class="px-3 py-1 bg-brand-gold/10 text-brand-gold rounded-full text-[10px] md:text-xs font-bold uppercase tracking-widest">Featured</span>
                        <span class="text-gray-400 font-bold text-xs md:text-sm tracking-widest uppercase"><?= htmlspecialchars($sermonDate) ?></span>
                    </div>
                    <h3 class="text-3xl md:text-5xl font-bold text-gray-900 mb-4 leading-tight tracking-tight group-hover:text-brand-green transition-colors"><?= htmlspecialchars($sermonTitle) ?></h3>
                    <p class="text-brand-green font-bold text-xs uppercase tracking-widest mb-6"><?= htmlspecialchars($sermonPreacher) ?></p>
                    <p class="text-lg md:text-xl text-gray-600 mb-8 md:mb-12 leading-[1.8] font-light">
                        <?= htmlspecialchars($sermonDescription) ?>
                    </p>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 md:gap-8">
                        <a href="<?= htmlspecialchars($sermonUrl) ?>" <?= $sermonLinkIsExternal ? 'target="_blank" rel="noopener"' : '' ?>
                           class="w-full sm:w-auto px-10 py-5 md:px-12 md:py-6 bg-brand-green text-white rounded-2xl hover:bg-brand-green-dark transition-all duration-300 font-bold shadow-2xl shadow-brand-green/20 hover:-translate-y-1 text-center">
                            <?= $sermonPrimaryAction ?>
                        </a>
                        <a href="<?= APP_URL ?>/sermons" class="flex items-center gap-4 text-gray-500 hover:text-brand-green font-bold transition-all group/btn">
                            <span class="w-12 h-12 md:w-14 md:h-14 rounded-xl md:rounded-2xl bg-gray-100 flex items-center justify-center group-hover/btn:bg-brand-green/10 transition-all transform group-hover/btn:rotate-12">
                                <svg class="w-6 h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </span>
                            ALL MESSAGES
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Upcoming Events -->
<section class="py-20 md:py-32 bg-gray-50 relative overflow-hidden" id="events">
    <div class="container mx-auto px-6 md:px-12 lg:px-16">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 md:mb-20 gap-8">
            <div class="max-w-2xl">
                <span class="inline-block px-4 py-1.5 bg-brand-green/10 text-brand-green rounded-full text-xs font-bold mb-6 tracking-[0.2em] uppercase">
                    WHAT'S HAPPENING
                </span>
                <h2 class="font-heading font-bold text-3xl md:text-5xl lg:text-6xl text-gray-900 leading-tight tracking-tight">
                    Upcoming <span class="text-brand-green">Events</span>
                </h2>
            </div>
            <a href="<?= APP_URL ?>/events" 
               class="inline-flex items-center gap-4 text-brand-green font-bold hover:text-brand-green-dark transition-all group">
                VIEW ALL EVENTS
                <span class="w-10 h-10 md:w-12 md:h-12 bg-brand-green/10 rounded-full flex items-center justify-center group-hover:bg-brand-green group-hover:text-white transition-all duration-300 transform group-hover:translate-x-2">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </span>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 lg:gap-12">
            <?php foreach($events as $event): 
                $date = new DateTime($event['start_datetime']);
                $month = $date->format('M');
                $day = $date->format('d');
                $time = $date->format('g:i A');
            ?>
            <!-- Event Card -->
            <div class="bg-white rounded-[2.5rem] overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 group border border-gray-100 flex flex-col h-full">
                <div class="relative h-72 overflow-hidden">
                    <img src="<?= APP_URL ?>/<?= $event['image'] ?? 'assets/img/event-placeholder.jpg' ?>" alt="<?= htmlspecialchars($event['title']) ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-1000">
                    <div class="absolute top-8 left-8 px-5 py-2.5 bg-white/95 backdrop-blur-sm rounded-2xl text-brand-green font-bold text-sm shadow-xl flex flex-col items-center leading-none">
                        <span class="text-xs uppercase tracking-widest mb-1 opacity-60"><?= $month ?></span>
                        <span class="text-2xl"><?= $day ?></span>
                    </div>
                </div>
                <div class="p-12 flex flex-col flex-grow">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6 group-hover:text-brand-green transition-colors leading-tight">
                        <?= htmlspecialchars($event['title']) ?>
                    </h3>
                    <p class="text-gray-600 mb-10 leading-[1.8] font-light text-lg flex-grow">
                        <?= htmlspecialchars($event['description']) ?>
                    </p>
                    <div class="mt-auto flex items-center justify-between pt-8 border-t border-gray-50">
                        <a href="<?= APP_URL ?>/events/<?= $event['slug'] ?>" class="text-brand-green font-bold hover:text-brand-green-dark transition-colors inline-flex items-center gap-3 group/link">
                            LEARN MORE
                            <svg class="w-6 h-6 group-hover/link:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                        <div class="flex items-center gap-2 text-gray-400 text-sm font-bold uppercase tracking-widest">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <?= $time ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-40 bg-brand-green relative overflow-hidden">
    <!-- Animated background elements -->
    <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-white/5 rounded-full blur-[120px] -translate-x-1/2 -translate-y-1/2 animate-pulse"></div>
    <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-brand-gold/10 rounded-full blur-[120px] translate-x-1/3 translate-y-1/3"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full opacity-[0.03] pointer-events-none" 
         style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
    
    <div class="container mx-auto px-6 md:px-12 lg:px-16 text-center relative z-10">
        <div class="max-w-4xl mx-auto">
            <span class="inline-block px-4 py-1.5 bg-white/10 text-white rounded-full text-xs font-bold mb-8 tracking-[0.2em] uppercase backdrop-blur-sm border border-white/10">
                <?= $content['cta']['badge']['value'] ?? 'JOIN OUR COMMUNITY' ?>
            </span>
            <h2 class="font-heading font-bold text-5xl md:text-7xl lg:text-8xl text-white mb-10 leading-[1.1] tracking-tight">
                <?= isset($content['cta']['title']['value'])
                    ? preg_replace('/\*(.*?)\*/', '<span class="text-brand-gold">$1</span>', htmlspecialchars($content['cta']['title']['value']))
                    : 'Ready to Experience<br><span class="text-brand-gold">God\'s Presence?</span>' ?>
            </h2>
            <div class="prose prose-lg prose-invert text-white/80 mb-16 font-light leading-relaxed max-w-2xl mx-auto">
                <?= nl2br(htmlspecialchars($content['cta']['subtitle']['value'] ?? 'We can\'t wait to welcome you to our church family. Plan your visit today and experience the transformative love and power of God in our services.')) ?>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-center gap-8 mb-24">
                <a href="<?= APP_URL ?>/contact" 
                   class="group relative px-12 py-6 bg-white text-brand-green font-bold rounded-2xl transition-all duration-500 hover:bg-brand-gold hover:text-brand-green overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.2)] hover:shadow-brand-gold/30">
                    <span class="relative z-10">PLAN YOUR VISIT</span>
                    <div class="absolute inset-0 bg-brand-gold translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                </a>
                <a href="<?= APP_URL ?>/give" 
                   class="px-12 py-6 bg-transparent border-2 border-white/30 text-white font-bold rounded-2xl hover:bg-white hover:text-brand-green transition-all duration-500 transform hover:scale-105 backdrop-blur-sm">
                    GIVE ONLINE
                </a>
            </div>
            
            <div class="pt-16 border-t border-white/10 grid grid-cols-1 md:grid-cols-3 gap-12">
                <div class="group">
                    <span class="block text-4xl lg:text-5xl font-bold text-brand-gold mb-3 group-hover:scale-110 transition-transform duration-300 inline-block"><?= $content['cta']['stat1_number']['value'] ?? '20+' ?></span>
                    <span class="block text-sm font-bold text-white/60 uppercase tracking-[0.2em]"><?= $content['cta']['stat1_label']['value'] ?? 'Active Ministries' ?></span>
                </div>
                <div class="group">
                    <span class="block text-4xl lg:text-5xl font-bold text-brand-gold mb-3 group-hover:scale-110 transition-transform duration-300 inline-block"><?= $content['cta']['stat2_number']['value'] ?? '1000+' ?></span>
                    <span class="block text-sm font-bold text-white/60 uppercase tracking-[0.2em]"><?= $content['cta']['stat2_label']['value'] ?? 'Lives Impacted' ?></span>
                </div>
                <div class="group">
                    <span class="block text-4xl lg:text-5xl font-bold text-brand-gold mb-3 group-hover:scale-110 transition-transform duration-300 inline-block"><?= $content['cta']['stat3_number']['value'] ?? '5+' ?></span>
                    <span class="block text-sm font-bold text-white/60 uppercase tracking-[0.2em]"><?= $content['cta']['stat3_label']['value'] ?? 'Global Branches' ?></span>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'app/Views/layouts/footer.php'; ?>
