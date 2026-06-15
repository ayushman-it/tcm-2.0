<div class="tcm-page-head">
    <div><h2>Students</h2><p>All registered learners on the platform.</p></div>
</div>

<div class="tcm-card">
    <form method="get" style="margin-bottom:16px;max-width:320px;">
        <input class="tcm-input" name="q" placeholder="Search by name or email..." value="<?= e($_GET['q'] ?? '') ?>">
    </form>
    <table class="tcm-table">
        <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Status</th><th>Joined</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($students as $s): ?>
            <tr>
                <td><?= e($s['name']) ?></td>
                <td class="muted"><?= e($s['email']) ?></td>
                <td class="muted"><?= e($s['phone'] ?? '—') ?></td>
                <td><span class="tcm-badge <?= $s['status'] === 'active' ? 'green' : 'red' ?>"><?= e($s['status']) ?></span></td>
                <td class="muted"><?= e(date('d M Y', strtotime($s['created_at']))) ?></td>
                <td><a class="tcm-btn sm" href="<?= base_url('/admin/students/' . $s['id']) ?>">View</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if ($students === []): ?><tr><td colspan="6" class="muted">No students found.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
