<?php require_once 'app/Views/layouts/header.php'; ?>
<?php
$bankBranch = !empty($branch) ? $branch : ($paymentBranch ?? null);
$bankValue = function ($branchKey, $contentKey, $fallback) use ($bankBranch, $content) {
    $branchValue = trim((string)($bankBranch[$branchKey] ?? ''));
    if ($branchValue !== '') {
        return $branchValue;
    }

    $contentValue = trim((string)($content['bank_details'][$contentKey]['value'] ?? ''));
    return $contentValue !== '' ? $contentValue : $fallback;
};
?>

<!-- Modern Hero Section -->
<section class="relative min-h-[70vh] flex items-center justify-center overflow-hidden">
    <!-- Animated background elements -->
    <div class="absolute inset-0 w-full h-full bg-brand-green">
        <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-white/10 rounded-full blur-[100px] -translate-x-1/2 -translate-y-1/2 animate-pulse"></div>
        <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-brand-gold/20 rounded-full blur-[120px] translate-x-1/3 translate-y-1/3"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full opacity-[0.05]" 
             style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
    </div>
    
    <div class="relative z-20 text-center text-white container mx-auto px-6 md:px-12 lg:px-16">
        <div class="max-w-4xl mx-auto">
            <span class="inline-block px-5 py-2 bg-white/10 text-white rounded-full text-[10px] font-bold mb-8 tracking-[0.4em] uppercase backdrop-blur-sm border border-white/20">
                <?= $content['hero']['badge']['value'] ?? 'GENEROSITY' ?>
            </span>
            <h1 class="font-heading font-bold text-6xl md:text-8xl lg:text-9xl mb-10 leading-[1.1] tracking-tight">
                <?= !empty($branch)
                    ? htmlspecialchars($branch['name'])
                    : (isset($content['hero']['title']['value'])
                        ? preg_replace('/\*(.*?)\*/', '<span class="text-brand-gold relative inline-block">$1</span>', htmlspecialchars($content['hero']['title']['value']))
                        : 'Give Online') ?>
            </h1>
            <div class="prose prose-xl text-white/80 max-w-2xl mx-auto font-light leading-relaxed mb-12 italic">
                <?= nl2br(htmlspecialchars($content['hero']['subtitle']['value'] ?? '"God loves a cheerful giver." — 2 Corinthians 9:7')) ?>
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
</section>

<!-- Giving Content -->
<section class="py-32 bg-[#F8F9FA] relative">
    <div class="container mx-auto px-6 md:px-12 lg:px-16 max-w-7xl">
        
        <?php if(isset($_GET['status'])): ?>
            <?php if($_GET['status'] == 'success'): ?>
                <div class="mb-12 bg-green-50 border border-green-200 text-green-800 rounded-2xl p-6 flex items-center shadow-sm">
                    <svg class="w-8 h-8 mr-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <h3 class="font-bold text-lg">Thank You!</h3>
                        <p>Your donation was successful. May God bless you abundantly.</p>
                    </div>
                </div>
            <?php elseif($_GET['status'] == 'failed' || $_GET['status'] == 'error'): ?>
                <div class="mb-12 bg-red-50 border border-red-200 text-red-800 rounded-2xl p-6 flex items-center shadow-sm">
                    <svg class="w-8 h-8 mr-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <h3 class="font-bold text-lg">Payment Failed</h3>
                        <p>There was an issue processing your donation. Please try again.</p>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-start">
            <!-- Online Giving Form -->
            <div class="lg:col-span-7">
                <div class="bg-white rounded-[40px] p-10 md:p-16 shadow-[0_40px_100px_-20px_rgba(0,0,0,0.08)] border border-gray-100 relative overflow-hidden group">
                    <!-- Subtle background glow -->
                    <div class="absolute -top-24 -right-24 w-64 h-64 bg-brand-green/5 rounded-full blur-[80px] group-hover:bg-brand-green/10 transition-colors duration-700"></div>
                    
                    <div class="relative z-10">
                        <div class="mb-12">
                            <span class="inline-block px-4 py-1.5 bg-brand-green/10 text-brand-green rounded-full text-[10px] font-bold mb-6 tracking-widest uppercase">
                                <?= $content['giving_form']['badge']['value'] ?? 'SECURE GIVING' ?>
                            </span>
                            <h2 class="font-heading font-bold text-4xl md:text-5xl text-gray-900 mb-6 leading-tight"><?= $content['giving_form']['title']['value'] ?? 'Make a Donation' ?></h2>
                            <p class="text-gray-500 text-lg font-light leading-relaxed">
                                <?= $content['giving_form']['subtitle']['value'] ?? 'Support the work of God through your generous giving. Your contribution helps us reach more lives.' ?>
                            </p>
                        </div>
                        
                        <form action="<?= htmlspecialchars($formAction ?? APP_URL . '/give/process') ?>" method="POST" id="give-form" class="space-y-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-3">
                                    <label class="block text-gray-800 text-xs font-bold tracking-widest uppercase ml-1">Giving Category</label>
                                    <div class="relative group">
                                        <select name="fund_id" class="w-full bg-gray-50 border-none rounded-2xl px-6 py-5 focus:ring-2 focus:ring-brand-green/20 text-gray-700 font-medium transition-all appearance-none cursor-pointer">
                                            <?php foreach($funds as $fund): ?>
                                                <option value="<?= $fund['id'] ?>"><?= $fund['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-brand-green">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="space-y-3">
                                    <label class="block text-gray-800 text-xs font-bold tracking-widest uppercase ml-1">Amount (NGN)</label>
                                    <input type="number" name="amount" class="w-full bg-gray-50 border-none rounded-2xl px-6 py-5 focus:ring-2 focus:ring-brand-green/20 text-gray-700 font-medium transition-all placeholder:text-gray-400" placeholder="e.g. 5000" required min="100">
                                </div>
                            </div>
        
                            <div class="space-y-3">
                                <label class="block text-gray-800 text-xs font-bold tracking-widest uppercase ml-1">Full Name (Optional)</label>
                                <input type="text" name="name" class="w-full bg-gray-50 border-none rounded-2xl px-6 py-5 focus:ring-2 focus:ring-brand-green/20 text-gray-700 font-medium transition-all placeholder:text-gray-400" placeholder="Anonymous if empty">
                            </div>
        
                            <div class="space-y-3">
                                <label class="block text-gray-800 text-xs font-bold tracking-widest uppercase ml-1">Email Address</label>
                                <input type="email" name="email" class="w-full bg-gray-50 border-none rounded-2xl px-6 py-5 focus:ring-2 focus:ring-brand-green/20 text-gray-700 font-medium transition-all placeholder:text-gray-400" placeholder="For receipt" required>
                            </div>
        
                            <button type="submit" class="w-full group relative bg-brand-green text-white font-bold py-6 px-8 rounded-2xl overflow-hidden transition-all duration-500 hover:shadow-[0_20px_50px_rgba(4,108,78,0.3)]">
                                <span class="relative z-10 flex items-center justify-center gap-3 tracking-widest uppercase text-xs">
                                    Pay with Paystack
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </span>
                                <div class="absolute inset-0 bg-brand-green-dark translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                            </button>
                            <div class="text-center">
                                <p class="text-xs text-gray-400 flex items-center justify-center gap-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    Secured by Paystack
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Bank Transfer & Information -->
            <div class="lg:col-span-5 space-y-12">
                <!-- Bank Transfer Card -->
                <div class="bg-white rounded-[40px] p-10 md:p-12 shadow-[0_30px_80px_-15px_rgba(0,0,0,0.05)] border border-gray-100 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-brand-gold/5 rounded-bl-[100px] transition-all duration-500 group-hover:bg-brand-gold/10"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center mb-8">
                            <div class="w-14 h-14 bg-brand-green/10 rounded-2xl flex items-center justify-center mr-5 group-hover:scale-110 transition-transform duration-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-brand-green" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                </svg>
                            </div>
                            <h2 class="font-heading font-bold text-2xl md:text-3xl text-gray-900">Bank Transfer</h2>
                        </div>
                        
                        <p class="text-gray-500 mb-8 font-light leading-relaxed">For direct deposits or transfers, please use the account details below:</p>
                        
                        <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100 group-hover:border-brand-green/20 transition-colors duration-500">
                            <div class="space-y-6">
                                <div>
                                    <p class="text-[10px] font-bold tracking-widest text-gray-400 uppercase mb-1">Account Name</p>
                                    <p class="font-bold text-xl text-gray-900"><?= htmlspecialchars($bankValue('bank_account_name', 'account_name', 'Great House Christian Centre')) ?></p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold tracking-widest text-gray-400 uppercase mb-1">Account Number</p>
                                    <div class="flex items-center justify-between">
                                        <p class="text-3xl text-brand-green font-heading font-bold tracking-wider"><?= htmlspecialchars($bankValue('bank_account_number', 'account_number', '1234567890')) ?></p>
                                        <button class="p-2 hover:bg-brand-green/10 rounded-lg text-brand-green transition-colors" title="Copy Account Number">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold tracking-widest text-gray-400 uppercase mb-1">Bank Name</p>
                                    <p class="text-gray-900 font-bold"><?= htmlspecialchars($bankValue('bank_name', 'bank_name', 'Zenith Bank PLC')) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Why We Give Section -->
                <div class="bg-white rounded-[40px] p-10 md:p-12 shadow-[0_30px_80px_-15px_rgba(0,0,0,0.05)] border border-gray-100">
                    <h3 class="font-heading font-bold text-2xl md:text-3xl text-gray-900 mb-10"><?= $content['principles']['title']['value'] ?? 'Why We Give' ?></h3>
                    <ul class="space-y-6">
                        <?php 
                        $principles = [
                            $content['principles']['item1']['value'] ?? "To honor God with our substance",
                            $content['principles']['item2']['value'] ?? "To support the work of the ministry",
                            $content['principles']['item3']['value'] ?? "To advance the Kingdom of God on earth",
                            $content['principles']['item4']['value'] ?? "To experience God's provision and blessing"
                        ];
                        foreach($principles as $principle): 
                        ?>
                        <li class="flex items-start group">
                            <div class="w-10 h-10 bg-brand-green/10 rounded-xl flex items-center justify-center mr-5 group-hover:bg-brand-green group-hover:text-white transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-gray-600 font-light text-lg group-hover:text-gray-900 transition-colors py-1.5"><?= $principle ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
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
                <?= $content['cta']['badge']['value'] ?? 'OUR IMPACT' ?>
            </span>
            <h2 class="font-heading font-bold text-5xl md:text-7xl lg:text-8xl text-white mb-10 leading-[1.1] tracking-tight">
                <?= isset($content['cta']['title']['value'])
                    ? preg_replace('/\*(.*?)\*/', '<span class="text-brand-gold relative inline-block">$1</span>', htmlspecialchars($content['cta']['title']['value']))
                    : 'Your Giving Makes a Difference' ?>
            </h2>
            <div class="prose prose-lg prose-invert text-white/80 mb-16 font-light leading-relaxed max-w-2xl mx-auto">
                <?= nl2br(htmlspecialchars($content['cta']['subtitle']['value'] ?? 'Every gift supports our mission to spread the Gospel and serve our community. Thank you for your generosity.')) ?>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-center gap-8">
                <a href="<?= APP_URL ?>/services" 
                   class="group relative px-12 py-6 bg-white text-brand-green font-bold rounded-2xl transition-all duration-500 hover:bg-brand-gold hover:text-brand-green overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.3)] tracking-widest uppercase text-xs">
                    <span class="relative z-10">OUR MINISTRIES</span>
                    <div class="absolute inset-0 bg-brand-gold translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                </a>
                <a href="<?= APP_URL ?>/about" 
                   class="px-12 py-6 bg-transparent border-2 border-white/30 text-white font-bold rounded-2xl hover:bg-white hover:text-brand-green transition-all duration-500 transform hover:scale-105 backdrop-blur-sm tracking-widest uppercase text-xs">
                    LEARN MORE
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'app/Views/layouts/footer.php'; ?>
