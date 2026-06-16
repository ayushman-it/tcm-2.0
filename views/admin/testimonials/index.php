<style>
.adm-test-card {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 16px 0;
    border-bottom: 1px solid #f0f0f0;
}
.adm-test-card:first-child { padding-top: 0; }
.adm-test-card:last-child  { border-bottom: none; padding-bottom: 0; }
.adm-test-avatar {
    width: 38px; height: 38px;
    border-radius: 50%;
    background: #f0f0f0; border: 1.5px solid #e5e5e5;
    display: grid; place-items: center;
    font-weight: 700; font-size: .82rem; color: #111;
    flex-shrink: 0;
}
.adm-test-stars { color: #f59e0b; font-size: .8rem; letter-spacing: 1px; }
.adm-status-dot {
    display: inline-block;
    width: 7px; height: 7px;
    border-radius: 50%;
    margin-right: 4px;
    background: #22c55e;
    vertical-align: middle;
}
</style>

<div class="tcm-page-head">
    <div>
        <h2>Testimonials</h2>
        <p>Student reviews shown on the homepage. Add up to 20 for best display.</p>
    </div>
    <span class="tcm-badge <?= count($testimonials) >= 20 ? 'green' : 'amber' ?>">
        <?= count($testimonials) ?> / 20
    </span>
</div>

<div class="tcm-grid-2" style="align-items:start;">

    <!-- List -->
    <div class="tcm-card">
        <div class="flex-between" style="margin-bottom:16px;">
            <h3><i class="bi bi-chat-quote"></i> All Reviews</h3>
        </div>

        <?php if ($testimonials === []): ?>
            <div class="tcm-empty">
                <i class="bi bi-chat-square-dots"></i>
                No testimonials yet. Add your first one.
            </div>
        <?php else: ?>
        <?php foreach ($testimonials as $t): ?>
        <div class="adm-test-card">
            <div class="adm-test-avatar">
                <?= e(strtoupper(substr($t['name'], 0, 1))) ?>
            </div>
            <div style="flex:1;min-width:0;">
                <div class="flex-between gap-8">
                    <div>
                        <div style="font-weight:700;font-size:.85rem;color:#111;"><?= e($t['name']) ?></div>
                        <?php if ($t['role']): ?>
                            <div style="font-size:.74rem;color:var(--muted);"><?= e($t['role']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex items-center gap-8" style="flex-shrink:0;">
                        <span class="adm-test-stars">
                            <?= str_repeat('★', (int)$t['rating']) ?><?= str_repeat('☆', 5 - (int)$t['rating']) ?>
                        </span>
                        <form method="post"
                              action="<?= base_url('/admin/testimonials/' . $t['id'] . '/delete') ?>"
                              onsubmit="return confirm('Delete this testimonial?')">
                            <?= csrf_field() ?>
                            <button class="tcm-btn sm danger" style="padding:4px 8px;">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <p style="font-size:.82rem;color:#555;margin-top:5px;line-height:1.55;">
                    "<?= e($t['content']) ?>"
                </p>
                <div style="font-size:.72rem;color:var(--muted2);margin-top:3px;">
                    Sort: <?= (int)$t['sort_order'] ?>
                    &nbsp;·&nbsp;
                    <span class="adm-status-dot"
                          style="background:<?= $t['status'] === 'active' ? '#22c55e' : '#aaa' ?>;"></span>
                    <?= e($t['status']) ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Add form -->
    <div class="tcm-card">
        <h3 style="margin-bottom:16px;">
            <i class="bi bi-plus-circle"></i> Add Testimonial
        </h3>

        <form method="post" action="<?= base_url('/admin/testimonials') ?>">
            <?= csrf_field() ?>

            <div class="tcm-field">
                <label>Student Name *</label>
                <input class="tcm-input" name="name" required
                       placeholder="e.g. Priya Nair">
            </div>

            <div class="tcm-field">
                <label>Role / Title</label>
                <input class="tcm-input" name="role"
                       placeholder="e.g. Frontend Developer @ Startup">
            </div>

            <div class="tcm-field">
                <label>Testimonial *</label>
                <textarea class="tcm-textarea" name="content" required
                          style="min-height:100px;"
                          placeholder="What did they say about TCM?"></textarea>
            </div>

            <div class="tcm-grid-2">
                <div class="tcm-field">
                    <label>Rating (1–5)</label>
                    <select class="tcm-select" name="rating">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?= $i ?>" <?= $i === 5 ? 'selected' : '' ?>>
                                <?= $i ?> star<?= $i > 1 ? 's' : '' ?>
                                (<?= str_repeat('★', $i) ?>)
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="tcm-field">
                    <label>Sort Order</label>
                    <input class="tcm-input" type="number" name="sort_order"
                           value="<?= count($testimonials) + 1 ?>"
                           min="0" max="99">
                </div>
            </div>

            <button type="submit" class="tcm-btn primary w-full"
                    style="justify-content:center;margin-top:4px;">
                <i class="bi bi-plus-lg"></i> Add Testimonial
            </button>
        </form>

        <div style="margin-top:18px;padding:14px;background:#f9f9f9;border-radius:10px;font-size:.78rem;color:#888;line-height:1.6;">
            <i class="bi bi-info-circle" style="margin-right:4px;"></i>
            Testimonials are shown live on the homepage via the API.
            The top 20 (by sort order) are displayed.
        </div>
    </div>

</div>
