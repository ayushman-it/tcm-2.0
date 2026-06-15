<?php
use TCM\Core\Auth;
$current = TCM\Core\Request::path();
$me      = Auth::user();
$nav = [
    ['/student',              'bi-squares-fill',      'Dashboard'],
    ['/student/courses',      'bi-journal-code',      'My Courses'],
    ['/student/programs',     'bi-stack',             'Programs'],
    ['/student/events',       'bi-calendar-event',    'Events'],
    ['/student/applications', 'bi-file-earmark-text', 'Applications'],
    ['/student/portfolio',    'bi-briefcase',         'Portfolio'],
    ['/student/profile',      'bi-person-gear',       'Profile'],
];
function snav_active(string $href, string $current): string {
    if ($href === '/student') return $current === '/student' ? 'active' : '';
    return str_starts_with($current, $href) ? 'active' : '';
}
$fl = strtoupper(substr($me['name'] ?? 'S', 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="app-base" content="<?= base_url('') ?>">
    <title><?= e($title ?? 'Dashboard') ?> · The Code Munk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('/assets/dashboard.css') ?>">
</head>
<body>

<div class="tcm-sidebar-backdrop" id="sidebarBackdrop"></div>

<div class="tcm-shell">

    <!-- Sidebar -->
    <aside class="tcm-sidebar" id="sidebar">

        <div class="tcm-brand">
            <i class="bi bi-code-slash"></i> The Code Munk
        </div>

        <ul class="tcm-nav" style="flex:1;">
            <?php foreach ($nav as [$href, $icon, $label]): ?>
            <li>
                <a href="<?= base_url($href) ?>"
                   class="<?= snav_active($href, $current) ?>"
                   onclick="closeSidebar()">
                    <i class="bi <?= e($icon) ?>"></i>
                    <?= e($label) ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>

        <div class="tcm-nav-label">Quick links</div>
        <ul class="tcm-nav">
            <li>
                <a href="<?= base_url('/portfolio/' . ($me['id'] ?? 0)) ?>"
                   target="_blank" onclick="closeSidebar()">
                    <i class="bi bi-box-arrow-up-right"></i> Public Portfolio
                </a>
            </li>
            <li>
                <a href="<?= base_url('/') ?>" onclick="closeSidebar()">
                    <i class="bi bi-house"></i> Main Site
                </a>
            </li>
        </ul>

        <div class="tcm-sidebar-spacer"></div>

        <!-- User strip -->
        <div class="tcm-sidebar-footer">
            <div class="tcm-sidebar-user">
                <?php $avatarSrc = !empty($me['avatar']) ? base_url('/uploads/' . e($me['avatar'])) : null; ?>
                <div class="tcm-avatar" style="width:32px;height:32px;font-size:.75rem;">
                    <?php if ($avatarSrc): ?>
                        <img src="<?= $avatarSrc ?>" alt="">
                    <?php else: ?>
                        <?= e($fl) ?>
                    <?php endif; ?>
                </div>
                <div class="tcm-sidebar-user-info">
                    <div class="tcm-sidebar-user-name"><?= e($me['name'] ?? 'Student') ?></div>
                    <div class="tcm-sidebar-user-role">Student</div>
                </div>
                <form method="post" action="<?= base_url('/auth/logout') ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="tcm-btn ghost sm" title="Sign out"
                            style="padding:6px 8px;">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>

    </aside>

    <!-- Main -->
    <div class="tcm-main">

        <!-- Topbar -->
        <div class="tcm-topbar">
            <div class="tcm-topbar-left">
                <button id="menuToggle"
                        class="tcm-btn ghost sm tcm-menu-toggle"
                        onclick="openSidebar()"
                        aria-label="Open menu"
                        style="padding:7px 9px;">
                    <i class="bi bi-list" style="font-size:1.1rem;"></i>
                </button>
                <h1><?= e($title ?? 'Dashboard') ?></h1>
            </div>
            <div class="tcm-topbar-right">
                <span class="tcm-user-name"><?= e($me['name'] ?? '') ?></span>
                <?php
                $avatarSrc = !empty($me['avatar']) ? base_url('/uploads/' . e($me['avatar'])) : null;
                ?>
                <a href="<?= base_url('/student/profile') ?>" class="tcm-avatar" title="Profile">
                    <?php if ($avatarSrc): ?>
                        <img src="<?= $avatarSrc ?>" alt="<?= e($me['name'] ?? '') ?>">
                    <?php else: ?>
                        <?= e($fl) ?>
                    <?php endif; ?>
                </a>
                <form method="post" action="<?= base_url('/auth/logout') ?>" style="display:contents">
                    <?= csrf_field() ?>
                    <button type="submit" class="tcm-btn ghost sm" title="Sign out"
                            style="padding:7px 9px;">
                        <i class="bi bi-box-arrow-right" style="font-size:.9rem;"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Content -->
        <div class="tcm-content">
            <?php require dirname(__DIR__) . '/partials/flash.php'; ?>
            <?= $content ?? '' ?>
        </div>

    </div>

</div>

<script>
function openSidebar() {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('sidebarBackdrop').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarBackdrop').classList.remove('active');
    document.body.style.overflow = '';
}
document.getElementById('sidebarBackdrop').addEventListener('click', closeSidebar);
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });
</script>
</body>
</html>
