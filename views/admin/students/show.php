<div class="tcm-page-head">
    <div><h2><?= e($student['name']) ?></h2><p><?= e($student['email']) ?></p></div>
    <div class="d-flex gap-8">
        <a class="tcm-btn" href="<?= base_url('/admin/students') ?>"><i class="bi bi-arrow-left"></i> Back</a>
        <form method="post" action="<?= base_url('/admin/students/' . $student['id'] . '/toggle') ?>">
            <?= csrf_field() ?>
            <button class="tcm-btn <?= $student['status'] === 'active' ? 'danger' : 'primary' ?>">
                <?= $student['status'] === 'active' ? 'Suspend' : 'Activate' ?>
            </button>
        </form>
    </div>
</div>

<div class="tcm-grid-2" style="align-items:start;">
    <div class="tcm-card">
        <h3 class="mt-0">Profile</h3>
        <p><strong>Headline:</strong> <?= e($profile['headline'] ?? '—') ?></p>
        <p><strong>College:</strong> <?= e($profile['college'] ?? '—') ?></p>
        <p><strong>Experience:</strong> <?= e($profile['experience_level'] ?? '—') ?></p>
        <p><strong>Goal:</strong> <?= e($profile['goal'] ?? '—') ?></p>
        <p class="mb-0"><strong>Bio:</strong> <span class="muted"><?= e($profile['bio'] ?? '—') ?></span></p>
    </div>
    <div class="tcm-card">
        <h3 class="mt-0">Enrolled courses (<?= count($enrollments) ?>)</h3>
        <?php foreach ($enrollments as $en): ?>
            <div class="flex-between" style="padding:8px 0;border-bottom:1px solid var(--tcm-border);">
                <span><?= e($en['title']) ?></span>
                <span class="tcm-badge purple"><?= (int)$en['progress'] ?>%</span>
            </div>
        <?php endforeach; ?>
        <?php if ($enrollments === []): ?><p class="muted mb-0">No enrollments.</p><?php endif; ?>
    </div>
</div>

<div class="tcm-card" style="margin-top:18px;">
    <h3 class="mt-0">Orders</h3>
    <table class="tcm-table">
        <thead><tr><th>Order</th><th>Item</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
            <tr>
                <td><?= e($o['order_number']) ?></td>
                <td><?= e($o['item_title'] ?? $o['item_type']) ?></td>
                <td><?= money($o['amount']) ?></td>
                <td><span class="tcm-badge <?= $o['status'] === 'paid' ? 'green' : 'amber' ?>"><?= e($o['status']) ?></span></td>
                <td class="muted"><?= e(date('d M Y', strtotime($o['created_at']))) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if ($orders === []): ?><tr><td colspan="5" class="muted">No orders.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
