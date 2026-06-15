<?php
$isEdit = $event !== null;
$action = $isEdit ? base_url('/admin/events/' . $event['id']) : base_url('/admin/events');
$val = static fn (string $k, $d = '') => e((string) ($event[$k] ?? $d));
?>
<div class="tcm-page-head">
    <div><h2><?= $isEdit ? 'Edit Event' : 'New Event' ?></h2></div>
    <a class="tcm-btn" href="<?= base_url('/admin/events') ?>"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<form method="post" action="<?= $action ?>">
    <?= csrf_field() ?>
    <div class="tcm-card">
        <div class="tcm-field"><label>Title</label><input class="tcm-input" name="title" value="<?= $val('title') ?>" required></div>
        <div class="tcm-field"><label>Description</label><textarea class="tcm-textarea" name="description"><?= $val('description') ?></textarea></div>
        <div class="tcm-grid-3">
            <div class="tcm-field">
                <label>Category</label>
                <select class="tcm-select" name="category">
                    <?php foreach (['frontend','backend','python','dsa','career','general'] as $c): ?>
                        <option value="<?= $c ?>" <?= ($event['category'] ?? '') === $c ? 'selected' : '' ?>><?= ucfirst($c) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="tcm-field">
                <label>Type</label>
                <select class="tcm-select" name="type" id="evtype">
                    <option value="free" <?= ($event['type'] ?? '') === 'free' ? 'selected' : '' ?>>Free</option>
                    <option value="paid" <?= ($event['type'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
                </select>
            </div>
            <div class="tcm-field"><label>Price (₹)</label><input class="tcm-input" type="number" step="0.01" name="price" value="<?= $val('price', '0') ?>"></div>
        </div>
        <div class="tcm-grid-3">
            <div class="tcm-field"><label>Date</label><input class="tcm-input" type="date" name="event_date" value="<?= $val('event_date') ?>"></div>
            <div class="tcm-field"><label>Time</label><input class="tcm-input" type="time" name="event_time" value="<?= $val('event_time') ?>"></div>
            <div class="tcm-field">
                <label>Status</label>
                <select class="tcm-select" name="status">
                    <?php foreach (['upcoming','ongoing','past'] as $st): ?>
                        <option value="<?= $st ?>" <?= ($event['status'] ?? 'upcoming') === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="tcm-grid-3">
            <div class="tcm-field">
                <label>Mode</label>
                <select class="tcm-select" name="mode">
                    <option value="online" <?= ($event['mode'] ?? '') === 'online' ? 'selected' : '' ?>>Online</option>
                    <option value="offline" <?= ($event['mode'] ?? '') === 'offline' ? 'selected' : '' ?>>Offline</option>
                </select>
            </div>
            <div class="tcm-field"><label>Total seats</label><input class="tcm-input" type="number" name="total_seats" value="<?= $val('total_seats', '16') ?>"></div>
            <div class="tcm-field"><label>Location</label><input class="tcm-input" name="location" value="<?= $val('location') ?>"></div>
        </div>
        <div class="tcm-field"><label>Recording URL (for past events)</label><input class="tcm-input" name="recording_url" value="<?= $val('recording_url') ?>"></div>
        <button class="tcm-btn primary"><i class="bi bi-check2"></i> <?= $isEdit ? 'Save Changes' : 'Create Event' ?></button>
    </div>
</form>

<?php if ($isEdit && !empty($registrations)): ?>
<div class="tcm-card" style="margin-top:18px;">
    <h3 class="mt-0">Registrations (<?= count($registrations) ?>)</h3>
    <table class="tcm-table">
        <thead><tr><th>Name</th><th>Email</th><th>Status</th><th>Registered</th></tr></thead>
        <tbody>
        <?php foreach ($registrations as $r): ?>
            <tr>
                <td><?= e($r['name']) ?></td>
                <td class="muted"><?= e($r['email']) ?></td>
                <td><span class="tcm-badge purple"><?= e($r['status']) ?></span></td>
                <td class="muted"><?= e(date('d M Y', strtotime($r['registered_at']))) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
