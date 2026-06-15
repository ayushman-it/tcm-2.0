<div class="tcm-page-head">
    <div><h2>Testimonials</h2><p>Student success stories shown on the site.</p></div>
</div>

<div class="tcm-grid-2" style="align-items:start;">
    <div class="tcm-card">
        <?php foreach ($testimonials as $t): ?>
            <div class="flex-between" style="padding:10px 0;border-bottom:1px solid var(--tcm-border);">
                <div>
                    <strong><?= e($t['name']) ?></strong> <span class="muted">· <?= e($t['role'] ?? '') ?></span>
                    <div class="muted" style="font-size:.85rem;"><?= e($t['content']) ?></div>
                    <div style="color:#ffb800;font-size:.8rem;"><?= str_repeat('★', (int)$t['rating']) ?></div>
                </div>
                <form method="post" action="<?= base_url('/admin/testimonials/' . $t['id'] . '/delete') ?>">
                    <?= csrf_field() ?>
                    <button class="tcm-btn sm danger"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        <?php endforeach; ?>
        <?php if ($testimonials === []): ?><p class="muted mb-0">No testimonials.</p><?php endif; ?>
    </div>

    <div class="tcm-card">
        <h3 class="mt-0">Add testimonial</h3>
        <form method="post" action="<?= base_url('/admin/testimonials') ?>">
            <?= csrf_field() ?>
            <div class="tcm-field"><label>Name</label><input class="tcm-input" name="name" required></div>
            <div class="tcm-field"><label>Role</label><input class="tcm-input" name="role" placeholder="Frontend Developer @ Startup"></div>
            <div class="tcm-field"><label>Content</label><textarea class="tcm-textarea" name="content" required></textarea></div>
            <div class="tcm-grid-2">
                <div class="tcm-field"><label>Rating (1-5)</label><input class="tcm-input" type="number" min="1" max="5" name="rating" value="5"></div>
                <div class="tcm-field"><label>Sort order</label><input class="tcm-input" type="number" name="sort_order" value="0"></div>
            </div>
            <button class="tcm-btn primary"><i class="bi bi-plus-lg"></i> Add</button>
        </form>
    </div>
</div>
