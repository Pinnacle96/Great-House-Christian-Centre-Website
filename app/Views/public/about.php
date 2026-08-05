<?php 
require_once 'app/Views/layouts/header.php'; 
?>

<!-- Modern Hero Section -->
<section class="relative min-h-[70vh] flex items-center justify-center overflow-hidden">
    <!-- Animated background elements -->
    <div class="absolute inset-0 w-full h-full">
        <div class="absolute inset-0 bg-gradient-to-br from-brand-green/90 via-brand-green/70 to-brand-green-dark/90 z-10"></div>
        <img src="<?= APP_URL ?>/<?= $content['hero']['image']['value'] ?? 'assets/img/bg.jpg' ?>" alt="About GHCC" class="w-full h-full object-cover transform scale-105 animate-slow-zoom">
        <div class="absolute inset-0 bg-brand-green mix-blend-multiply opacity-20 z-11"></div>
    </div>
    
    <!-- Decorative Overlays -->
    <div class="absolute inset-0 z-12 opacity-30" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
    
    <div class="relative z-20 text-center text-white container mx-auto px-6">
        <div class="max-w-4xl mx-auto">
            <span class="inline-block px-5 py-2 bg-white/10 text-white rounded-full text-xs font-bold mb-10 tracking-[0.3em] uppercase backdrop-blur-md border border-white/20 animate-fade-in-down">
                OUR STORY & MISSION
            </span>
            <h1 class="font-heading font-bold text-6xl md:text-8xl lg:text-9xl mb-10 leading-[0.9] tracking-tighter animate-fade-in-up">
                <?= isset($content['hero']['title']['value']) 
                    ? preg_replace('/\*(.*?)\*/', '<span class="text-brand-gold relative inline-block">$1</span>', htmlspecialchars($content['hero']['title']['value'])) 
                    : 'About <span class="text-brand-gold relative inline-block">GHCC</span>' ?>
            </h1>
            <div class="prose prose-lg prose-invert max-w-2xl mx-auto font-light leading-relaxed text-white/90 mb-12 animate-fade-in-up delay-200">
                <?= nl2br(htmlspecialchars($content['hero']['subtitle']['value'] ?? 'Discover who we are, what we believe, and where God is taking us as a family of faith united in purpose and power.')) ?>
            </div>
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

<!-- Vision & Mission -->
<section class="py-40 bg-white relative overflow-hidden">
    <!-- Background Accents -->
    <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-brand-green/5 rounded-full blur-[120px] translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-brand-gold/5 rounded-full blur-[120px] -translate-x-1/2 translate-y-1/2"></div>

    <div class="container mx-auto px-6 md:px-12 lg:px-16 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-24 lg:gap-32 items-center">
            <div class="space-y-24">
                <div class="group">
                    <div class="flex items-center gap-8 mb-10">
                        <div class="w-24 h-24 bg-brand-green/10 rounded-[2.5rem] flex items-center justify-center group-hover:bg-brand-green group-hover:rotate-12 transition-all duration-700 shadow-xl shadow-brand-green/5 overflow-hidden relative">
                            <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-700"></div>
                            <span class="text-5xl relative z-10 group-hover:scale-110 transition-transform duration-500">👁️</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-brand-green uppercase tracking-[0.4em] mb-3 block opacity-70">THE VISION</span>
                            <h2 class="font-heading font-bold text-5xl md:text-6xl text-gray-900 leading-none tracking-tight"><?= $content['vision']['title']['value'] ?? 'Our Vision' ?></h2>
                        </div>
                    </div>
                    <div class="prose prose-xl text-gray-600 leading-[1.7] font-light">
                        <?= nl2br(htmlspecialchars($content['vision']['content']['value'] ?? 'To raise a people of power, purpose, and passion for God\'s Kingdom.')) ?>
                    </div>
                </div>
                
                <div class="group">
                    <div class="flex items-center gap-8 mb-10">
                        <div class="w-24 h-24 bg-brand-green/10 rounded-[2.5rem] flex items-center justify-center group-hover:bg-brand-green group-hover:-rotate-12 transition-all duration-700 shadow-xl shadow-brand-green/5 overflow-hidden relative">
                            <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-700"></div>
                            <span class="text-5xl relative z-10 group-hover:scale-110 transition-transform duration-500">🎯</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-brand-green uppercase tracking-[0.4em] mb-3 block opacity-70">THE MISSION</span>
                            <h2 class="font-heading font-bold text-5xl md:text-6xl text-gray-900 leading-none tracking-tight"><?= $content['mission']['title']['value'] ?? 'Our Mission' ?></h2>
                        </div>
                    </div>
                    <div class="prose prose-xl text-gray-600 leading-[1.7] font-light">
                        <?= nl2br(htmlspecialchars($content['mission']['content']['value'] ?? 'To preach the undiluted Word of God and disciple believers into maturity.')) ?>
                    </div>
                </div>
            </div>
            
            <div class="relative group">
                <div class="absolute inset-0 bg-brand-green/5 rounded-[4rem] -rotate-6 scale-105 transition-transform duration-1000 group-hover:rotate-0 group-hover:scale-100"></div>
                <div class="relative bg-gradient-to-br from-brand-green via-brand-green to-brand-green-dark rounded-[4rem] overflow-hidden aspect-[4/5] flex items-center justify-center shadow-[0_30px_60px_-15px_rgba(20,68,44,0.3)]">
                    <!-- Glassmorphism card inside -->
                    <div class="absolute inset-0 opacity-20" style="background-image: url('<?= APP_URL ?>/assets/img/pattern.png'); background-size: 200px;"></div>
                    <div class="text-center text-white p-12 relative z-10">
                        <div class="w-36 h-36 bg-white/10 backdrop-blur-xl rounded-[3rem] flex items-center justify-center mx-auto mb-16 transform group-hover:scale-110 group-hover:rotate-[15deg] transition-all duration-700 border border-white/20 shadow-2xl">
                            <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                        <h3 class="text-5xl font-bold mb-8 leading-tight tracking-tight">Ministry Focus</h3>
                        <div class="h-1 w-24 bg-brand-gold mx-auto mb-8 rounded-full"></div>
                        <p class="text-xl text-white/80 font-light tracking-[0.2em] uppercase">Transforming Lives Through Christ</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Core Values -->
<section class="py-40 bg-gray-50 relative overflow-hidden">
    <div class="container mx-auto px-6 md:px-12 lg:px-16 relative z-10">
        <div class="text-center mb-32 max-w-3xl mx-auto">
            <span class="inline-block px-4 py-1.5 bg-brand-green/10 text-brand-green rounded-full text-[10px] font-bold mb-8 tracking-[0.4em] uppercase border border-brand-green/20">
                <?= $content['core_values']['badge']['value'] ?? 'CORE VALUES' ?>
            </span>
            <h2 class="font-heading font-bold text-5xl md:text-7xl text-gray-900 mb-10 leading-tight tracking-tight">
                <?= isset($content['core_values']['title']['value']) 
                    ? preg_replace('/\*(.*?)\*/', '<span class="text-brand-green">$1</span>', htmlspecialchars($content['core_values']['title']['value'])) 
                    : 'What We <span class="text-brand-green">Stand For</span>' ?>
            </h2>
            <div class="prose prose-xl text-gray-500 font-light leading-relaxed max-w-2xl mx-auto">
                <?= nl2br(htmlspecialchars($content['core_values']['subtitle']['value'] ?? 'These foundational principles guide everything we do as a church family, reflecting our commitment to God and His people.')) ?>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 lg:gap-16">
            <!-- Card 1 -->
            <div class="bg-white rounded-[3rem] p-12 lg:p-16 border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-700 group relative overflow-hidden flex flex-col h-full">
                <div class="absolute top-0 right-0 w-40 h-40 bg-brand-green/5 rounded-bl-[5rem] -mr-12 -mt-12 group-hover:bg-brand-green/10 transition-all duration-700"></div>
                <div class="w-24 h-24 bg-brand-green/10 rounded-3xl flex items-center justify-center mb-12 group-hover:bg-brand-green group-hover:rotate-12 transition-all duration-700 shadow-lg shadow-brand-green/5 overflow-hidden relative">
                    <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-700"></div>
                    <span class="text-5xl relative z-10 group-hover:scale-110 transition-transform duration-500">👨‍👩‍👧‍👦</span>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 mb-8 group-hover:text-brand-green transition-colors leading-tight tracking-tight">Family Community</h3>
                <div class="prose prose-lg text-gray-500 leading-relaxed font-light flex-grow">
                    <?= $content['core_values']['card1_content']['value'] ?? 'We believe in the power of authentic community and treating every member as part of God\'s family.' ?>
                </div>
            </div>
            
            <!-- Card 2 -->
            <div class="bg-white rounded-[3rem] p-12 lg:p-16 border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-700 group relative overflow-hidden flex flex-col h-full">
                <div class="absolute top-0 right-0 w-40 h-40 bg-brand-green/5 rounded-bl-[5rem] -mr-12 -mt-12 group-hover:bg-brand-green/10 transition-all duration-700"></div>
                <div class="w-24 h-24 bg-brand-green/10 rounded-3xl flex items-center justify-center mb-12 group-hover:bg-brand-green group-hover:-rotate-12 transition-all duration-700 shadow-lg shadow-brand-green/5 overflow-hidden relative">
                    <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-700"></div>
                    <span class="text-5xl relative z-10 group-hover:scale-110 transition-transform duration-500">👑</span>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 mb-8 group-hover:text-brand-green transition-colors leading-tight tracking-tight">Kingdom Culture</h3>
                <div class="prose prose-lg text-gray-500 leading-relaxed font-light flex-grow">
                    <?= $content['core_values']['card2_content']['value'] ?? 'We uphold the values and principles of God\'s Kingdom in our daily lives and relationships.' ?>
                </div>
            </div>
            
            <!-- Card 3 -->
            <div class="bg-white rounded-[3rem] p-12 lg:p-16 border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-700 group relative overflow-hidden flex flex-col h-full">
                <div class="absolute top-0 right-0 w-40 h-40 bg-brand-green/5 rounded-bl-[5rem] -mr-12 -mt-12 group-hover:bg-brand-green/10 transition-all duration-700"></div>
                <div class="w-24 h-24 bg-brand-green/10 rounded-3xl flex items-center justify-center mb-12 group-hover:bg-brand-green group-hover:rotate-12 transition-all duration-700 shadow-lg shadow-brand-green/5 overflow-hidden relative">
                    <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-700"></div>
                    <span class="text-5xl relative z-10 group-hover:scale-110 transition-transform duration-500">✨</span>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 mb-8 group-hover:text-brand-green transition-colors leading-tight tracking-tight">Divine Purpose</h3>
                <div class="prose prose-lg text-gray-500 leading-relaxed font-light flex-grow">
                    <?= $content['core_values']['card3_content']['value'] ?? 'We help every individual discover and walk in their God-ordained purpose and destiny.' ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Leadership Team -->
<section class="py-40 bg-white relative overflow-hidden">
    <div class="container mx-auto px-6 md:px-12 lg:px-16 relative z-10">
        <div class="text-center mb-16 md:mb-24">
            <span class="inline-block px-4 py-1.5 bg-brand-green/10 text-brand-green rounded-full text-[10px] font-bold mb-6 tracking-[0.4em] uppercase border border-brand-green/20">
                <?= $content['leadership']['badge']['value'] ?? 'OUR LEADERS' ?>
            </span>
            <h2 class="font-heading font-bold text-5xl md:text-7xl text-gray-900 mb-10 leading-tight tracking-tight">
                <?= isset($content['leadership']['title']['value']) 
                    ? preg_replace('/\*(.*?)\*/', '<span class="text-brand-green">$1</span>', htmlspecialchars($content['leadership']['title']['value'])) 
                    : 'Our <span class="text-brand-green">Leadership</span> Team' ?>
            </h2>
            <div class="prose prose-xl text-gray-500 font-light leading-relaxed max-w-2xl mx-auto">
                <?= nl2br(htmlspecialchars($content['leadership']['subtitle']['value'] ?? 'Godly leaders dedicated to serving and guiding our church family with wisdom, integrity, and love.')) ?>
            </div>
        </div>
        
        <?php if (!empty($team)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-16 lg:gap-24">
            <?php foreach($team as $index => $leader): 
                $rotate = ($index % 2 == 0) ? 'rotate-12' : '-rotate-12';
            ?>
            <div class="text-center group">
                <div class="relative w-80 h-80 mx-auto mb-12">
                    <div class="absolute inset-0 bg-brand-green rounded-[4rem] <?= $rotate ?> group-hover:rotate-0 transition-transform duration-700 opacity-5"></div>
                    <div class="relative w-full h-full bg-gradient-to-br from-brand-green to-brand-green-dark rounded-[4rem] overflow-hidden shadow-[0_30px_60px_-15px_rgba(20,68,44,0.3)] transform group-hover:scale-105 transition-all duration-700 flex items-center justify-center border-4 border-white">
                        <?php if ($leader['image']): ?>
                            <img src="<?= APP_URL ?>/<?= $leader['image'] ?>" alt="<?= htmlspecialchars($leader['name']) ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <svg class="w-36 h-36 text-white/10 group-hover:scale-110 transition-transform duration-700" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        <?php endif; ?>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 mb-3 group-hover:text-brand-green transition-colors tracking-tight"><?= htmlspecialchars($leader['name']) ?></h3>
                <p class="text-brand-green font-bold text-[10px] uppercase tracking-[0.4em] opacity-80"><?= htmlspecialchars($leader['role']) ?></p>
                
                <?php if ($leader['facebook'] || $leader['twitter'] || $leader['instagram']): ?>
                <div class="flex justify-center gap-4 mt-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <?php if ($leader['facebook']): ?>
                        <a href="<?= htmlspecialchars($leader['facebook']) ?>" target="_blank" class="text-gray-400 hover:text-brand-green"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                    <?php if ($leader['twitter']): ?>
                        <a href="<?= htmlspecialchars($leader['twitter']) ?>" target="_blank" class="text-gray-400 hover:text-brand-green"><i class="fab fa-twitter"></i></a>
                    <?php endif; ?>
                    <?php if ($leader['instagram']): ?>
                        <a href="<?= htmlspecialchars($leader['instagram']) ?>" target="_blank" class="text-gray-400 hover:text-brand-green"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <div class="text-center text-gray-400 py-10">
                <p>No team members added yet.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Join Family Section -->
<section class="py-40 bg-brand-green relative overflow-hidden">
    <!-- Animated background elements -->
    <div class="absolute top-0 left-0 w-[600px] h-[600px] bg-white/5 rounded-full blur-[120px] -translate-x-1/2 -translate-y-1/2 animate-pulse"></div>
    <div class="absolute bottom-0 right-0 w-[700px] h-[700px] bg-brand-gold/10 rounded-full blur-[120px] translate-x-1/3 translate-y-1/3"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full opacity-[0.03] pointer-events-none" 
         style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
    
    <div class="container mx-auto px-6 md:px-12 lg:px-16 text-center relative z-10">
        <div class="max-w-4xl mx-auto">
            <span class="inline-block px-5 py-2 bg-white/10 text-white rounded-full text-[10px] font-bold mb-10 tracking-[0.4em] uppercase backdrop-blur-sm border border-white/20">
                <?= $content['cta']['badge']['value'] ?? 'JOIN OUR FAMILY' ?>
            </span>
            <h2 class="font-heading font-bold text-5xl md:text-7xl lg:text-8xl text-white mb-10 leading-[1.1] tracking-tight">
                <?= isset($content['cta']['title']['value']) 
                    ? preg_replace('/\*(.*?)\*/', '<span class="text-brand-gold">$1</span>', htmlspecialchars($content['cta']['title']['value'])) 
                    : 'Ready to Become Part of<br><span class="text-brand-gold">Something Great?</span>' ?>
            </h2>
            <div class="prose prose-lg prose-invert text-white/80 mb-16 font-light leading-relaxed max-w-2xl mx-auto">
                <?= nl2br(htmlspecialchars($content['cta']['subtitle']['value'] ?? 'Whether you\'re looking for a spiritual home or just want to learn more about us, we can\'t wait to welcome you with open arms.')) ?>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-center gap-8">
                <a href="<?= APP_URL ?>/contact" 
                   class="group relative px-12 py-6 bg-white text-brand-green font-bold rounded-2xl transition-all duration-500 hover:bg-brand-gold hover:text-brand-green overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.3)] tracking-widest uppercase text-xs">
                    <span class="relative z-10">VISIT US TODAY</span>
                    <div class="absolute inset-0 bg-brand-gold translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                </a>
                <a href="<?= APP_URL ?>/services" 
                   class="px-12 py-6 bg-transparent border-2 border-white/30 text-white font-bold rounded-2xl hover:bg-white hover:text-brand-green transition-all duration-500 transform hover:scale-105 backdrop-blur-sm tracking-widest uppercase text-xs">
                    EXPLORE SERVICES
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'app/Views/layouts/footer.php'; ?>

