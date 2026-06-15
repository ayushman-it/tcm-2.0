<div class="tcm-page-head">
    <div><h2><?= e($course['title']) ?></h2><p><?= e($course['category_name'] ?? '') ?></p></div>
    <a class="tcm-btn" href="<?= base_url('/student/courses') ?>"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="tcm-grid-2" style="align-items:start;">
    <div class="tcm-card">
        <p><?= nl2br(e($course['description'] ?? '')) ?></p>
        <div class="d-flex gap-8 flex-wrap muted" style="font-size:.85rem;margin-top:10px;">
            <span><i class="bi bi-clock"></i> <?= e($course['duration'] ?? '—') ?></span>
            <span><i class="bi bi-translate"></i> <?= e($course['language'] ?? '') ?></span>
            <span><i class="bi bi-bar-chart"></i> <?= e(ucfirst($course['level'])) ?></span>
            <?php if ((int)$course['certificate']): ?><span><i class="bi bi-patch-check"></i> Certificate</span><?php endif; ?>
        </div>

        <h3>Curriculum</h3>
        <?php foreach ($curriculum as $m): ?>
            <details class="tcm-card" style="background:var(--tcm-surface-2);margin-bottom:10px;">
                <summary style="cursor:pointer;font-weight:600;"><?= e($m['title']) ?>
                    <span class="muted" style="font-weight:400;font-size:.8rem;">· <?= e($m['summary'] ?? '') ?></span></summary>
                <ul style="margin:10px 0 0;padding-left:18px;">
                    <?php foreach ($m['lessons'] as $l): ?>
                        <li class="muted" style="padding:3px 0;"><?= e($l['title']) ?>
                            <span class="tcm-badge gray"><?= e($l['type']) ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </details>
        <?php endforeach; ?>
        <?php if ($curriculum === []): ?><p class="muted">Curriculum coming soon.</p><?php endif; ?>
    </div>

    <div class="tcm-card">
        <div class="flex-between">
            <strong style="font-size:1.5rem;"><?= money($course['price']) ?></strong>
            <?php if ($course['original_price']): ?>
                <span class="muted" style="text-decoration:line-through;"><?= money($course['original_price']) ?></span>
            <?php endif; ?>
        </div>
        <?php if ((int)$course['total_seats'] > 0): ?>
            <p class="muted" style="font-size:.85rem;margin-top:10px;"><i class="bi bi-people"></i> <?= (int)$course['seats_left'] ?> seats left of <?= (int)$course['total_seats'] ?></p>
        <?php endif; ?>
        <div style="margin-top:14px;">
            <?php if ($enrolled): ?>
                <a class="tcm-btn primary" style="width:100%;justify-content:center;" href="<?= base_url('/student/learn/' . $course['id']) ?>">
                    <i class="bi bi-play-fill"></i> Continue Learning
                </a>
            <?php else: ?>
                <form method="post" action="<?= base_url('/student/courses/' . $course['id'] . '/buy') ?>">
                    <?= csrf_field() ?>
                    <button class="tcm-btn primary" style="width:100%;justify-content:center;">
                        <i class="bi bi-cart-check"></i> <?= (float)$course['price'] > 0 ? 'Buy Now' : 'Enroll Free' ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
