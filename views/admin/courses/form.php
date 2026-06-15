<?php
$isEdit = $course !== null;
$action = $isEdit ? base_url('/admin/courses/' . $course['id']) : base_url('/admin/courses');
$val = static fn (string $k, $d = '') => e((string) ($course[$k] ?? $d));
?>
<div class="tcm-page-head">
    <div>
        <h2><?= $isEdit ? 'Edit Course' : 'New Course' ?></h2>
        <p><?= $isEdit ? e($course['title']) : 'Add a new course to the catalog.' ?></p>
    </div>
    <a class="tcm-btn" href="<?= base_url('/admin/courses') ?>"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<form method="post" action="<?= $action ?>">
    <?= csrf_field() ?>
    <div class="tcm-card">
        <div class="tcm-field">
            <label>Title</label>
            <input class="tcm-input" name="title" value="<?= $val('title') ?>" required>
        </div>
        <div class="tcm-field">
            <label>Subtitle</label>
            <input class="tcm-input" name="subtitle" value="<?= $val('subtitle') ?>">
        </div>
        <div class="tcm-field">
            <label>Description</label>
            <textarea class="tcm-textarea" name="description"><?= $val('description') ?></textarea>
        </div>
        <div class="tcm-grid-3">
            <div class="tcm-field">
                <label>Category</label>
                <select class="tcm-select" name="category_id">
                    <option value="">— None —</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= (int)($course['category_id'] ?? 0) === (int)$cat['id'] ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="tcm-field">
                <label>Icon (bootstrap-icon)</label>
                <input class="tcm-input" name="icon" value="<?= $val('icon', 'bi-journal-code') ?>" placeholder="bi-code-slash">
            </div>
            <div class="tcm-field">
                <label>Level</label>
                <select class="tcm-select" name="level">
                    <?php foreach (['beginner','intermediate','advanced'] as $lvl): ?>
                        <option value="<?= $lvl ?>" <?= ($course['level'] ?? '') === $lvl ? 'selected' : '' ?>><?= ucfirst($lvl) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="tcm-grid-3">
            <div class="tcm-field"><label>Price (₹)</label><input class="tcm-input" type="number" step="0.01" name="price" value="<?= $val('price', '0') ?>" required></div>
            <div class="tcm-field"><label>Original price (₹)</label><input class="tcm-input" type="number" step="0.01" name="original_price" value="<?= $val('original_price') ?>"></div>
            <div class="tcm-field"><label>Duration</label><input class="tcm-input" name="duration" value="<?= $val('duration') ?>" placeholder="3 Months"></div>
        </div>
        <div class="tcm-grid-3">
            <div class="tcm-field"><label>Language</label><input class="tcm-input" name="language" value="<?= $val('language', 'Hindi + English') ?>"></div>
            <div class="tcm-field"><label>Schedule</label><input class="tcm-input" name="schedule" value="<?= $val('schedule') ?>" placeholder="7:00 PM - 8:30 PM"></div>
            <div class="tcm-field"><label>Starts at</label><input class="tcm-input" type="date" name="starts_at" value="<?= $val('starts_at') ?>"></div>
        </div>
        <div class="tcm-grid-3">
            <div class="tcm-field"><label>Total seats</label><input class="tcm-input" type="number" name="total_seats" value="<?= $val('total_seats', '0') ?>"></div>
            <div class="tcm-field"><label>Seats left</label><input class="tcm-input" type="number" name="seats_left" value="<?= $val('seats_left', '0') ?>"></div>
            <div class="tcm-field">
                <label>Status</label>
                <select class="tcm-select" name="status">
                    <?php foreach (['draft','published','archived'] as $st): ?>
                        <option value="<?= $st ?>" <?= ($course['status'] ?? 'draft') === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="d-flex gap-8 flex-wrap">
            <label class="muted" style="font-size:.85rem;"><input type="checkbox" name="certificate" value="1" <?= (int)($course['certificate'] ?? 1) ? 'checked' : '' ?>> Certificate</label>
            <label class="muted" style="font-size:.85rem;"><input type="checkbox" name="is_featured" value="1" <?= (int)($course['is_featured'] ?? 0) ? 'checked' : '' ?>> Featured</label>
            <label class="muted" style="font-size:.85rem;"><input type="checkbox" name="is_bestseller" value="1" <?= (int)($course['is_bestseller'] ?? 0) ? 'checked' : '' ?>> Bestseller</label>
        </div>
        <div style="margin-top:18px;">
            <button class="tcm-btn primary"><i class="bi bi-check2"></i> <?= $isEdit ? 'Save Changes' : 'Create Course' ?></button>
        </div>
    </div>
</form>

<?php if ($isEdit): ?>
<div class="tcm-card" style="margin-top:18px;">
    <h3 class="mt-0">Curriculum</h3>
    <p class="muted" style="margin-top:-6px;">Organise the course into modules and lessons.</p>

    <?php foreach (($modules ?? []) as $m): ?>
        <div class="tcm-card" style="background:var(--tcm-surface-2);margin-bottom:14px;">
            <div class="flex-between">
                <strong><?= e($m['title']) ?> <span class="muted" style="font-weight:400;font-size:.8rem;">— <?= e($m['summary'] ?? '') ?></span></strong>
                <form method="post" action="<?= base_url('/admin/modules/' . $m['id'] . '/delete') ?>" onsubmit="return confirm('Delete module and its lessons?');">
                    <?= csrf_field() ?>
                    <button class="tcm-btn sm danger"><i class="bi bi-trash"></i></button>
                </form>
            </div>
            <ul style="margin:12px 0;padding-left:0;list-style:none;">
                <?php foreach ($m['lessons'] as $l): ?>
                    <li class="flex-between" style="padding:6px 0;border-bottom:1px solid var(--tcm-border);">
                        <span><i class="bi bi-play-circle muted"></i> <?= e($l['title']) ?>
                            <span class="tcm-badge gray"><?= e($l['type']) ?></span></span>
                        <form method="post" action="<?= base_url('/admin/lessons/' . $l['id'] . '/delete') ?>">
                            <?= csrf_field() ?>
                            <button class="tcm-btn sm danger" style="padding:2px 8px;"><i class="bi bi-x"></i></button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
            <form method="post" action="<?= base_url('/admin/modules/' . $m['id'] . '/lessons') ?>" class="d-flex gap-8 flex-wrap items-center">
                <?= csrf_field() ?>
                <input class="tcm-input" name="title" placeholder="Lesson title" style="flex:1;min-width:200px;" required>
                <select class="tcm-select" name="type" style="width:130px;">
                    <option value="live">Live</option><option value="video">Video</option>
                    <option value="project">Project</option><option value="reading">Reading</option><option value="quiz">Quiz</option>
                </select>
                <button class="tcm-btn sm"><i class="bi bi-plus"></i> Add lesson</button>
            </form>
        </div>
    <?php endforeach; ?>

    <form method="post" action="<?= base_url('/admin/courses/' . $course['id'] . '/modules') ?>" class="d-flex gap-8 flex-wrap items-center" style="margin-top:8px;">
        <?= csrf_field() ?>
        <input class="tcm-input" name="title" placeholder="New module title" style="flex:1;min-width:200px;" required>
        <input class="tcm-input" name="summary" placeholder="Summary (optional)" style="flex:1;min-width:160px;">
        <button class="tcm-btn primary"><i class="bi bi-plus-lg"></i> Add module</button>
    </form>
</div>
<?php endif; ?>
