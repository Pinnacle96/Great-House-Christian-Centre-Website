<?php require_once 'app/Views/layouts/header.php'; ?>

<!-- Modern Hero Section -->
<section class="relative min-h-[70vh] flex items-center justify-center overflow-hidden">
    <!-- Animated background elements -->
    <div class="absolute inset-0 w-full h-full bg-brand-green">
        <div class="absolute inset-0 bg-[url('<?= APP_URL ?>/public/assets/images/hero-bg.jpg')] bg-cover bg-center opacity-30 mix-blend-overlay animate-slow-zoom"></div>
        <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-white/10 rounded-full blur-[100px] -translate-x-1/2 -translate-y-1/2 animate-pulse"></div>
        <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-brand-gold/20 rounded-full blur-[120px] translate-x-1/3 translate-y-1/3"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full opacity-[0.05]" 
             style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
    </div>
    
    <div class="relative z-20 text-center text-white container mx-auto px-6 md:px-12 lg:px-16">
        <div class="max-w-4xl mx-auto">
            <span class="inline-block px-5 py-2 bg-white/10 text-white rounded-full text-[10px] font-bold mb-8 tracking-[0.4em] uppercase backdrop-blur-sm border border-white/20">
                <?= $content['hero']['badge']['value'] ?? 'WORSHIP WITH US' ?>
            </span>
            <h1 class="font-heading font-bold text-6xl md:text-8xl lg:text-9xl mb-10 leading-[1.1] tracking-tight">
                <?= $content['hero']['title']['value'] ?? 'Services & Programs' ?>
            </h1>
            <p class="text-xl md:text-2xl text-white/80 max-w-2xl mx-auto font-light leading-relaxed mb-12">
                <?= $content['hero']['subtitle']['value'] ?? 'Join us as we gather to worship, learn, and grow together in Christ through our various weekly gatherings and ministries.' ?>
            </p>
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

<!-- Weekly Services -->
<section class="py-32 bg-[#F8F9FA] relative overflow-hidden">
    <!-- Background accents -->
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-brand-green/5 rounded-full blur-[100px] -translate-y-1/2 translate-x-1/2"></div>
    
    <div class="container mx-auto px-6 md:px-12 lg:px-16 relative z-10">
        <div class="text-center mb-24">
            <span class="inline-block px-4 py-1.5 bg-brand-green/10 text-brand-green rounded-full text-[10px] font-bold mb-6 tracking-[0.3em] uppercase">
                <?= $content['weekly_services']['badge']['value'] ?? 'WEEKLY GATHERINGS' ?>
            </span>
            <h2 class="font-heading font-bold text-5xl md:text-6xl text-gray-900 mb-8 leading-tight"><?= $content['weekly_services']['title']['value'] ?? 'Weekly Services' ?></h2>
            <p class="text-xl text-gray-500 max-w-3xl mx-auto font-light leading-relaxed">
                <?= $content['weekly_services']['subtitle']['value'] ?? 'Join our vibrant community for powerful worship and life-changing messages every week.' ?>
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <?php 
            $services = [
                [
                    'emoji' => '⛪',
                    'title' => 'Sunday Celebration',
                    'day' => 'Every Sunday Morning',
                    'time' => '9:00 AM',
                    'location' => 'Main Auditorium',
                    'quote' => 'Powerful worship and life-changing messages to start your week.'
                ],
                [
                    'emoji' => '📖',
                    'title' => 'Bible Study',
                    'day' => 'Every Wednesday Evening',
                    'time' => '6:00 PM',
                    'location' => 'Main Auditorium & Online',
                    'quote' => "Deep dive into God's Word and practical application for daily living."
                ],
                [
                    'emoji' => '🙏',
                    'title' => 'Prayer Meeting',
                    'day' => 'Every Friday Evening',
                    'time' => '5:00 PM',
                    'location' => 'Prayer Room',
                    'quote' => 'Corporate prayer and intercession for our needs, church, and nation.'
                ]
            ];
            foreach($services as $service): 
            ?>
            <div class="group bg-white rounded-[40px] p-10 border border-gray-100 shadow-[0_40px_100px_-20px_rgba(0,0,0,0.05)] hover:shadow-[0_40px_100px_-20px_rgba(4,108,78,0.15)] transition-all duration-700 hover:-translate-y-3">
                <div class="w-20 h-20 bg-brand-green/5 rounded-3xl flex items-center justify-center mb-10 group-hover:bg-brand-green group-hover:rotate-6 transition-all duration-500">
                    <span class="text-4xl group-hover:scale-110 transition-transform duration-500"><?= $service['emoji'] ?></span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-6 group-hover:text-brand-green transition-colors"><?= $service['title'] ?></h3>
                <div class="space-y-5 mb-10">
                    <div class="flex items-center text-gray-400 group-hover:text-gray-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-brand-green" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-xs font-bold tracking-widest uppercase"><?= $service['day'] ?></span>
                    </div>
                    <div class="flex items-center text-gray-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-brand-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-4xl font-heading font-bold tracking-tight"><?= $service['time'] ?></span>
                    </div>
                    <div class="flex items-center text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-brand-green" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-sm px-4 py-1.5 bg-gray-50 rounded-full group-hover:bg-brand-green/5 group-hover:text-brand-green transition-all"><?= $service['location'] ?></span>
                    </div>
                </div>
                <div class="pt-8 border-t border-gray-50 group-hover:border-brand-green/10 transition-colors">
                    <p class="text-gray-500 leading-relaxed italic font-light">"<?= $service['quote'] ?>"</p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Ministries & Programs -->
<section class="py-32 bg-white relative overflow-hidden">
    <!-- Background accents -->
    <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-brand-green/5 rounded-full blur-[100px] translate-y-1/2 -translate-x-1/2"></div>

    <div class="container mx-auto px-6 md:px-12 lg:px-16 relative z-10">
        <div class="text-center mb-24">
            <span class="inline-block px-4 py-1.5 bg-brand-green/10 text-brand-green rounded-full text-[10px] font-bold mb-6 tracking-[0.3em] uppercase">
                <?= $content['ministries']['badge']['value'] ?? 'OUR COMMUNITIES' ?>
            </span>
            <h2 class="font-heading font-bold text-5xl md:text-6xl text-gray-900 mb-8 leading-tight"><?= $content['ministries']['title']['value'] ?? 'Ministries & Programs' ?></h2>
            <p class="text-xl text-gray-500 max-w-3xl mx-auto font-light leading-relaxed">
                <?= $content['ministries']['subtitle']['value'] ?? 'Find your place and grow with others in our specialized ministry departments.' ?>
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php 
            $ministries = [
                [
                    'emoji' => '👨‍👦',
                    'title' => "Men's Fellowship",
                    'desc' => 'Empowering men to be spiritual leaders in their homes, workplace, and communities.'
                ],
                [
                    'emoji' => '👩‍👧',
                    'title' => "Women's Ministry",
                    'desc' => 'A sisterhood of faith, prayer, and mutual support dedicated to spiritual growth.'
                ],
                [
                    'emoji' => '👦👧',
                    'title' => 'Youth Church',
                    'desc' => 'Dynamic services and programs designed specifically for the next generation of believers.'
                ],
                [
                    'emoji' => '🧒',
                    'title' => "Children's Dept",
                    'desc' => 'Fun, engaging, and biblical learning experiences for kids ages 3-12 in a safe environment.'
                ]
            ];
            foreach($ministries as $min): 
            ?>
            <div class="group bg-gray-50 rounded-[35px] p-8 border border-transparent hover:border-brand-green/10 hover:bg-white hover:shadow-[0_30px_70px_-15px_rgba(0,0,0,0.08)] transition-all duration-500">
                <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mb-8 group-hover:bg-brand-green group-hover:rotate-12 transition-all duration-500 shadow-sm group-hover:shadow-lg">
                    <span class="text-2xl group-hover:scale-110 transition-transform duration-500"><?= $min['emoji'] ?></span>
                </div>
                <h3 class="font-bold text-xl mb-4 text-gray-900 group-hover:text-brand-green transition-colors"><?= $min['title'] ?></h3>
                <p class="text-gray-500 leading-relaxed text-sm font-light mb-8"><?= $min['desc'] ?></p>
                <div class="pt-8 border-t border-gray-100 opacity-0 group-hover:opacity-100 transition-all duration-500 transform translate-y-2 group-hover:translate-y-0">
                    <a href="<?= APP_URL ?>/contact" class="inline-flex items-center text-brand-green font-bold text-[10px] tracking-[0.2em] uppercase group/link">
                        LEARN MORE 
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-2 transform group-hover/link:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
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
                <?= $content['cta']['badge']['value'] ?? 'JOIN THE FAMILY' ?>
            </span>
            <h2 class="font-heading font-bold text-5xl md:text-7xl lg:text-8xl text-white mb-10 leading-[1.1] tracking-tight">
                <?= $content['cta']['title']['value'] ?? 'Ready to Join Us This Week?' ?>
            </h2>
            <p class="text-xl md:text-2xl text-white/80 mb-16 font-light leading-relaxed max-w-2xl mx-auto">
                <?= $content['cta']['subtitle']['value'] ?? "Experience the life-changing power of worship and community. We can't wait to welcome you home." ?>
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-8">
                <a href="<?= APP_URL ?>/contact" 
                   class="group relative px-12 py-6 bg-white text-brand-green font-bold rounded-2xl transition-all duration-500 hover:bg-brand-gold hover:text-brand-green overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.3)] tracking-widest uppercase text-xs">
                    <span class="relative z-10">GET DIRECTIONS</span>
                    <div class="absolute inset-0 bg-brand-gold translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                </a>
                <a href="<?= APP_URL ?>/events" 
                   class="px-12 py-6 bg-transparent border-2 border-white/30 text-white font-bold rounded-2xl hover:bg-white hover:text-brand-green transition-all duration-500 transform hover:scale-105 backdrop-blur-sm tracking-widest uppercase text-xs">
                    VIEW ALL EVENTS
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'app/Views/layouts/footer.php'; ?>