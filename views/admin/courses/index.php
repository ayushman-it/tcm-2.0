<div class="tcm-page-head">
    <div>
        <h2>Courses</h2>
        <p>Manage your course catalog, pricing and curriculum.</p>
    </div>
    <a class="tcm-btn primary" href="<?= base_url('/admin/courses/create') ?>"><i class="bi bi-plus-lg"></i> New Course</a>
</div>

<div class="tcm-card">
    <form method="get" style="margin-bottom:16px;max-width:320px;">
        <input class="tcm-input" name="q" placeholder="Search courses..." value="<?= e($_GET['q'] ?? '') ?>">
    </form>
    <table class="tcm-table">
        <thead>
            <tr><th>Title</th><th>Category</th><th>Price</th><th>Students</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
        <?php foreach ($courses as $c): ?>
            <tr>
                <td>
                    <div class="d-flex items-center gap-8">
                        <i class="bi <?= e($c['icon'] ?? 'bi-journal-code') ?>" style="color:var(--tcm-primary-2);"></i>
                        <div>
                            <div><?= e($c['title']) ?></div>
                            <div class="muted" style="font-size:.78rem;"><?= e($c['subtitle'] ?? '') ?></div>
                        </div>
                    </div>
                </td>
                <td class="muted"><?= e($c['category_name'] ?? '—') ?></td>
                <td><?= money($c['price']) ?></td>
                <td><?= number_format($c['students_count']) ?></td>
                <td>
                    <?php $cls = $c['status'] === 'published' ? 'green' : ($c['status'] === 'draft' ? 'amber' : 'gray'); ?>
                    <span class="tcm-badge <?= $cls ?>"><?= e($c['status']) ?></span>
                </td>
                <td>
                    <div class="d-flex gap-8">
                        <a class="tcm-btn sm" href="<?= base_url('/admin/courses/' . $c['id'] . '/edit') ?>"><i class="bi bi-pencil"></i></a>
                        <form method="post" action="<?= base_url('/admin/courses/' . $c['id'] . '/delete') ?>"
                              onsubmit="return confirm('Delete this course? This cannot be undone.');">
                            <?= csrf_field() ?>
                            <button class="tcm-btn sm danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if ($courses === []): ?>
            <tr><td colspan="6" class="muted">No courses found. Create your first course.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
