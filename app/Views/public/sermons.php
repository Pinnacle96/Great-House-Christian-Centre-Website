<?php
require_once 'app/Views/layouts/header.php';

if (!function_exists('ghcc_media_url')) {
    function ghcc_media_url($path) {
        if (!$path) {
            return '';
        }

        return preg_match('/^https?:\/\//i', $path)
            ? $path
            : APP_URL . '/' . ltrim($path, '/');
    }
}

if (!function_exists('ghcc_youtube_embed_url')) {
    function ghcc_youtube_embed_url($url) {
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

if (!function_exists('ghcc_sermon_media')) {
    function ghcc_sermon_media($sermon, $featured = false) {
        $videoUrl = $sermon['video_url'] ?? '';
        $audioUrl = ghcc_media_url($sermon['audio_url'] ?? '');
        $embedUrl = ghcc_youtube_embed_url($videoUrl);
        $thumbnail = !empty($sermon['thumbnail']) ? APP_URL . '/' . ltrim($sermon['thumbnail'], '/') : '';
        $heightClass = $featured ? 'min-h-[420px] md:min-h-[560px]' : 'h-64';

        ob_start();
        ?>
        <div class="relative overflow-hidden rounded-2xl bg-brand-green <?= $heightClass ?>">
            <?php if ($embedUrl): ?>
                <iframe src="<?= htmlspecialchars($embedUrl) ?>" title="<?= htmlspecialchars($sermon['title']) ?>" class="absolute inset-0 h-full w-full" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
            <?php else: ?>
                <?php if ($thumbnail): ?>
                    <img src="<?= htmlspecialchars($thumbnail) ?>" alt="<?= htmlspecialchars($sermon['title']) ?>" class="absolute inset-0 h-full w-full object-cover">
                    <div class="absolute inset-0 bg-black/45"></div>
                <?php else: ?>
                    <div class="absolute inset-0 bg-gradient-to-br from-brand-green to-brand-green-dark"></div>
                <?php endif; ?>

                <div class="relative z-10 flex h-full items-center justify-center p-6 text-center text-white">
                    <?php if ($audioUrl): ?>
                        <div class="w-full max-w-xl">
                            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full border border-white/20 bg-white/10 backdrop-blur">
                                <i class="fas fa-headphones text-3xl"></i>
                            </div>
                            <p class="mb-5 text-xs font-bold uppercase tracking-[0.25em] text-white/80">Audio Message</p>
                            <audio controls preload="metadata" class="w-full">
                                <source src="<?= htmlspecialchars($audioUrl) ?>">
                            </audio>
                        </div>
                    <?php else: ?>
                        <div>
                            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full border border-white/20 bg-white/10 backdrop-blur">
                                <i class="fas fa-microphone-alt text-3xl"></i>
                            </div>
                            <p class="text-xs font-bold uppercase tracking-[0.25em] text-white/80">Message Details</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}

if (!function_exists('ghcc_sermon_actions')) {
    function ghcc_sermon_actions($sermon, $large = false) {
        $videoUrl = $sermon['video_url'] ?? '';
        $audioUrl = ghcc_media_url($sermon['audio_url'] ?? '');
        $buttonClass = $large
            ? 'px-8 py-4 rounded-xl text-xs'
            : 'px-5 py-3 rounded-lg text-[10px]';

        ob_start();
        ?>
        <div class="flex flex-wrap gap-3">
            <?php if ($videoUrl): ?>
                <a href="<?= htmlspecialchars($videoUrl) ?>" target="_blank" rel="noopener" class="<?= $buttonClass ?> bg-brand-green text-white font-bold uppercase tracking-widest hover:bg-brand-green-dark transition">
                    Watch Video
                </a>
                <a href="<?= htmlspecialchars($videoUrl) ?>" target="_blank" rel="noopener" class="<?= $buttonClass ?> border border-brand-green text-brand-green font-bold uppercase tracking-widest hover:bg-brand-green hover:text-white transition">
                    Download / YouTube
                </a>
            <?php endif; ?>

            <?php if ($audioUrl): ?>
                <a href="<?= htmlspecialchars($audioUrl) ?>" target="_blank" class="<?= $buttonClass ?> border border-gray-200 text-gray-700 font-bold uppercase tracking-widest hover:border-brand-green hover:text-brand-green transition">
                    Play Audio
                </a>
                <a href="<?= htmlspecialchars($audioUrl) ?>" download class="<?= $buttonClass ?> bg-gray-900 text-white font-bold uppercase tracking-widest hover:bg-black transition">
                    Download Audio
                </a>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
?>

<section class="relative min-h-[60vh] flex items-center justify-center overflow-hidden bg-brand-green">
    <div class="absolute inset-0 bg-gradient-to-br from-brand-green via-brand-green to-brand-green-dark"></div>
    <div class="absolute inset-0 opacity-[0.06]" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>

    <div class="relative z-10 container mx-auto px-6 text-center text-white">
        <span class="inline-block px-5 py-2 bg-white/10 backdrop-blur-md rounded-full text-[10px] font-bold mb-8 border border-white/20 tracking-[0.3em] uppercase">
            <?= htmlspecialchars($content['hero']['badge']['value'] ?? 'SERMONS & TEACHINGS') ?>
        </span>
        <h1 class="font-heading font-bold text-5xl md:text-7xl mb-8 leading-tight tracking-tight">
            <?= htmlspecialchars($content['hero']['title']['value'] ?? 'Watch & Listen') ?>
        </h1>
        <p class="text-xl md:text-2xl max-w-2xl mx-auto text-white/80 font-light leading-relaxed">
            <?= htmlspecialchars($content['hero']['subtitle']['value'] ?? 'Experience our services live or catch up on past messages.') ?>
        </p>
    </div>
</section>

<section class="py-24 bg-white">
    <div class="container mx-auto px-6 md:px-12 lg:px-16">
        <?php if (empty($sermons)): ?>
            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-12 text-center text-gray-500">
                No sermons found. Please check back later.
            </div>
        <?php else: ?>
            <?php $featuredSermon = $sermons[0]; ?>
            <div class="mb-24">
                <div class="text-center mb-12">
                    <span class="inline-block px-4 py-1.5 bg-brand-green/10 text-brand-green rounded-full text-[11px] font-bold mb-6 tracking-[0.2em] uppercase">
                        <?= htmlspecialchars($content['featured']['badge']['value'] ?? 'FEATURED MESSAGE') ?>
                    </span>
                    <h2 class="font-heading font-bold text-4xl md:text-6xl text-gray-900 mb-4 leading-tight">
                        <?= htmlspecialchars($featuredSermon['title']) ?>
                    </h2>
                    <p class="text-gray-500 font-medium">
                        <?= htmlspecialchars($featuredSermon['preacher']) ?> · <?= date('F j, Y', strtotime($featuredSermon['date_preached'])) ?>
                    </p>
                </div>

                <div class="bg-white rounded-[2rem] p-5 md:p-8 border border-gray-100 shadow-[0_30px_60px_-15px_rgba(0,0,0,0.1)]">
                    <?= ghcc_sermon_media($featuredSermon, true) ?>

                    <div class="mt-10 max-w-4xl mx-auto">
                        <p class="text-gray-600 mb-8 text-lg font-light leading-relaxed text-center">
                            <?= htmlspecialchars($featuredSermon['description'] ?: 'Listen to this message from Great House Christian Center.') ?>
                        </p>
                        <div class="flex justify-center">
                            <?= ghcc_sermon_actions($featuredSermon, true) ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php $gridSermons = array_slice($sermons, 1); ?>
            <?php if (!empty($gridSermons)): ?>
                <div class="text-center mb-16">
                    <span class="inline-block px-4 py-1.5 bg-brand-green/10 text-brand-green rounded-full text-[11px] font-bold mb-6 tracking-[0.2em] uppercase">
                        <?= htmlspecialchars($content['sermon_grid']['badge']['value'] ?? 'RECENT MESSAGES') ?>
                    </span>
                    <h2 class="font-heading font-bold text-4xl md:text-6xl text-gray-900 mb-6 leading-tight">
                        <?= htmlspecialchars($content['sermon_grid']['title']['value'] ?? 'Browse More Sermons') ?>
                    </h2>
                    <p class="text-gray-600 max-w-3xl mx-auto font-light leading-relaxed">
                        <?= htmlspecialchars($content['sermon_grid']['subtitle']['value'] ?? 'Browse our collection of recent teachings and messages.') ?>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($gridSermons as $sermon): ?>
                        <article class="group bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col">
                            <?= ghcc_sermon_media($sermon) ?>

                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="text-2xl font-bold text-gray-900 mb-3 group-hover:text-brand-green transition-colors">
                                    <?= htmlspecialchars($sermon['title']) ?>
                                </h3>
                                <p class="text-sm font-bold uppercase tracking-wider text-brand-green mb-4">
                                    <?= htmlspecialchars($sermon['preacher']) ?>
                                </p>
                                <p class="text-gray-600 mb-6 text-sm leading-relaxed flex-grow">
                                    <?= htmlspecialchars($sermon['description'] ?: 'Message from Great House Christian Center.') ?>
                                </p>
                                <div class="flex items-center text-gray-500 text-xs mb-6">
                                    <i class="fas fa-calendar-alt text-brand-green mr-3"></i>
                                    <span><?= date('F j, Y', strtotime($sermon['date_preached'])) ?></span>
                                </div>
                                <?= ghcc_sermon_actions($sermon) ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<section class="py-32 bg-brand-green relative overflow-hidden">
    <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
    <div class="container mx-auto px-6 md:px-12 lg:px-16 text-center relative z-10">
        <div class="max-w-4xl mx-auto">
            <span class="inline-block px-5 py-2 bg-white/10 text-white rounded-full text-[10px] font-bold mb-8 tracking-[0.4em] uppercase backdrop-blur-sm border border-white/20">
                <?= htmlspecialchars($content['cta']['badge']['value'] ?? 'STAY CONNECTED') ?>
            </span>
            <h2 class="font-heading font-bold text-5xl md:text-7xl text-white mb-8 leading-tight">
                <?= htmlspecialchars($content['cta']['title']['value'] ?? 'Never Miss a Message') ?>
            </h2>
            <p class="text-xl text-white/80 mb-12 font-light leading-relaxed max-w-2xl mx-auto">
                <?= htmlspecialchars($content['cta']['subtitle']['value'] ?? 'Subscribe to sermon updates and join us for services.') ?>
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-5">
                <a href="<?= APP_URL ?>/contact" class="px-10 py-5 bg-white text-brand-green font-bold rounded-xl hover:bg-brand-gold transition uppercase tracking-widest text-xs">
                    Subscribe Now
                </a>
                <a href="<?= APP_URL ?>/services" class="px-10 py-5 border border-white/30 text-white font-bold rounded-xl hover:bg-white hover:text-brand-green transition uppercase tracking-widest text-xs">
                    Service Times
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'app/Views/layouts/footer.php'; ?>
