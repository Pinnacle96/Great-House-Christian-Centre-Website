<?php
if (empty($pagination) || !is_array($pagination)) {
    return;
}

$total = (int)($pagination['total'] ?? 0);
$page = (int)($pagination['page'] ?? 1);
$totalPages = (int)($pagination['total_pages'] ?? 1);
$perPage = (int)($pagination['per_page'] ?? 15);
$allowedPerPage = $pagination['allowed_per_page'] ?? [10, 15, 25, 50];
$label = $pagination['label'] ?? 'records';
$controlId = 'per-page-' . preg_replace('/[^a-z0-9_-]+/i', '-', strtolower($label));

$pageUrl = function ($targetPage) use ($perPage) {
    $query = $_GET;
    $query['p'] = max(1, (int)$targetPage);
    $query['per_page'] = $perPage;
    return '?' . http_build_query($query);
};

$windowStart = max(1, $page - 2);
$windowEnd = min($totalPages, $page + 2);
?>

<div class="border-t border-gray-200 bg-white px-4 py-4 sm:px-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="text-sm text-gray-600">
            Showing <span class="font-semibold text-gray-900"><?= (int)$pagination['from'] ?></span>
            to <span class="font-semibold text-gray-900"><?= (int)$pagination['to'] ?></span>
            of <span class="font-semibold text-gray-900"><?= $total ?></span>
            <?= htmlspecialchars($label) ?>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <form method="GET" class="flex items-center gap-2">
                <?php foreach ($_GET as $key => $value): ?>
                    <?php if (in_array($key, ['p', 'per_page'], true)) continue; ?>
                    <?php if (is_array($value)) continue; ?>
                    <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
                <?php endforeach; ?>
                <input type="hidden" name="p" value="1">
                <label for="<?= htmlspecialchars($controlId) ?>" class="text-sm text-gray-600">Rows</label>
                <select id="<?= htmlspecialchars($controlId) ?>" name="per_page" onchange="this.form.submit()" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-green-500 focus:ring-brand-green-500">
                    <?php foreach ($allowedPerPage as $option): ?>
                        <option value="<?= (int)$option ?>" <?= (int)$option === $perPage ? 'selected' : '' ?>>
                            <?= (int)$option ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <?php if ($totalPages > 1): ?>
                <nav class="flex items-center gap-1" aria-label="Pagination">
                    <a href="<?= $pageUrl($page - 1) ?>" class="inline-flex h-9 items-center rounded-md border border-gray-300 px-3 text-sm font-medium <?= $page <= 1 ? 'pointer-events-none text-gray-300' : 'text-gray-700 hover:bg-gray-50' ?>">
                        Previous
                    </a>

                    <?php if ($windowStart > 1): ?>
                        <a href="<?= $pageUrl(1) ?>" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">1</a>
                        <?php if ($windowStart > 2): ?>
                            <span class="px-2 text-gray-400">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $windowStart; $i <= $windowEnd; $i++): ?>
                        <a href="<?= $pageUrl($i) ?>" class="inline-flex h-9 w-9 items-center justify-center rounded-md border text-sm font-medium <?= $i === $page ? 'border-brand-green-600 bg-brand-green-600 text-white' : 'border-gray-300 text-gray-700 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($windowEnd < $totalPages): ?>
                        <?php if ($windowEnd < $totalPages - 1): ?>
                            <span class="px-2 text-gray-400">...</span>
                        <?php endif; ?>
                        <a href="<?= $pageUrl($totalPages) ?>" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50"><?= $totalPages ?></a>
                    <?php endif; ?>

                    <a href="<?= $pageUrl($page + 1) ?>" class="inline-flex h-9 items-center rounded-md border border-gray-300 px-3 text-sm font-medium <?= $page >= $totalPages ? 'pointer-events-none text-gray-300' : 'text-gray-700 hover:bg-gray-50' ?>">
                        Next
                    </a>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>
