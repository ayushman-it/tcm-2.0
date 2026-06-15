<div class="tcm-page-head">
    <div><h2>Insights</h2><p>Blog posts and articles for the public site.</p></div>
    <a class="tcm-btn primary" href="<?= base_url('/admin/posts/create') ?>"><i class="bi bi-plus-lg"></i> New Insight</a>
</div>

<div class="tcm-card">
    <table class="tcm-table">
        <thead><tr><th>Title</th><th>Category</th><th>Status</th><th>Published</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($posts as $p): ?>
            <tr>
                <td><?= e($p['title']) ?></td>
                <td class="muted"><?= e($p['category']) ?></td>
                <td><span class="tcm-badge <?= $p['status'] === 'published' ? 'green' : 'amber' ?>"><?= e($p['status']) ?></span></td>
                <td class="muted"><?= $p['published_at'] ? e(date('d M Y', strtotime($p['published_at']))) : '—' ?></td>
                <td>
                    <div class="d-flex gap-8">
                        <a class="tcm-btn sm" href="<?= base_url('/admin/posts/' . $p['id'] . '/edit') ?>"><i class="bi bi-pencil"></i></a>
                        <form method="post" action="<?= base_url('/admin/posts/' . $p['id'] . '/delete') ?>" onsubmit="return confirm('Delete this insight?');">
                            <?= csrf_field() ?>
                            <button class="tcm-btn sm danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if ($posts === []): ?><tr><td colspan="5" class="muted">No insights yet.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
