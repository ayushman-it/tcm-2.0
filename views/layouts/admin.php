<?php
use TCM\Core\Auth;
$current = TCM\Core\Request::path();
$admin   = Auth::user();
$nav = [
    ['Overview',  [['/admin',              'bi-squares-fill',         'Dashboard']]],
    ['Catalog',   [['/admin/courses',      'bi-journal-code',         'Courses'],
                   ['/admin/categories',   'bi-collection',           'Categories'],
                   ['/admin/programs',     'bi-stack',                'Programs'],
                   ['/admin/events',       'bi-calendar-event',       'Events']]],
    ['Growth',    [['/admin/leads',        'bi-megaphone',            'Leads'],
                   ['/admin/internships',  'bi-file-earmark-person',  'Internships']]],
    ['Content',   [['/admin/posts',        'bi-newspaper',            'Blog Posts'],
                   ['/admin/testimonials', 'bi-chat-quote',           'Testimonials'],
                   ['/admin/messages',     'bi-envelope',             'Messages']]],
    ['People',    [['/admin/students',     'bi-people',               'Students']]],
    ['System',    [['/admin/settings',     'bi-gear',                 'Settings']]],
];
function nav_active(string $href, string $current): string {
    if ($href === '/admin') return $current === '/admin' ? 'active' : '';
    return str_starts_with($current, $href) ? 'active' : '';
}
$fl   = strtoupper(substr($admin['name'] ?? 'A', 0, 1));
$name = $admin['name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Admin') ?> · TCM Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('/assets/dashboard.css') ?>">
    <style>
    /* ── Admin overrides ──────────────────────────────────── */
    .adm-breadcrumb { font-size:.7rem; color:var(--muted); margin-top:1px; }
    .adm-breadcrumb a { color:var(--muted); text-decoration:none; }
    .adm-breadcrumb a:hover { color:#111; }
    .adm-breadcrumb .sep { margin:0 4px; font-size:.6rem; }

    /* Quick cards */
    .adm-quick-grid {
        display:grid;
        grid-template-columns:repeat(auto-fill,minmax(130px,1fr));
        gap:10px;
        margin-bottom:20px;
    }
    .adm-quick-card {
        display:flex; flex-direction:column; align-items:flex-start; gap:10px;
        background:#fff; border:1.5px solid #ececec; border-radius:12px;
        padding:14px 14px 12px; text-decoration:none;
        transition:border-color .15s, box-shadow .15s, transform .15s;
    }
    .adm-quick-card:hover {
        border-color:#ddd; box-shadow:0 4px 14px rgba(0,0,0,.06);
        transform:translateY(-2px); color:inherit;
    }
    .adm-quick-icon {
        width:32px; height:32px;
        background:#f5f5f5; border:1px solid #e5e5e5;
        border-radius:9px; display:grid; place-items:center;
        font-size:.9rem; color:#111;
    }
    .adm-quick-label { font-size:.82rem; font-weight:600; color:#111; }

    /* Stat action link */
    .adm-stat-link {
        font-size:.72rem; color:var(--muted); margin-top:6px;
        display:inline-flex; align-items:center; gap:4px;
        text-decoration:none;
    }
    .adm-stat-link:hover { color:#111; }
    .adm-stat-link.danger { color:#dc2626; font-weight:600; }

    /* Table scroll wrapper */
    .adm-table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }

    /* Section head */
    .adm-section-head {
        display:flex; align-items:center; justify-content:space-between;
        margin-bottom:16px; gap:10px;
    }
    .adm-section-head h3 {
        font-size:.92rem; font-weight:700; color:#111;
        display:flex; align-items:center; gap:8px; margin:0;
    }
    .adm-section-head h3 i { color:var(--muted); }

    /* Curriculum builder */
    .adm-module-card {
        background:#f9f9f9; border:1px solid #ececec;
        border-radius:12px; padding:16px 18px; margin-bottom:12px;
    }
    .adm-module-head {
        display:flex; align-items:center; justify-content:space-between;
        gap:10px; margin-bottom:12px;
    }
    .adm-module-title { font-weight:700; font-size:.9rem; color:#111; }
    .adm-module-meta  { font-size:.76rem; color:var(--muted); margin-top:1px; }
    .adm-lesson-row {
        display:flex; align-items:center; justify-content:space-between;
        gap:10px; padding:7px 0; border-bottom:1px solid #ececec;
        font-size:.84rem; color:#333;
    }
    .adm-lesson-row:last-child { border-bottom:none; }
    .adm-lesson-left { display:flex; align-items:center; gap:8px; flex:1; min-width:0; }
    .adm-lesson-title { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .adm-add-lesson-row {
        display:flex; gap:8px; flex-wrap:wrap; align-items:center;
        margin-top:12px; padding-top:12px; border-top:1px solid #ececec;
    }

    /* Form sections */
    .adm-form-section {
        background:#fff; border:1px solid #ececec;
        border-radius:14px; padding:22px 22px 18px;
        margin-bottom:16px;
    }
    .adm-form-section-title {
        font-size:.72rem; font-weight:700; text-transform:uppercase;
        letter-spacing:.08em; color:var(--muted); margin-bottom:14px;
        padding-bottom:10px; border-bottom:1px solid #f0f0f0;
        display:flex; align-items:center; gap:7px;
    }

    /* Responsive */
    @media (max-width:768px) {
        .adm-quick-grid { grid-template-columns:repeat(3,1fr); }
        .adm-section-head { flex-direction:column; align-items:flex-start; }
    }
    @media (max-width:420px) {
        .adm-quick-grid { grid-template-columns:repeat(2,1fr); }
    }
    </style>
</head>
<body>

<div class="tcm-sidebar-backdrop" id="sidebarBackdrop"></div>

<div class="tcm-shell">

    <!-- Sidebar -->
    <aside class="tcm-sidebar" id="sidebar">

        <div class="tcm-brand">
            <i class="bi bi-code-slash"></i> TCM Admin
        </div>

        <nav style="flex:1;overflow-y:auto;overflow-x:hidden;scrollbar-width:none;padding-bottom:8px;">
            <?php foreach ($nav as [$label, $items]): ?>
                <div class="tcm-nav-label"><?= e($label) ?></div>
                <ul class="tcm-nav">
                    <?php foreach ($items as [$href, $icon, $text]): ?>
                    <li>
                        <a class="<?= nav_active($href, $current) ?>"
                           href="<?= base_url($href) ?>"
                           onclick="closeSidebar()">
                            <i class="bi <?= e($icon) ?>"></i>
                            <?= e($text) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
        </nav>

        <div class="tcm-sidebar-footer">
            <div class="tcm-sidebar-user">
                <div class="tcm-avatar" style="width:30px;height:30px;font-size:.72rem;">
                    <?= e($fl) ?>
                </div>
                <div class="tcm-sidebar-user-info">
                    <div class="tcm-sidebar-user-name"><?= e($name) ?></div>
                    <div class="tcm-sidebar-user-role">Administrator</div>
                </div>
                <form method="post" action="<?= base_url('/auth/logout') ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="tcm-btn ghost sm" title="Sign out"
                            style="padding:5px 7px;">
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
                <button class="tcm-btn ghost sm tcm-menu-toggle"
                        onclick="openSidebar()"
                        style="padding:7px 9px;">
                    <i class="bi bi-list" style="font-size:1.1rem;"></i>
                </button>
                <div>
                    <h1><?= e($title ?? 'Dashboard') ?></h1>
                    <div class="adm-breadcrumb">
                        <a href="<?= base_url('/admin') ?>">Admin</a>
                        <?php if (($title ?? 'Dashboard') !== 'Dashboard'): ?>
                            <i class="bi bi-chevron-right sep"></i>
                            <span><?= e($title ?? '') ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="tcm-topbar-right">
                <a href="<?= base_url('/') ?>" target="_blank"
                   class="tcm-btn ghost sm" title="View website"
                   style="display:none;" id="websiteBtn">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <span style="font-size:.75rem;">Site</span>
                </a>
                <div class="tcm-avatar" style="font-size:.8rem;cursor:default;"
                     title="<?= e($name) ?>"><?= e($fl) ?></div>
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
document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeSidebar(); });
// Show website button on larger screens
if(window.innerWidth > 768) document.getElementById('websiteBtn').style.display='';
</script>
</body>
</html>
