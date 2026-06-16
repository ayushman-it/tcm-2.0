<?php
$isEdit = $course !== null;
$action = $isEdit ? base_url('/admin/courses/' . $course['id']) : base_url('/admin/courses');
$val    = static fn(string $k, $d = '') => e((string)($course[$k] ?? $d));
?>

<!-- Page header -->
<div class="tcm-page-head">
    <div>
        <h2><?= $isEdit ? 'Edit Course' : 'New Course' ?></h2>
        <p><?= $isEdit ? e($course['title']) : 'Add a new course to the catalog.' ?></p>
    </div>
    <div class="d-flex gap-8">
        <?php if ($isEdit): ?>
            <a href="<?= base_url('/student/courses/' . $course['slug']) ?>"
               target="_blank" class="tcm-btn sm ghost">
                <i class="bi bi-eye"></i> Preview
            </a>
        <?php endif; ?>
        <a class="tcm-btn ghost sm" href="<?= base_url('/admin/courses') ?>">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<form method="post" action="<?= $action ?>">
    <?= csrf_field() ?>

    <!-- ── Section: Basic Info ── -->
    <div class="tcm-card" style="margin-bottom:14px;">
        <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;
                    color:var(--muted);margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid #f0f0f0;">
            <i class="bi bi-info-circle" style="margin-right:5px;"></i> Basic Information
        </div>

        <div class="tcm-field">
            <label>Course Title *</label>
            <input class="tcm-input" name="title" value="<?= $val('title') ?>" required
                   placeholder="e.g. Full Stack Development">
        </div>
        <div class="tcm-field">
            <label>Subtitle</label>
            <input class="tcm-input" name="subtitle" value="<?= $val('subtitle') ?>"
                   placeholder="Short description shown on cards">
        </div>
        <div class="tcm-field">
            <label>Full Description</label>
            <textarea class="tcm-textarea" name="description"
                      style="min-height:110px;"><?= $val('description') ?></textarea>
        </div>

        <div class="tcm-grid-2">
            <div class="tcm-field">
                <label>Category</label>
                <select class="tcm-select" name="category_id">
                    <option value="">— None —</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"
                            <?= (int)($course['category_id'] ?? 0) === (int)$cat['id'] ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="tcm-field">
                <label>Level</label>
                <select class="tcm-select" name="level">
                    <?php foreach (['beginner' => '🌱 Beginner', 'intermediate' => '⚡ Intermediate', 'advanced' => '🔥 Advanced'] as $v => $l): ?>
                        <option value="<?= $v ?>" <?= ($course['level'] ?? 'beginner') === $v ? 'selected' : '' ?>>
                            <?= $l ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="tcm-grid-2">
            <div class="tcm-field">
                <label>Bootstrap Icon</label>
                <div class="d-flex items-center gap-8">
                    <input class="tcm-input" name="icon" id="iconInput"
                           value="<?= $val('icon', 'bi-journal-code') ?>"
                           placeholder="bi-code-slash"
                           oninput="document.getElementById('iconPreview').className='bi '+this.value">
                    <i id="iconPreview"
                       class="bi <?= $val('icon', 'bi-journal-code') ?>"
                       style="font-size:1.5rem;color:#111;flex-shrink:0;"></i>
                </div>
            </div>
            <div class="tcm-field">
                <label>Language</label>
                <input class="tcm-input" name="language"
                       value="<?= $val('language', 'Hindi + English') ?>">
            </div>
        </div>
    </div>

    <!-- ── Section: Pricing & Schedule ── -->
    <div class="tcm-card" style="margin-bottom:14px;">
        <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;
                    color:var(--muted);margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid #f0f0f0;">
            <i class="bi bi-currency-rupee" style="margin-right:5px;"></i> Pricing & Schedule
        </div>

        <div class="tcm-grid-2" style="gap:12px;">
            <div class="tcm-field">
                <label>Price (₹) *</label>
                <input class="tcm-input" type="number" step="0.01" name="price"
                       value="<?= $val('price', '0') ?>" required>
            </div>
            <div class="tcm-field">
                <label>Original Price (₹)</label>
                <input class="tcm-input" type="number" step="0.01" name="original_price"
                       value="<?= $val('original_price') ?>"
                       placeholder="Leave blank if no discount">
            </div>
        </div>

        <div class="tcm-grid-2" style="gap:12px;">
            <div class="tcm-field">
                <label>Duration</label>
                <input class="tcm-input" name="duration" value="<?= $val('duration') ?>"
                       placeholder="e.g. 3 Months">
            </div>
            <div class="tcm-field">
                <label>Schedule</label>
                <input class="tcm-input" name="schedule" value="<?= $val('schedule') ?>"
                       placeholder="e.g. 7:00 PM – 8:30 PM">
            </div>
        </div>

        <div class="tcm-grid-2" style="gap:12px;">
            <div class="tcm-field">
                <label>Start Date</label>
                <input class="tcm-input" type="date" name="starts_at"
                       value="<?= $val('starts_at') ?>">
            </div>
            <div class="tcm-field">
                <label>Status</label>
                <select class="tcm-select" name="status">
                    <?php foreach (['draft' => '📝 Draft', 'published' => '✅ Published', 'archived' => '📦 Archived'] as $v => $l): ?>
                        <option value="<?= $v ?>" <?= ($course['status'] ?? 'draft') === $v ? 'selected' : '' ?>>
                            <?= $l ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="tcm-grid-2" style="gap:12px;">
            <div class="tcm-field">
                <label>Total Seats</label>
                <input class="tcm-input" type="number" name="total_seats"
                       value="<?= $val('total_seats', '24') ?>">
            </div>
            <div class="tcm-field">
                <label>Seats Left</label>
                <input class="tcm-input" type="number" name="seats_left"
                       value="<?= $val('seats_left', '24') ?>">
            </div>
        </div>

        <div class="d-flex gap-12 flex-wrap" style="margin-top:8px;">
            <label style="display:flex;align-items:center;gap:7px;font-size:.84rem;cursor:pointer;">
                <input type="checkbox" name="certificate" value="1"
                       <?= (int)($course['certificate'] ?? 1) ? 'checked' : '' ?>
                       style="accent-color:#111;width:15px;height:15px;">
                Certificate included
            </label>
            <label style="display:flex;align-items:center;gap:7px;font-size:.84rem;cursor:pointer;">
                <input type="checkbox" name="is_featured" value="1"
                       <?= (int)($course['is_featured'] ?? 0) ? 'checked' : '' ?>
                       style="accent-color:#111;width:15px;height:15px;">
                Featured on homepage
            </label>
            <label style="display:flex;align-items:center;gap:7px;font-size:.84rem;cursor:pointer;">
                <input type="checkbox" name="is_bestseller" value="1"
                       <?= (int)($course['is_bestseller'] ?? 0) ? 'checked' : '' ?>
                       style="accent-color:#111;width:15px;height:15px;">
                Bestseller badge
            </label>
        </div>
    </div>

    <!-- Save button -->
    <div style="margin-bottom:24px;">
        <button type="submit" class="tcm-btn primary" style="padding:12px 28px;">
            <i class="bi bi-check2-circle"></i>
            <?= $isEdit ? 'Save Changes' : 'Create Course' ?>
        </button>
        <?php if ($isEdit): ?>
            <span style="font-size:.78rem;color:var(--muted);margin-left:12px;">
                Last updated: <?= $course['updated_at'] ? e(date('d M Y H:i', strtotime($course['updated_at']))) : '—' ?>
            </span>
        <?php endif; ?>
    </div>

</form>

<!-- ── Curriculum Builder (edit mode only) ── -->
<?php if ($isEdit): ?>
<div class="tcm-card">

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
        <div>
            <h3 style="margin:0;font-size:1rem;">
                <i class="bi bi-collection" style="color:var(--muted);margin-right:7px;"></i>
                Curriculum Builder
            </h3>
            <p style="font-size:.8rem;color:var(--muted);margin:3px 0 0;">
                Organise lessons into modules. Changes save instantly per action.
            </p>
        </div>
        <span class="tcm-badge gray"><?= count($modules ?? []) ?> modules</span>
    </div>

    <div style="height:1px;background:#f0f0f0;margin:14px 0 18px;"></div>

    <!-- Module list -->
    <?php if (empty($modules)): ?>
        <div class="tcm-empty" style="padding:20px 0 16px;">
            <i class="bi bi-collection"></i>
            No modules yet. Add your first module below.
        </div>
    <?php endif; ?>

    <?php foreach (($modules ?? []) as $mi => $m): ?>
    <div style="background:#f9f9f9;border:1px solid #ececec;border-radius:12px;
                padding:16px 18px;margin-bottom:12px;">

        <!-- Module header -->
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:12px;">
            <div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="width:24px;height:24px;background:#111;color:#fff;border-radius:50%;
                                 display:grid;place-items:center;font-size:.68rem;font-weight:800;flex-shrink:0;">
                        <?= $mi + 1 ?>
                    </span>
                    <span style="font-weight:700;font-size:.9rem;color:#111;"><?= e($m['title']) ?></span>
                </div>
                <?php if ($m['summary']): ?>
                    <div style="font-size:.76rem;color:var(--muted);margin-top:3px;margin-left:32px;">
                        <?= e($m['summary']) ?>
                    </div>
                <?php endif; ?>
            </div>
            <form method="post"
                  action="<?= base_url('/admin/modules/' . $m['id'] . '/delete') ?>"
                  onsubmit="return confirm('Delete module \'<?= e(addslashes($m['title'])) ?>\' and all its lessons?')">
                <?= csrf_field() ?>
                <button class="tcm-btn sm danger" title="Delete module">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>

        <!-- Lessons list -->
        <?php if (!empty($m['lessons'])): ?>
        <div style="background:#fff;border:1px solid #ececec;border-radius:9px;
                    overflow:hidden;margin-bottom:12px;">
            <?php foreach ($m['lessons'] as $li => $l): ?>
            <div style="display:flex;align-items:center;gap:10px;padding:9px 14px;
                        border-bottom:1px solid #f5f5f5;font-size:.83rem;">
                <i class="bi <?= $l['type'] === 'project' ? 'bi-folder2-open' : ($l['type'] === 'video' ? 'bi-play-circle' : 'bi-broadcast') ?>"
                   style="color:var(--muted);flex-shrink:0;font-size:.85rem;"></i>
                <span style="flex:1;color:#111;"><?= e($l['title']) ?></span>
                <span class="tcm-badge <?= match($l['type']) { 'project' => 'amber', 'video' => 'purple', 'quiz' => 'green', default => 'gray' } ?>"
                      style="font-size:.62rem;">
                    <?= e($l['type']) ?>
                </span>
                <?php if ($l['is_preview']): ?>
                    <span class="tcm-badge green" style="font-size:.62rem;">preview</span>
                <?php endif; ?>
                <form method="post"
                      action="<?= base_url('/admin/lessons/' . $l['id'] . '/delete') ?>">
                    <?= csrf_field() ?>
                    <button class="tcm-btn sm danger" style="padding:3px 7px;" title="Remove lesson">
                        <i class="bi bi-x"></i>
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Add lesson to this module -->
        <form method="post"
              action="<?= base_url('/admin/modules/' . $m['id'] . '/lessons') ?>"
              style="display:flex;gap:8px;flex-wrap:wrap;align-items:flex-end;">
            <?= csrf_field() ?>
            <div style="flex:1;min-width:180px;">
                <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;
                            letter-spacing:.07em;color:var(--muted);margin-bottom:4px;">
                    Add lesson
                </div>
                <input class="tcm-input" name="title" placeholder="Lesson title" required
                       style="font-size:.83rem;">
            </div>
            <div style="width:120px;">
                <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;
                            letter-spacing:.07em;color:var(--muted);margin-bottom:4px;">
                    Type
                </div>
                <select class="tcm-select" name="type" style="font-size:.83rem;">
                    <option value="live">🔴 Live</option>
                    <option value="video">▶️ Video</option>
                    <option value="project">📁 Project</option>
                    <option value="reading">📖 Reading</option>
                    <option value="quiz">✅ Quiz</option>
                </select>
            </div>
            <div>
                <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;
                            letter-spacing:.07em;color:var(--muted);margin-bottom:4px;">
                    Preview?
                </div>
                <select class="tcm-select" name="is_preview" style="width:80px;font-size:.83rem;">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </div>
            <button type="submit" class="tcm-btn sm primary" style="align-self:flex-end;">
                <i class="bi bi-plus"></i> Add
            </button>
        </form>
    </div>
    <?php endforeach; ?>

    <!-- Add new module -->
    <div style="background:#f0f0f0;border:1.5px dashed #ddd;border-radius:12px;
                padding:16px 18px;margin-top:4px;">
        <div style="font-size:.76rem;font-weight:700;text-transform:uppercase;
                    letter-spacing:.08em;color:var(--muted);margin-bottom:12px;">
            <i class="bi bi-plus-circle" style="margin-right:5px;"></i>
            New Module
        </div>
        <form method="post"
              action="<?= base_url('/admin/courses/' . $course['id'] . '/modules') ?>"
              style="display:flex;gap:8px;flex-wrap:wrap;align-items:flex-end;">
            <?= csrf_field() ?>
            <div style="flex:1;min-width:200px;">
                <input class="tcm-input" name="title" placeholder="Module title *" required>
            </div>
            <div style="flex:1;min-width:160px;">
                <input class="tcm-input" name="summary"
                       placeholder="e.g. 8 sessions · 12 hours">
            </div>
            <button type="submit" class="tcm-btn primary">
                <i class="bi bi-plus-lg"></i> Add Module
            </button>
        </form>
    </div>

</div>
<?php endif; ?>
