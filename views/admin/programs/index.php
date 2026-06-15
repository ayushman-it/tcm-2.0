<?php use TCM\Models\Program; ?>
<div class="tcm-page-head">
    <div><h2>Programs</h2><p>Live classes, learning tracks, internships and bundles.</p></div>
    <a class="tcm-btn primary" href="<?= base_url('/admin/programs/create') ?>"><i class="bi bi-plus-lg"></i> New Program</a>
</div>

<div class="tcm-card">
    <form method="get" style="margin-bottom:16px;max-width:320px;">
        <input class="tcm-input" name="q" placeholder="Search programs..." value="<?= e($_GET['q'] ?? '') ?>">
    </form>
    <table class="tcm-table">
        <thead><tr><th>Title</th><th>Type</th><th>Mode</th><th>Price</th><th>Seats</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($programs as $p): ?>
            <tr>
                <td><i class="bi <?= e($p['icon'] ?? 'bi-stack') ?>" style="color:var(--tcm-primary-2);"></i> <?= e($p['title']) ?></td>
                <td class="muted"><?= e(Program::TYPES[$p['type']] ?? $p['type']) ?></td>
                <td class="muted"><?= e(ucfirst($p['mode'])) ?></td>
                <td><?= (float)$p['price'] > 0 ? money($p['price']) : '<span class="tcm-badge green">Free</span>' ?></td>
                <td><?= (int)$p['seats_left'] ?> / <?= (int)$p['total_seats'] ?></td>
                <td>
                    <?php $cls = $p['status'] === 'published' ? 'green' : ($p['status'] === 'draft' ? 'amber' : 'gray'); ?>
                    <span class="tcm-badge <?= $cls ?>"><?= e($p['status']) ?></span>
                </td>
                <td>
                    <div class="d-flex gap-8">
                        <a class="tcm-btn sm" href="<?= base_url('/admin/programs/' . $p['id'] . '/edit') ?>"><i class="bi bi-pencil"></i></a>
                        <form method="post" action="<?= base_url('/admin/programs/' . $p['id'] . '/delete') ?>" onsubmit="return confirm('Delete this program?');">
                            <?= csrf_field() ?>
                            <button class="tcm-btn sm danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if ($programs === []): ?><tr><td colspan="7" class="muted">No programs yet.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
