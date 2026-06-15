<?php
use TCM\Models\Program;
$isEdit = $program !== null;
$action = $isEdit ? base_url('/admin/programs/' . $program['id']) : base_url('/admin/programs');
$val = static fn (string $k, $d = '') => e((string) ($program[$k] ?? $d));
?>
<div class="tcm-page-head">
    <div><h2><?= $isEdit ? 'Edit Program' : 'New Program' ?></h2></div>
    <a class="tcm-btn" href="<?= base_url('/admin/programs') ?>"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<form method="post" action="<?= $action ?>">
    <?= csrf_field() ?>
    <div class="tcm-card">
        <div class="tcm-field"><label>Title</label><input class="tcm-input" name="title" value="<?= $val('title') ?>" required></div>
        <div class="tcm-field"><label>Subtitle</label><input class="tcm-input" name="subtitle" value="<?= $val('subtitle') ?>"></div>
        <div class="tcm-field"><label>Description</label><textarea class="tcm-textarea" name="description"><?= $val('description') ?></textarea></div>
        <div class="tcm-grid-3">
            <div class="tcm-field">
                <label>Type</label>
                <select class="tcm-select" name="type">
                    <?php foreach (Program::TYPES as $k => $lbl): ?>
                        <option value="<?= $k ?>" <?= ($program['type'] ?? '') === $k ? 'selected' : '' ?>><?= e($lbl) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="tcm-field"><label>Icon</label><input class="tcm-input" name="icon" value="<?= $val('icon', 'bi-stack') ?>"></div>
            <div class="tcm-field">
                <label>Mode</label>
                <select class="tcm-select" name="mode">
                    <?php foreach (['online','offline','hybrid'] as $m): ?>
                        <option value="<?= $m ?>" <?= ($program['mode'] ?? '') === $m ? 'selected' : '' ?>><?= ucfirst($m) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="tcm-grid-3">
            <div class="tcm-field"><label>Price (₹)</label><input class="tcm-input" type="number" step="0.01" name="price" value="<?= $val('price', '0') ?>"></div>
            <div class="tcm-field"><label>Original price (₹)</label><input class="tcm-input" type="number" step="0.01" name="original_price" value="<?= $val('original_price') ?>"></div>
            <div class="tcm-field"><label>Duration</label><input class="tcm-input" name="duration" value="<?= $val('duration') ?>" placeholder="4 Months"></div>
        </div>
        <div class="tcm-grid-3">
            <div class="tcm-field"><label>Schedule</label><input class="tcm-input" name="schedule" value="<?= $val('schedule') ?>" placeholder="Mon–Fri 7–8:30 PM"></div>
            <div class="tcm-field"><label>Start date</label><input class="tcm-input" type="date" name="start_date" value="<?= $val('start_date') ?>"></div>
            <div class="tcm-field"><label>End date</label><input class="tcm-input" type="date" name="end_date" value="<?= $val('end_date') ?>"></div>
        </div>
        <div class="tcm-grid-3">
            <div class="tcm-field"><label>Total seats</label><input class="tcm-input" type="number" name="total_seats" value="<?= $val('total_seats', '0') ?>"></div>
            <div class="tcm-field"><label>Seats left</label><input class="tcm-input" type="number" name="seats_left" value="<?= $val('seats_left', '0') ?>"></div>
            <div class="tcm-field">
                <label>Status</label>
                <select class="tcm-select" name="status">
                    <?php foreach (['draft','published','archived'] as $st): ?>
                        <option value="<?= $st ?>" <?= ($program['status'] ?? 'draft') === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="tcm-field"><label>Highlights (one per line)</label><textarea class="tcm-textarea" name="highlights" placeholder="Daily live sessions&#10;1:1 mentorship&#10;Certificate"><?= $val('highlights') ?></textarea></div>
        <div class="tcm-field">
            <label>Level</label>
            <select class="tcm-select" name="level" style="max-width:200px;">
                <?php foreach (['beginner','intermediate','advanced'] as $lvl): ?>
                    <option value="<?= $lvl ?>" <?= ($program['level'] ?? '') === $lvl ? 'selected' : '' ?>><?= ucfirst($lvl) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <label class="muted" style="font-size:.85rem;"><input type="checkbox" name="is_featured" value="1" <?= (int)($program['is_featured'] ?? 0) ? 'checked' : '' ?>> Featured on homepage</label>

        <div class="tcm-field" style="margin-top:16px;">
            <label>Bundle courses (optional)</label>
            <div class="tcm-card" style="background:var(--tcm-surface-2);max-height:200px;overflow:auto;">
                <?php foreach ($allCourses as $c): ?>
                    <label style="display:block;font-size:.88rem;padding:3px 0;">
                        <input type="checkbox" name="courses[]" value="<?= $c['id'] ?>" <?= in_array((int)$c['id'], $linkedCourses, true) ? 'checked' : '' ?>>
                        <?= e($c['title']) ?>
                    </label>
                <?php endforeach; ?>
                <?php if ($allCourses === []): ?><span class="muted">No courses available.</span><?php endif; ?>
            </div>
        </div>

        <button class="tcm-btn primary"><i class="bi bi-check2"></i> <?= $isEdit ? 'Save Changes' : 'Create Program' ?></button>
    </div>
</form>

<?php if ($isEdit): ?>
<div class="tcm-card" style="margin-top:18px;">
    <h3 class="mt-0">Live sessions</h3>
    <?php foreach ($sessions as $s): ?>
        <div class="tcm-card" style="background:var(--tcm-surface-2);padding:14px 16px;margin-bottom:10px;">
            <div class="flex-between">
                <span>
                    <i class="bi bi-broadcast" style="color:var(--tcm-accent);"></i> <strong><?= e($s['title']) ?></strong>
                    <span class="muted" style="font-size:.8rem;">
                        <?= $s['session_date'] ? e(date('d M', strtotime($s['session_date']))) : '' ?>
                        <?= $s['start_time'] ? e(date('h:i A', strtotime($s['start_time']))) : '' ?>
                    </span>
                </span>
                <form method="post" action="<?= base_url('/admin/sessions/' . $s['id'] . '/delete') ?>">
                    <?= csrf_field() ?>
                    <button class="tcm-btn sm danger" style="padding:2px 8px;"><i class="bi bi-trash"></i></button>
                </form>
            </div>
            <form method="post" action="<?= base_url('/admin/sessions/' . $s['id']) ?>" class="d-flex gap-8 flex-wrap items-center" style="margin-top:10px;">
                <?= csrf_field() ?>
                <select class="tcm-select" name="status" style="width:140px;">
                    <?php foreach (['scheduled','live','completed','cancelled'] as $st): ?>
                        <option value="<?= $st ?>" <?= $s['status'] === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                    <?php endforeach; ?>
                </select>
                <input class="tcm-input" name="meeting_url" value="<?= e($s['meeting_url'] ?? '') ?>" placeholder="Meeting link" style="flex:1;min-width:160px;">
                <input class="tcm-input" name="recording_url" value="<?= e($s['recording_url'] ?? '') ?>" placeholder="Recording link" style="flex:1;min-width:160px;">
                <button class="tcm-btn sm primary"><i class="bi bi-check2"></i> Update</button>
            </form>
        </div>
    <?php endforeach; ?>
    <?php if ($sessions === []): ?><p class="muted">No sessions scheduled.</p><?php endif; ?>

    <form method="post" action="<?= base_url('/admin/programs/' . $program['id'] . '/sessions') ?>" style="margin-top:12px;">
        <?= csrf_field() ?>
        <div class="tcm-grid-2">
            <div class="tcm-field" style="margin-bottom:8px;"><input class="tcm-input" name="title" placeholder="Session title" required></div>
            <div class="tcm-field" style="margin-bottom:8px;"><input class="tcm-input" name="host" placeholder="Host / mentor"></div>
        </div>
        <div class="tcm-grid-3">
            <div class="tcm-field" style="margin-bottom:8px;"><input class="tcm-input" type="date" name="session_date"></div>
            <div class="tcm-field" style="margin-bottom:8px;"><input class="tcm-input" type="time" name="start_time"></div>
            <div class="tcm-field" style="margin-bottom:8px;"><input class="tcm-input" type="time" name="end_time"></div>
        </div>
        <div class="tcm-field"><input class="tcm-input" name="meeting_url" placeholder="Meeting link (Google Meet / Zoom)"></div>
        <button class="tcm-btn"><i class="bi bi-plus-lg"></i> Add session</button>
    </form>
</div>
<?php endif; ?>
