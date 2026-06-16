<?php $hour = (int) date('H');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
$adminName = explode(' ', $user['name'] ?? 'Admin')[0];
?>

<!-- Welcome banner -->
<div class="tcm-welcome" style="margin-bottom:20px;">
    <div class="flex-between flex-wrap gap-12">
        <div>
            <div class="tcm-welcome-tag">
                <span class="tcm-welcome-tag-dot"></span>
                <?= date('l, d M Y') ?>
            </div>
            <h2><?= e($greeting) ?>, <?= e($adminName) ?> 👋</h2>
            <p class="mb-0">Here's what's happening with The Code Munk today.</p>
        </div>
        <div class="d-flex gap-6 flex-wrap">
            <a href="<?= base_url('/admin/courses/create') ?>" class="tcm-btn primary sm">
                <i class="bi bi-plus-lg"></i> Add Course
            </a>
            <a href="<?= base_url('/admin/events/create') ?>" class="tcm-btn sm">
                <i class="bi bi-calendar-plus"></i> Add Event
            </a>
            <a href="<?= base_url('/admin/leads') ?>" class="tcm-btn sm">
                <i class="bi bi-megaphone"></i> Leads
            </a>
        </div>
    </div>
</div>

<!-- Stats grid -->
<div class="tcm-stat-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px;">

    <div class="tcm-stat">
        <i class="bi bi-people icon"></i>
        <div class="label">Total Students</div>
        <div class="value"><?= number_format($stats['students']) ?></div>
        <a href="<?= base_url('/admin/students') ?>"
           style="font-size:.72rem;color:var(--muted);margin-top:6px;display:inline-flex;align-items:center;gap:4px;text-decoration:none;">
            View all <i class="bi bi-arrow-right" style="font-size:.65rem;"></i>
        </a>
    </div>

    <div class="tcm-stat">
        <i class="bi bi-journal-code icon"></i>
        <div class="label">Courses</div>
        <div class="value"><?= number_format($stats['courses']) ?></div>
        <a href="<?= base_url('/admin/courses') ?>"
           style="font-size:.72rem;color:var(--muted);margin-top:6px;display:inline-flex;align-items:center;gap:4px;text-decoration:none;">
            Manage <i class="bi bi-arrow-right" style="font-size:.65rem;"></i>
        </a>
    </div>

    <div class="tcm-stat">
        <i class="bi bi-calendar-event icon"></i>
        <div class="label">Events</div>
        <div class="value"><?= number_format($stats['events']) ?></div>
        <a href="<?= base_url('/admin/events') ?>"
           style="font-size:.72rem;color:var(--muted);margin-top:6px;display:inline-flex;align-items:center;gap:4px;text-decoration:none;">
            Manage <i class="bi bi-arrow-right" style="font-size:.65rem;"></i>
        </a>
    </div>

    <div class="tcm-stat">
        <i class="bi bi-mortarboard icon"></i>
        <div class="label">Enrollments</div>
        <div class="value"><?= number_format($stats['enrollments']) ?></div>
    </div>

    <div class="tcm-stat">
        <i class="bi bi-currency-rupee icon"></i>
        <div class="label">Total Revenue</div>
        <div class="value" style="font-size:1.5rem;letter-spacing:-.5px;"><?= money($stats['revenue']) ?></div>
    </div>

    <div class="tcm-stat">
        <i class="bi bi-envelope icon"></i>
        <div class="label">New Messages</div>
        <div class="value"><?= number_format($stats['new_messages']) ?></div>
        <?php if ($stats['new_messages'] > 0): ?>
        <a href="<?= base_url('/admin/messages') ?>"
           style="font-size:.72rem;color:#dc2626;margin-top:6px;display:inline-flex;align-items:center;gap:4px;text-decoration:none;font-weight:600;">
            Reply now <i class="bi bi-arrow-right" style="font-size:.65rem;"></i>
        </a>
        <?php endif; ?>
    </div>

</div>

<!-- Quick nav cards -->
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;margin-bottom:20px;">
    <?php
    $quick = [
        ['/admin/leads',       'bi-megaphone',           'Leads',        'amber'],
        ['/admin/students',    'bi-people',              'Students',     'gray'],
        ['/admin/programs',    'bi-stack',               'Programs',     'gray'],
        ['/admin/internships', 'bi-file-earmark-person', 'Internships',  'gray'],
        ['/admin/posts',       'bi-newspaper',           'Blog Posts',   'gray'],
        ['/admin/settings',    'bi-gear',                'Settings',     'gray'],
    ];
    foreach ($quick as [$href, $icon, $label, $color]):
    ?>
    <a href="<?= base_url($href) ?>"
       style="display:flex;flex-direction:column;align-items:flex-start;gap:10px;
              background:#fff;border:1px solid #ececec;border-radius:12px;
              padding:14px 14px 12px;text-decoration:none;
              transition:border-color .15s,box-shadow .15s,transform .15s;">
        <div style="width:32px;height:32px;background:#f5f5f5;border:1px solid #e5e5e5;
                    border-radius:9px;display:grid;place-items:center;font-size:.9rem;color:#111;">
            <i class="bi <?= e($icon) ?>"></i>
        </div>
        <span style="font-size:.82rem;font-weight:600;color:#111;"><?= e($label) ?></span>
    </a>
    <?php endforeach; ?>
</div>

<!-- Tables row -->
<div class="tcm-grid-2" style="align-items:start;">

    <!-- Recent orders -->
    <div class="tcm-card">
        <div class="flex-between" style="margin-bottom:16px;">
            <h3><i class="bi bi-receipt" style="color:var(--muted);margin-right:8px;"></i>Recent Orders</h3>
            <a href="<?= base_url('/admin/students') ?>" class="tcm-btn ghost sm">
                All students <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <?php if ($recentOrders === []): ?>
            <div class="tcm-empty" style="padding:20px 0 8px;">
                <i class="bi bi-receipt"></i>
                No orders yet.
            </div>
        <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="tcm-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Student</th>
                        <th>Item</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($recentOrders as $o): ?>
                    <tr>
                        <td style="font-size:.78rem;font-family:monospace;color:var(--muted);">
                            <?= e(substr($o['order_number'], 0, 14)) ?>
                        </td>
                        <td style="font-weight:500;"><?= e($o['student_name']) ?></td>
                        <td style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            <?= e($o['item_title'] ?? ucfirst($o['item_type'])) ?>
                        </td>
                        <td style="font-weight:700;"><?= money($o['amount']) ?></td>
                        <td>
                            <span class="tcm-badge <?= $o['status'] === 'paid' ? 'green' : 'amber' ?>">
                                <?= e($o['status']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Recent students -->
    <div class="tcm-card">
        <div class="flex-between" style="margin-bottom:16px;">
            <h3><i class="bi bi-people" style="color:var(--muted);margin-right:8px;"></i>New Students</h3>
            <a href="<?= base_url('/admin/students') ?>" class="tcm-btn ghost sm">
                All <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <?php if ($recentStudents === []): ?>
            <div class="tcm-empty" style="padding:20px 0 8px;">
                <i class="bi bi-people"></i>
                No students yet.
            </div>
        <?php else: ?>
        <div class="tcm-row-list">
            <?php foreach ($recentStudents as $s): ?>
            <div class="tcm-row">
                <div class="tcm-row-main">
                    <div class="d-flex items-center gap-8">
                        <div class="tcm-avatar" style="width:30px;height:30px;font-size:.72rem;flex-shrink:0;">
                            <?= e(strtoupper(substr($s['name'], 0, 1))) ?>
                        </div>
                        <div>
                            <div style="font-size:.85rem;font-weight:600;color:#111;">
                                <a href="<?= base_url('/admin/students/' . $s['id']) ?>"
                                   style="color:#111;text-decoration:none;">
                                    <?= e($s['name']) ?>
                                </a>
                            </div>
                            <div style="font-size:.74rem;color:var(--muted);"><?= e($s['email']) ?></div>
                        </div>
                    </div>
                </div>
                <div class="tcm-row-meta">
                    <?= e(date('d M', strtotime($s['created_at']))) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

</div>
