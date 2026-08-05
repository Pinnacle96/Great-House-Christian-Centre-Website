<?php require_once 'app/Views/layouts/header.php'; 
$date = new DateTime($event['start_datetime']);
$endDate = $event['end_datetime'] ? new DateTime($event['end_datetime']) : null;
$startTime = $date->format('g:i A');
$endTime = $endDate ? $endDate->format('g:i A') : '';
?>

<!-- Hero Section -->
<section class="relative min-h-[50vh] flex items-end justify-center overflow-hidden pb-20">
    <div class="absolute inset-0 w-full h-full">
        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent z-10"></div>
        <div class="absolute inset-0 bg-[url('<?= APP_URL ?>/<?= $event['image'] ?? 'assets/img/hero-bg.jpg' ?>')] bg-cover bg-center animate-slow-zoom"></div>
    </div>
    
    <div class="relative z-20 text-center text-white container mx-auto px-6">
        <span class="inline-block px-5 py-2 bg-brand-green/90 backdrop-blur-md rounded-full text-[10px] font-bold mb-8 border border-white/20 tracking-[0.3em] uppercase animate-fade-in-down">
            <?= htmlspecialchars($event['category'] ?? 'EVENT') ?>
        </span>
        <h1 class="font-heading font-bold text-5xl md:text-7xl mb-6 leading-tight tracking-tight animate-fade-in-up">
            <?= htmlspecialchars($event['title']) ?>
        </h1>
        <div class="flex flex-wrap justify-center gap-6 text-sm md:text-base font-medium animate-fade-in-up delay-200">
            <div class="flex items-center gap-2 bg-white/10 px-4 py-2 rounded-full backdrop-blur-sm">
                <span class="text-brand-gold">📅</span>
                <span><?= $date->format('l, F j, Y') ?></span>
            </div>
            <div class="flex items-center gap-2 bg-white/10 px-4 py-2 rounded-full backdrop-blur-sm">
                <span class="text-brand-gold">⏰</span>
                <span><?= $startTime ?><?= $endTime ? ' - ' . $endTime : '' ?></span>
            </div>
            <div class="flex items-center gap-2 bg-white/10 px-4 py-2 rounded-full backdrop-blur-sm">
                <span class="text-brand-gold">📍</span>
                <span><?= htmlspecialchars($event['location'] ?? 'Main Auditorium') ?></span>
            </div>
        </div>
    </div>
</section>

<!-- Event Details Content -->
<section class="py-20 bg-white relative">
    <div class="container mx-auto px-6 md:px-12 lg:px-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <!-- Main Content -->
            <div class="lg:col-span-8">
                <div class="prose prose-lg max-w-none text-gray-600 font-light leading-relaxed">
                    <h3 class="text-3xl font-heading font-bold text-gray-900 mb-6">About This Event</h3>
                    <p class="mb-8 text-lg">
                        <?= nl2br(htmlspecialchars($event['description'])) ?>
                    </p>
                    
                    <!-- Additional Info Placeholder (could be dynamic later) -->
                    <div class="bg-gray-50 p-8 rounded-3xl border border-gray-100 my-10">
                        <h4 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-brand-green/10 flex items-center justify-center text-brand-green text-sm">ℹ️</span>
                            What to Expect
                        </h4>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <span class="text-brand-green mt-1">✓</span>
                                <span>Powerful worship sessions led by the GHCC Choir</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-brand-green mt-1">✓</span>
                                <span>Life-transforming word and teaching</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-brand-green mt-1">✓</span>
                                <span>Opportunity for prayer and counseling</span>
                            </li>
                        </ul>
                    </div>
                </div>

                    <!-- Registration / CTA -->
                    <?php if ($event['requires_registration']): ?>
                        <div class="mt-12 p-10 bg-brand-green rounded-[2rem] text-white relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                            
                            <?php if (isset($_SESSION['info'])): ?>
                                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-6" role="alert">
                                    <span class="block sm:inline"><?= $_SESSION['info'] ?></span>
                                    <?php unset($_SESSION['info']); ?>
                                </div>
                            <?php endif; ?>

                            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                                <div>
                                    <h3 class="text-2xl font-bold mb-2">Save Your Spot!</h3>
                                    <p class="text-white/80 font-light">Registration is required for this event.</p>
                                </div>
                                <a href="<?= APP_URL ?>/events/<?= $event['slug'] ?>/register" class="px-8 py-4 bg-white text-brand-green font-bold rounded-xl hover:bg-brand-gold transition-colors shadow-lg whitespace-nowrap">
                                    REGISTER NOW
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-4 space-y-8">
                <!-- Event Info Card -->
                <div class="bg-white p-8 rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 sticky top-8">
                    <h4 class="text-lg font-bold text-gray-900 mb-6 uppercase tracking-widest text-xs">Event Details</h4>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-brand-green/5 flex items-center justify-center text-brand-green flex-shrink-0">
                                📅
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Date</p>
                                <p class="font-medium text-gray-900"><?= $date->format('l, F j, Y') ?></p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-brand-green/5 flex items-center justify-center text-brand-green flex-shrink-0">
                                ⏰
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Time</p>
                                <p class="font-medium text-gray-900"><?= $startTime ?><?= $endTime ? ' - ' . $endTime : '' ?></p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-brand-green/5 flex items-center justify-center text-brand-green flex-shrink-0">
                                📍
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Location</p>
                                <p class="font-medium text-gray-900"><?= htmlspecialchars($event['location'] ?? 'Main Auditorium') ?></p>
                                <p class="text-sm text-gray-500 mt-1">123 Church Street, Lagos</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-8 border-t border-gray-100">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Share Event</p>
                        <div class="flex gap-2">
                            <a href="#" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-[#1877F2] hover:text-white transition-colors">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-[#1DA1F2] hover:text-white transition-colors">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-[#25D366] hover:text-white transition-colors">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Events Widget -->
                <?php if (!empty($upcoming_events)): ?>
                <div class="bg-gray-50 p-8 rounded-3xl border border-gray-100">
                    <h4 class="text-lg font-bold text-gray-900 mb-6 uppercase tracking-widest text-xs">More Upcoming Events</h4>
                    <div class="space-y-6">
                        <?php foreach($upcoming_events as $next_event): 
                            $nextDate = new DateTime($next_event['start_datetime']);
                        ?>
                        <a href="<?= APP_URL ?>/events/<?= $next_event['slug'] ?>" class="flex gap-4 group">
                            <div class="w-16 h-16 rounded-xl bg-white overflow-hidden flex-shrink-0 border border-gray-100 group-hover:border-brand-green/30 transition-colors">
                                <img src="<?= APP_URL ?>/<?= $next_event['image'] ?? 'assets/img/event-placeholder.jpg' ?>" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="text-xs text-brand-green font-bold mb-1"><?= $nextDate->format('M d, Y') ?></p>
                                <h5 class="font-bold text-gray-900 leading-tight group-hover:text-brand-green transition-colors"><?= htmlspecialchars($next_event['title']) ?></h5>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'app/Views/layouts/footer.php'; ?>
