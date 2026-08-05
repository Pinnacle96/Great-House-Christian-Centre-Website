<?php require_once 'app/Views/layouts/header.php'; ?>

<section class="bg-brand-green text-white py-24 md:py-32">
    <div class="container mx-auto px-6 md:px-12 lg:px-16">
        <div class="max-w-3xl">
            <span class="inline-block px-4 py-1.5 bg-white/10 border border-white/20 rounded-full text-xs font-bold uppercase tracking-wider mb-6">Events</span>
            <h1 class="font-heading font-bold text-5xl md:text-7xl leading-tight mb-6"><?= htmlspecialchars($branch['name']) ?> Events</h1>
            <p class="text-white/80 text-lg md:text-xl leading-relaxed">Programmes and gatherings for this centre.</p>
        </div>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6 md:px-12 lg:px-16">
        <?php if (empty($events)): ?>
            <div class="bg-white rounded-xl border border-gray-100 p-12 text-center text-gray-500">No upcoming events for this centre yet.</div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php foreach ($events as $event): ?>
                    <article class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <p class="text-xs font-bold uppercase tracking-wider text-brand-green mb-3"><?= date('M j, Y', strtotime($event['start_datetime'])) ?></p>
                        <h2 class="text-2xl font-bold text-gray-900 mb-3"><?= htmlspecialchars($event['title']) ?></h2>
                        <p class="text-sm text-gray-500 mb-5"><?= htmlspecialchars($event['location'] ?? $branch['address']) ?></p>
                        <a href="<?= APP_URL ?>/branches/<?= htmlspecialchars($branch['slug']) ?>/events/<?= htmlspecialchars($event['slug']) ?>" class="font-bold text-brand-green hover:underline">View event</a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'app/Views/layouts/footer.php'; ?>
