<?php require_once 'app/Views/layouts/header.php'; ?>

<!-- Modern Hero Section -->
<section class="relative min-h-[60vh] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 w-full h-full">
        <div class="absolute inset-0 bg-gradient-to-br from-brand-green/90 via-brand-green/70 to-brand-green-dark/90 z-10"></div>
        <div class="absolute inset-0 bg-[url('<?= APP_URL ?>/public/assets/images/hero-bg.jpg')] bg-cover bg-center animate-slow-zoom"></div>
        <div class="absolute inset-0 bg-brand-green mix-blend-multiply opacity-20"></div>
    </div>
    
    <div class="relative z-20 text-center text-white container mx-auto px-6">
        <span class="inline-block px-5 py-2 bg-white/10 backdrop-blur-md rounded-full text-[10px] font-bold mb-8 border border-white/20 tracking-[0.3em] uppercase animate-fade-in-down">
            <?= $content['hero']['badge']['value'] ?? 'LIFE TOGETHER' ?>
        </span>
        <h1 class="font-heading font-bold text-6xl md:text-8xl mb-8 leading-tight tracking-tight animate-fade-in-up">
            <?= isset($content['hero']['title']['value'])
                ? preg_replace('/\*(.*?)\*/', '<span class="text-brand-gold relative inline-block">$1</span>', htmlspecialchars($content['hero']['title']['value']))
                : 'Join a <span class="text-brand-gold relative inline-block">Small Group</span>' ?>
        </h1>
        <div class="prose prose-xl text-white/80 max-w-2xl mx-auto font-light leading-relaxed animate-fade-in-up delay-200">
            <?= nl2br(htmlspecialchars($content['hero']['subtitle']['value'] ?? 'We were not meant to do life alone. Find a community where you can belong, grow, and serve.')) ?>
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

<!-- Groups Grid -->
<section class="py-32 bg-white relative overflow-hidden">
    <!-- Background accents -->
    <div class="absolute top-0 right-0 w-1/3 h-1/3 bg-brand-green/5 rounded-full blur-[100px] -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-1/3 h-1/3 bg-brand-gold/5 rounded-full blur-[100px] translate-y-1/2 -translate-x-1/2"></div>

    <div class="container mx-auto px-6 md:px-12 lg:px-16 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-12">
            <?php foreach ($groups as $group): ?>
            <div class="group bg-white rounded-[2.5rem] border border-gray-100 overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col h-full">
                <div class="relative h-64 overflow-hidden">
                    <?php if (!empty($group['image']) && file_exists($group['image'])): ?>
                        <img src="<?= APP_URL . '/' . $group['image'] ?>" alt="<?= htmlspecialchars($group['name']) ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-1000">
                    <?php else: ?>
                        <div class="absolute inset-0 bg-gradient-to-br from-brand-green to-brand-green-dark group-hover:scale-110 transition-transform duration-1000"></div>
                        <div class="absolute inset-0 flex items-center justify-center text-white/20">
                            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17 20h5v-2a3 3 0 00-5.3-1.9M16 21H8a4 4 0 01-4-4V7a4 4 0 014-4h3a4 4 0 014 4v10a4 4 0 01-4 4z"/>
                            </svg>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Overlay Gradient -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-60 group-hover:opacity-40 transition-opacity"></div>
                    
                    <div class="absolute bottom-6 left-6 right-6">
                        <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-md rounded-lg text-[10px] font-bold text-white uppercase tracking-widest mb-3 border border-white/20">
                            <?= htmlspecialchars($group['type']) ?>
                        </span>
                        <h3 class="text-2xl font-bold text-white mb-0 translate-y-0 group-hover:-translate-y-1 transition-transform duration-500"><?= htmlspecialchars($group['name']) ?></h3>
                    </div>
                </div>
                
                <div class="p-8 md:p-10 flex flex-col flex-grow">
                    <div class="mb-6 space-y-3">
                        <?php if (!empty($group['schedule_info'])): ?>
                        <div class="flex items-start gap-3 text-sm text-gray-500">
                            <span class="w-5 h-5 flex items-center justify-center text-brand-gold mt-0.5">⏰</span>
                            <span><?= htmlspecialchars($group['schedule_info']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($group['location'])): ?>
                        <div class="flex items-start gap-3 text-sm text-gray-500">
                            <span class="w-5 h-5 flex items-center justify-center text-brand-green mt-0.5">📍</span>
                            <span><?= htmlspecialchars($group['location']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <p class="text-gray-600 mb-8 leading-relaxed font-light text-lg flex-grow">
                        <?= htmlspecialchars($group['description']) ?>
                    </p>
                    
                    <div class="mt-auto pt-8 border-t border-gray-50">
                        <a href="<?= APP_URL ?>/contact?subject=Joining <?= urlencode($group['name']) ?>" 
                           class="inline-flex items-center gap-3 text-brand-green font-bold hover:text-brand-green-dark transition-all group/link uppercase tracking-widest text-xs">
                            Request to Join
                            <span class="w-8 h-8 rounded-full bg-brand-green/10 flex items-center justify-center group-hover/link:bg-brand-green group-hover/link:text-white transition-all duration-300">
                                <svg class="w-4 h-4 transform group-hover/link:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <!-- Start a Group Card -->
            <div class="group bg-gray-50 rounded-[2.5rem] border-2 border-dashed border-gray-200 overflow-hidden hover:border-brand-green/30 transition-all duration-500 flex flex-col h-full items-center justify-center text-center p-10 min-h-[400px]">
                <div class="w-20 h-20 bg-white rounded-full shadow-lg flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-500">
                    <span class="text-4xl">🌱</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Start a Group</h3>
                <p class="text-gray-500 mb-8 font-light max-w-xs">
                    Interested in leading a small group? We provide training and resources to help you disciple others.
                </p>
                <a href="<?= APP_URL ?>/contact?subject=Start a Group" class="px-8 py-3 bg-white border border-gray-200 text-gray-700 text-xs font-bold rounded-xl hover:border-brand-green hover:text-brand-green transition-all duration-300 tracking-widest uppercase shadow-sm">
                    Contact Leadership
                </a>
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
                <?= $content['cta']['badge']['value'] ?? 'LEADERSHIP' ?>
            </span>
            <h2 class="font-heading font-bold text-5xl md:text-7xl lg:text-8xl text-white mb-10 leading-[1.1] tracking-tight">
                <?= isset($content['cta']['title']['value'])
                    ? preg_replace('/\*(.*?)\*/', '<span class="text-brand-gold relative inline-block">$1</span>', htmlspecialchars($content['cta']['title']['value']))
                    : 'Start a <span class="text-brand-gold relative inline-block">Group</span>' ?>
            </h2>
            <div class="prose prose-xl text-white/80 max-w-2xl mx-auto font-light leading-relaxed mb-16">
                <?= nl2br(htmlspecialchars($content['cta']['subtitle']['value'] ?? 'Interested in leading a small group? We provide training and resources to help you disciple others.')) ?>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-center gap-8">
                <a href="<?= APP_URL ?>/contact" 
                   class="group relative px-12 py-6 bg-white text-brand-green font-bold rounded-2xl transition-all duration-500 hover:bg-brand-gold hover:text-brand-green overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.3)] tracking-widest uppercase text-xs">
                    <span class="relative z-10">BECOME A LEADER</span>
                    <div class="absolute inset-0 bg-brand-gold translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'app/Views/layouts/footer.php'; ?>
