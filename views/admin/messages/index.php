<div class="tcm-page-head">
    <div><h2>Messages</h2><p>Enquiries submitted through the contact form.</p></div>
</div>

<div class="tcm-card">
    <table class="tcm-table">
        <thead><tr><th>From</th><th>Subject</th><th>Message</th><th>Received</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($messages as $m): ?>
            <tr>
                <td><strong><?= e($m['name']) ?></strong><br><span class="muted" style="font-size:.8rem;"><?= e($m['email']) ?></span></td>
                <td><?= e($m['subject'] ?? '—') ?></td>
                <td class="muted" style="max-width:360px;"><?= e($m['message']) ?></td>
                <td class="muted"><?= e(date('d M Y', strtotime($m['created_at']))) ?></td>
                <td>
                    <form method="post" action="<?= base_url('/admin/messages/' . $m['id'] . '/delete') ?>">
                        <?= csrf_field() ?>
                        <button class="tcm-btn sm danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if ($messages === []): ?><tr><td colspan="5" class="muted">No messages.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
