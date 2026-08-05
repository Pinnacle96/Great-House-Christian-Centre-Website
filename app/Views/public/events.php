<?php require_once 'app/Views/layouts/header.php'; ?>

<!-- Modern Hero Section -->
<section class="relative min-h-[60vh] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 w-full h-full">
        <div class="absolute inset-0 bg-gradient-to-br from-brand-green/80 via-brand-green/40 to-black/60 z-10"></div>
        <div class="absolute inset-0 bg-[url('<?= APP_URL ?>/public/assets/images/hero-bg.jpg')] bg-cover bg-center animate-slow-zoom"></div>
        <div class="absolute inset-0 bg-brand-green mix-blend-multiply opacity-20"></div>
    </div>
    
    <div class="relative z-20 text-center text-white container mx-auto px-6">
        <span class="inline-block px-5 py-2 bg-white/10 backdrop-blur-md rounded-full text-[10px] font-bold mb-8 border border-white/20 tracking-[0.3em] uppercase animate-fade-in-down">
            <?= $content['hero']['badge']['value'] ?? 'UPCOMING EVENTS' ?>
        </span>
        <h1 class="font-heading font-bold text-6xl md:text-8xl mb-8 leading-tight tracking-tight animate-fade-in-up">
            <?= isset($content['hero']['title']['value'])
                ? preg_replace('/\*(.*?)\*/', '<span class="text-brand-gold relative inline-block">$1</span>', htmlspecialchars($content['hero']['title']['value']))
                : 'Church Events' ?>
        </h1>
        <div class="prose prose-xl text-white/80 max-w-2xl mx-auto font-light leading-relaxed animate-fade-in-up delay-200">
            <?= nl2br(htmlspecialchars($content['hero']['subtitle']['value'] ?? 'Join us for life-changing experiences, community gatherings, and special services throughout the year.')) ?>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-12 left-1/2 -translate-x-1/2 z-20 hidden md:block">
        <div class="flex flex-col items-center gap-4">
            <span class="text-[10px] font-bold tracking-[0.3em] text-white/40 uppercase rotate-90 mb-8">SCROLL</span>
            <div class="w-[1px] h-24 bg-gradient-to-b from-brand-gold/60 to-transparent"></div>
        </div>
    </div>
</section>

<!-- Events Content -->
<section class="py-32 bg-white relative overflow-hidden">
    <!-- Background accents -->
    <div class="absolute top-0 right-0 w-1/3 h-1/3 bg-brand-green/5 rounded-full blur-[100px] -translate-y-1/2 translate-x-1/2"></div>

    <div class="container mx-auto px-6 md:px-12 lg:px-16 relative z-10">
        <div class="text-center mb-24">
            <span class="inline-block px-4 py-1.5 bg-brand-green/10 text-brand-green rounded-full text-[11px] font-bold mb-6 tracking-[0.2em] uppercase">
                <?= $content['events_intro']['badge']['value'] ?? "WHAT'S HAPPENING" ?>
            </span>
            <h2 class="font-heading font-bold text-5xl md:text-6xl text-gray-900 mb-8 leading-tight"><?= $content['events_intro']['title']['value'] ?? 'Upcoming Events' ?></h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto font-light leading-relaxed">
                <?= $content['events_intro']['subtitle']['value'] ?? 'Discover our upcoming programs, conferences, and special services.' ?>
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <?php if (empty($events)): ?>
                <div class="col-span-1 lg:col-span-2 text-center py-32 bg-white rounded-[3rem] border border-gray-100 shadow-[0_30px_60px_-15px_rgba(0,0,0,0.05)]">
                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-10 text-4xl">📅</div>
                    <h3 class="font-heading font-bold text-3xl text-gray-900 mb-4"><?= $content['no_events']['title']['value'] ?? 'More Events Coming Soon' ?></h3>
                    <p class="text-gray-500 max-w-md mx-auto text-lg font-light leading-relaxed">
                        <?= $content['no_events']['subtitle']['value'] ?? "We're constantly planning new events and programs. Check back regularly or subscribe to stay updated." ?>
                    </p>
                </div>
            <?php else: ?>
                <?php foreach ($events as $event): 
                    $date = new DateTime($event['start_datetime']);
                    $day = $date->format('d');
                    $month = strtoupper($date->format('M'));
                ?>
                    <div class="group bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-[0_10px_40px_-15px_rgba(0,0,0,0.05)] hover:shadow-[0_30px_60px_-15px_rgba(0,0,0,0.1)] transition-all duration-500 flex flex-col md:flex-row">
                        <div class="relative w-full md:w-48 h-48 md:h-auto bg-brand-green overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-brand-green to-brand-green-dark group-hover:scale-110 transition-transform duration-700"></div>
                            <div class="relative h-full flex flex-col items-center justify-center text-white p-6">
                                <span class="text-5xl font-black tracking-tighter"><?= $day ?></span>
                                <span class="text-lg font-bold uppercase tracking-[0.2em] text-brand-gold"><?= $month ?></span>
                            </div>
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 bg-white/10 backdrop-blur-md text-white text-[10px] font-bold rounded-full border border-white/20 uppercase tracking-widest">
                                    <?= htmlspecialchars($event['category'] ?? 'EVENT') ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-8 md:p-10 flex-1">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4 group-hover:text-brand-green transition-colors"><?= htmlspecialchars($event['title']) ?></h3>
                            <div class="space-y-3 mb-8">
                                <div class="flex items-center text-gray-500 text-sm">
                                    <span class="w-5 h-5 mr-3 flex items-center justify-center text-brand-green">📅</span>
                                    <span><?= $date->format('l, F j, Y') ?></span>
                                </div>
                                <div class="flex items-center text-gray-500 text-sm">
                                    <span class="w-5 h-5 mr-3 flex items-center justify-center text-brand-gold">⏰</span>
                                    <span><?= $date->format('g:i A') ?><?= $event['end_datetime'] ? ' - ' . (new DateTime($event['end_datetime']))->format('g:i A') : '' ?></span>
                                </div>
                            </div>
                            <p class="text-gray-600 mb-8 text-sm leading-relaxed">
                                <?= htmlspecialchars($event['description']) ?>
                            </p>
                            
                            <div class="flex flex-wrap gap-4">
                                <a href="<?= APP_URL ?>/events/<?= $event['slug'] ?>" class="px-8 py-3 bg-brand-green text-white text-xs font-bold rounded-xl hover:bg-brand-green-dark transition-all duration-300 transform hover:scale-105 tracking-widest uppercase shadow-lg shadow-brand-green/20">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- No Events Message -->
        <div class="text-center mt-32">
            <div class="max-w-4xl mx-auto bg-gray-50 rounded-[3rem] p-16 md:p-24 border border-gray-100 relative overflow-hidden group">
                <!-- Background decorative element -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-brand-green/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform duration-1000"></div>
                
                <div class="relative z-10">
                    <div class="w-20 h-20 bg-white rounded-3xl shadow-xl shadow-gray-200/50 flex items-center justify-center mx-auto mb-10 group-hover:rotate-12 transition-transform duration-500">
                        <span class="text-4xl">📅</span>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-6">More Events Coming Soon</h3>
                    <p class="text-xl text-gray-600 mb-12 max-w-2xl mx-auto font-light leading-relaxed">
                        We're constantly planning new events and programs. Check back regularly or subscribe to stay updated.
                    </p>
                    <a href="<?= APP_URL ?>/contact" 
                       class="inline-flex items-center px-10 py-5 bg-brand-green text-white text-xs font-bold rounded-2xl hover:bg-brand-green-dark transition-all duration-500 tracking-[0.2em] uppercase shadow-xl shadow-brand-green/20 group/btn">
                        SUBSCRIBE TO UPDATES
                        <span class="ml-3 group-hover/btn:translate-x-2 transition-transform">→</span>
                    </a>
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
                <?= $content['cta']['badge']['value'] ?? 'EVENT INQUIRIES' ?>
            </span>
            <h2 class="font-heading font-bold text-5xl md:text-7xl lg:text-8xl text-white mb-10 leading-[1.1] tracking-tight">
                <?= isset($content['cta']['title']['value'])
                    ? preg_replace('/\*(.*?)\*/', '<span class="text-brand-gold relative inline-block">$1</span>', htmlspecialchars($content['cta']['title']['value']))
                    : 'Want to Host an Event?' ?>
            </h2>
            <div class="prose prose-lg prose-invert text-white/80 mb-16 font-light leading-relaxed max-w-2xl mx-auto">
                <?= nl2br(htmlspecialchars($content['cta']['subtitle']['value'] ?? 'Contact our administration office for inquiries regarding facility use and event partnerships.')) ?>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-center gap-8">
                <a href="<?= APP_URL ?>/contact" 
                   class="group relative px-12 py-6 bg-white text-brand-green font-bold rounded-2xl transition-all duration-500 hover:bg-brand-gold hover:text-brand-green overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.3)] tracking-widest uppercase text-xs">
                    <span class="relative z-10">SUGGEST EVENT</span>
                    <div class="absolute inset-0 bg-brand-gold translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                </a>
                <a href="<?= APP_URL ?>/services" 
                   class="px-12 py-6 bg-transparent border-2 border-white/30 text-white font-bold rounded-2xl hover:bg-white hover:text-brand-green transition-all duration-500 transform hover:scale-105 backdrop-blur-sm tracking-widest uppercase text-xs">
                    VIEW SERVICES
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'app/Views/layouts/footer.php'; ?>