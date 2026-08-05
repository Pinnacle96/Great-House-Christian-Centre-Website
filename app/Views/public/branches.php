<?php require_once 'app/Views/layouts/header.php'; ?>

<section class="bg-brand-green text-white py-24 md:py-32">
    <div class="container mx-auto px-6 md:px-12 lg:px-16">
        <div class="max-w-3xl">
            <span class="inline-block px-4 py-1.5 bg-white/10 border border-white/20 rounded-full text-xs font-bold uppercase tracking-wider mb-6">Our Centres</span>
            <h1 class="font-heading font-bold text-5xl md:text-7xl leading-tight mb-6">Find a GHCC centre near you</h1>
            <p class="text-white/80 text-lg md:text-xl leading-relaxed">Every centre is a fulfilment place, serving its city with pastoral care, worship, discipleship, and community.</p>
        </div>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6 md:px-12 lg:px-16">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <?php foreach ($branches as $branch): ?>
                <article class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 flex flex-col">
                    <div class="mb-5">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($branch['name']) ?></h2>
                        <p class="text-sm text-gray-500 leading-relaxed"><?= htmlspecialchars($branch['address'] ?? '') ?></p>
                    </div>

                    <div class="space-y-3 text-sm text-gray-700 flex-1">
                        <p><span class="font-bold text-gray-900">Pastor:</span> <?= htmlspecialchars($branch['display_pastor_name'] ?: 'To be assigned') ?></p>
                        <p><span class="font-bold text-gray-900">Phone:</span> <?= htmlspecialchars($branch['phone'] ?? '') ?></p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mt-6">
                        <a href="<?= APP_URL ?>/branches/<?= htmlspecialchars($branch['slug']) ?>" class="text-center bg-brand-green text-white px-4 py-3 rounded-lg font-bold hover:bg-brand-green-dark transition">View Centre</a>
                        <a href="<?= APP_URL ?>/branches/<?= htmlspecialchars($branch['slug']) ?>/give" class="text-center border border-brand-green text-brand-green px-4 py-3 rounded-lg font-bold hover:bg-brand-green hover:text-white transition">Give</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once 'app/Views/layouts/footer.php'; ?>
