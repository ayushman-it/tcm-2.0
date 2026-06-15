<?php
use TCM\Core\Auth;
$current = TCM\Core\Request::path();
$me = Auth::user();
$nav = [
    ['/student', 'bi-grid-1x2', 'Dashboard'],
    ['/student/courses', 'bi-journal-code', 'Courses'],
    ['/student/programs', 'bi-stack', 'Programs'],
    ['/student/events', 'bi-calendar-event', 'Events'],
    ['/student/applications', 'bi-file-earmark-text', 'Applications'],
    ['/student/portfolio', 'bi-briefcase', 'Portfolio'],
    ['/student/profile', 'bi-person-gear', 'Profile'],
];
function snav_active(string $href, string $current): string {
    if ($href === '/student') {
        return $current === '/student' ? 'active' : '';
    }
    return str_starts_with($current, $href) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Dashboard') ?> · The Code Munk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('/assets/dashboard.css') ?>">
</head>
<body>
<div class="tcm-shell">
    <aside class="tcm-sidebar" id="sidebar">
        <div class="tcm-brand"><i class="bi bi-code-slash"></i> The Code Munk</div>
        <ul class="tcm-nav" style="margin-top:18px;">
            <?php foreach ($nav as [$href, $icon, $text]): ?>
                <li>
                    <a class="<?= snav_active($href, $current) ?>" href="<?= base_url($href) ?>">
                        <i class="bi <?= e($icon) ?>"></i> <?= e($text) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="tcm-nav-label" style="margin-top:24px;">Quick link</div>
        <ul class="tcm-nav">
            <li>
                <a href="<?= base_url('/portfolio/' . ($me['id'] ?? 0)) ?>" target="_blank">
                    <i class="bi bi-box-arrow-up-right"></i> Public portfolio
                </a>
            </li>
        </ul>
    </aside>

    <div class="tcm-main">
        <div class="tcm-topbar">
            <div class="d-flex items-center gap-8">
                <button class="tcm-btn sm tcm-menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">
                    <i class="bi bi-list"></i>
                </button>
                <h1><?= e($title ?? 'Dashboard') ?></h1>
            </div>
            <div class="tcm-user">
                <span class="muted" style="font-size:.85rem;"><?= e($me['name'] ?? 'Student') ?></span>
                <span class="tcm-avatar"><?= e(strtoupper(substr($me['name'] ?? 'S', 0, 1))) ?></span>
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
