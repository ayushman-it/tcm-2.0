<div class="tcm-page-head">
    <div><h2>Courses</h2><p>Find a learning path that fits your goals.</p></div>
</div>

<div class="tcm-card" style="margin-bottom:18px;">
    <form method="get" class="d-flex gap-8 flex-wrap items-center">
        <input class="tcm-input" name="q" placeholder="Search courses..." value="<?= e($_GET['q'] ?? '') ?>" style="flex:1;min-width:200px;">
        <select class="tcm-select" name="audience" style="width:200px;" onchange="this.form.submit()">
            <option value="">All audiences</option>
            <?php foreach (['college'=>'College','beginners'=>'Beginners','working'=>'Working Pros'] as $k=>$lbl): ?>
                <option value="<?= $k ?>" <?= ($_GET['audience'] ?? '') === $k ? 'selected' : '' ?>><?= $lbl ?></option>
            <?php endforeach; ?>
        </select>
        <button class="tcm-btn primary">Search</button>
    </form>
</div>

<div class="grid-cards">
    <?php foreach ($courses as $c): $isOwned = in_array((int)$c['id'], array_map('intval', $owned), true); ?>
        <div class="tcm-card">
            <div class="flex-between">
                <div style="font-size:1.6rem;color:var(--tcm-primary-2);"><i class="bi <?= e($c['icon'] ?? 'bi-journal-code') ?>"></i></div>
                <?php if ($c['original_price'] && $c['original_price'] > $c['price']): ?>
                    <span class="tcm-badge green"><?= TCM\Models\Course::discountPercent($c) ?>% off</span>
                <?php endif; ?>
            </div>
            <h3 style="margin:12px 0 4px;"><?= e($c['title']) ?></h3>
            <p class="muted" style="font-size:.86rem;min-height:40px;"><?= e($c['subtitle'] ?? '') ?></p>
            <div class="flex-between" style="margin:10px 0;">
                <strong><?= money($c['price']) ?>
                    <?php if ($c['original_price']): ?><span class="muted" style="text-decoration:line-through;font-weight:400;font-size:.85rem;"><?= money($c['original_price']) ?></span><?php endif; ?>
                </strong>
                <span class="muted" style="font-size:.8rem;"><i class="bi bi-star-fill" style="color:#ffb800;"></i> <?= e((string)$c['rating']) ?></span>
            </div>
            <div class="d-flex gap-8">
                <a class="tcm-btn" href="<?= base_url('/student/courses/' . $c['slug']) ?>" style="flex:1;justify-content:center;">Details</a>
                <?php if ($isOwned): ?>
                    <a class="tcm-btn primary" href="<?= base_url('/student/learn/' . $c['id']) ?>" style="flex:1;justify-content:center;">Continue</a>
                <?php else: ?>
                    <form method="post" action="<?= base_url('/student/courses/' . $c['id'] . '/buy') ?>" style="flex:1;">
                        <?= csrf_field() ?>
                        <button class="tcm-btn primary" style="width:100%;justify-content:center;">
                            <?= (float)$c['price'] > 0 ? 'Buy' : 'Enroll' ?>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if ($courses === []): ?><p class="muted">No courses match your search.</p><?php endif; ?>
</div>
