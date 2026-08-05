<?php require_once 'app/Views/layouts/header.php'; ?>

<section class="bg-brand-green text-white py-24 md:py-32">
    <div class="container mx-auto px-6 md:px-12 lg:px-16">
        <div class="max-w-4xl">
            <span class="inline-block px-4 py-1.5 bg-white/10 border border-white/20 rounded-full text-xs font-bold uppercase tracking-wider mb-6">GHCC Centre</span>
            <h1 class="font-heading font-bold text-5xl md:text-7xl leading-tight mb-6"><?= htmlspecialchars($branch['name']) ?></h1>
            <p class="text-white/80 text-lg md:text-xl leading-relaxed"><?= htmlspecialchars($branch['address'] ?? '') ?></p>
        </div>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6 md:px-12 lg:px-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <aside class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 h-fit">
                <h2 class="text-xl font-bold text-gray-900 mb-5">Centre Details</h2>
                <div class="space-y-4 text-sm text-gray-700">
                    <p><span class="block text-xs uppercase font-bold text-gray-400 mb-1">Pastor / Contact</span><?= htmlspecialchars(($branch['pastor_name'] ?? '') ?: 'To be assigned') ?></p>
                    <p><span class="block text-xs uppercase font-bold text-gray-400 mb-1">Phone</span><?= htmlspecialchars($branch['phone'] ?? '') ?></p>
                    <p><span class="block text-xs uppercase font-bold text-gray-400 mb-1">Email</span><?= htmlspecialchars($branch['email'] ?? '') ?></p>
                </div>
                <div class="grid grid-cols-1 gap-3 mt-8">
                    <a href="<?= APP_URL ?>/branches/<?= htmlspecialchars($branch['slug']) ?>/give" class="text-center bg-brand-green text-white px-5 py-3 rounded-lg font-bold hover:bg-brand-green-dark transition">Give to this Centre</a>
                    <a href="<?= APP_URL ?>/branches/<?= htmlspecialchars($branch['slug']) ?>/events" class="text-center border border-brand-green text-brand-green px-5 py-3 rounded-lg font-bold hover:bg-brand-green hover:text-white transition">View Events</a>
                </div>
            </aside>

            <div class="lg:col-span-2">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Upcoming Events</h2>
                    <a href="<?= APP_URL ?>/branches/<?= htmlspecialchars($branch['slug']) ?>/events" class="text-brand-green font-bold hover:underline">See all</a>
                </div>

                <?php if (empty($events)): ?>
                    <div class="bg-white rounded-xl border border-gray-100 p-10 text-center text-gray-500">No upcoming events for this centre yet.</div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php foreach ($events as $event): ?>
                            <article class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                                <p class="text-xs font-bold uppercase tracking-wider text-brand-green mb-3"><?= date('M j, Y', strtotime($event['start_datetime'])) ?></p>
                                <h3 class="text-xl font-bold text-gray-900 mb-3"><?= htmlspecialchars($event['title']) ?></h3>
                                <p class="text-sm text-gray-500 mb-5"><?= htmlspecialchars($event['location'] ?? $branch['address']) ?></p>
                                <a href="<?= APP_URL ?>/branches/<?= htmlspecialchars($branch['slug']) ?>/events/<?= htmlspecialchars($event['slug']) ?>" class="font-bold text-brand-green hover:underline">View event</a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'app/Views/layouts/footer.php'; ?>
