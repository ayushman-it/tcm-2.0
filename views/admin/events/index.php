<div class="tcm-page-head">
    <div><h2>Events</h2><p>Workshops, bootcamps and live sessions.</p></div>
    <a class="tcm-btn primary" href="<?= base_url('/admin/events/create') ?>"><i class="bi bi-plus-lg"></i> New Event</a>
</div>

<div class="tcm-card">
    <form method="get" style="margin-bottom:16px;max-width:320px;">
        <input class="tcm-input" name="q" placeholder="Search events..." value="<?= e($_GET['q'] ?? '') ?>">
    </form>
    <table class="tcm-table">
        <thead><tr><th>Title</th><th>Category</th><th>Type</th><th>Date</th><th>Seats</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($events as $ev): ?>
            <tr>
                <td><?= e($ev['title']) ?></td>
                <td class="muted"><?= e(ucfirst($ev['category'])) ?></td>
                <td><span class="tcm-badge <?= $ev['type'] === 'free' ? 'green' : 'purple' ?>"><?= e($ev['type']) ?></span></td>
                <td class="muted"><?= $ev['event_date'] ? e(date('d M Y', strtotime($ev['event_date']))) : '—' ?></td>
                <td><?= (int)$ev['seats_filled'] ?> / <?= (int)$ev['total_seats'] ?></td>
                <td>
                    <?php $cls = $ev['status'] === 'ongoing' ? 'green' : ($ev['status'] === 'upcoming' ? 'amber' : 'gray'); ?>
                    <span class="tcm-badge <?= $cls ?>"><?= e($ev['status']) ?></span>
                </td>
                <td>
                    <div class="d-flex gap-8">
                        <a class="tcm-btn sm" href="<?= base_url('/admin/events/' . $ev['id'] . '/edit') ?>"><i class="bi bi-pencil"></i></a>
                        <form method="post" action="<?= base_url('/admin/events/' . $ev['id'] . '/delete') ?>" onsubmit="return confirm('Delete this event?');">
                            <?= csrf_field() ?>
                            <button class="tcm-btn sm danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if ($events === []): ?>
            <tr><td colspan="7" class="muted">No events yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
