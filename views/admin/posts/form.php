<?php
$isEdit = $post !== null;
$action = $isEdit ? base_url('/admin/posts/' . $post['id']) : base_url('/admin/posts');
$val = static fn (string $k, $d = '') => e((string) ($post[$k] ?? $d));
?>
<div class="tcm-page-head">
    <div><h2><?= $isEdit ? 'Edit Insight' : 'New Insight' ?></h2></div>
    <a class="tcm-btn" href="<?= base_url('/admin/posts') ?>"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<form method="post" action="<?= $action ?>">
    <?= csrf_field() ?>
    <div class="tcm-card">
        <div class="tcm-field"><label>Title</label><input class="tcm-input" name="title" value="<?= $val('title') ?>" required></div>
        <div class="tcm-grid-2">
            <div class="tcm-field"><label>Category</label><input class="tcm-input" name="category" value="<?= $val('category', 'General') ?>"></div>
            <div class="tcm-field"><label>Tags (comma separated)</label><input class="tcm-input" name="tags" value="<?= $val('tags') ?>"></div>
        </div>
        <div class="tcm-field"><label>Excerpt</label><textarea class="tcm-textarea" name="excerpt" style="min-height:70px;"><?= $val('excerpt') ?></textarea></div>
        <div class="tcm-field"><label>Content</label><textarea class="tcm-textarea" name="content" style="min-height:220px;"><?= $val('content') ?></textarea></div>
        <div class="tcm-field" style="max-width:200px;">
            <label>Status</label>
            <select class="tcm-select" name="status">
                <option value="draft" <?= ($post['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= ($post['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
            </select>
        </div>
        <button class="tcm-btn primary"><i class="bi bi-check2"></i> <?= $isEdit ? 'Save Changes' : 'Publish' ?></button>
    </div>
</form>
