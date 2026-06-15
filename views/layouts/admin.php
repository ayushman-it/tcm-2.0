<?php
use TCM\Core\Auth;
$current = TCM\Core\Request::path();
$admin = Auth::user();
$nav = [
    ['Overview', [
        ['/admin', 'bi-grid-1x2', 'Dashboard'],
    ]],
    ['Catalog', [
        ['/admin/courses', 'bi-journal-code', 'Courses'],
        ['/admin/categories', 'bi-collection', 'Categories'],
        ['/admin/programs', 'bi-stack', 'Programs'],
        ['/admin/events', 'bi-calendar-event', 'Events'],
    ]],
    ['Growth', [
        ['/admin/leads', 'bi-megaphone', 'Leads'],
        ['/admin/internships', 'bi-file-earmark-person', 'Internships'],
    ]],
    ['Content', [
        ['/admin/posts', 'bi-newspaper', 'Insights'],
        ['/admin/testimonials', 'bi-chat-quote', 'Testimonials'],
        ['/admin/messages', 'bi-envelope', 'Messages'],
    ]],
    ['People', [
        ['/admin/students', 'bi-people', 'Students'],
    ]],
    ['System', [
        ['/admin/settings', 'bi-gear', 'Settings'],
    ]],
];
function nav_active(string $href, string $current): string {
    if ($href === '/admin') {
        return $current === '/admin' ? 'active' : '';
    }
    return str_starts_with($current, $href) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Admin') ?> · TCM Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('/assets/dashboard.css') ?>">
</head>
<body>
<div class="tcm-shell">
    <aside class="tcm-sidebar" id="sidebar">
        <div class="tcm-brand"><i class="bi bi-code-slash"></i> TCM Admin</div>
        <nav>
            <?php foreach ($nav as [$label, $items]): ?>
                <div class="tcm-nav-label"><?= e($label) ?></div>
                <ul class="tcm-nav">
                    <?php foreach ($items as [$href, $icon, $text]): ?>
                        <li>
                            <a class="<?= nav_active($href, $current) ?>" href="<?= base_url($href) ?>">
                                <i class="bi <?= e($icon) ?>"></i> <?= e($text) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
        </nav>
    </aside>

    <div class="tcm-main">
        <div class="tcm-topbar">
            <div class="d-flex items-center gap-8">
                <button class="tcm-btn sm tcm-menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">
                    <i class="bi bi-list"></i>
                </button>
                <h1><?= e($title ?? 'Admin') ?></h1>
            </div>
            <div class="tcm-user">
                <span class="muted" style="font-size:.85rem;"><?= e($admin['name'] ?? 'Admin') ?></span>
                <span class="tcm-avatar"><?= e(strtoupper(substr($admin['name'] ?? 'A', 0, 1))) ?></span>
                <form method="post" action="<?= base_url('/auth/logout') ?>">
                    <?= csrf_field() ?>
                    <button class="tcm-btn sm" title="Sign out"><i class="bi bi-box-arrow-right"></i></button>
                </form>
            </div>
        </div>
        <div class="tcm-content">
            <?php require dirname(__DIR__) . '/partials/flash.php'; ?>
            <?= $content ?? '' ?>
        </div>
    </div>
</div>
</body>
</html>
