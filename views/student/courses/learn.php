<?php
$totalLessons = 0;
$doneCount = 0;
foreach ($curriculum as $m) {
    foreach ($m['lessons'] as $l) {
        $totalLessons++;
        if (in_array((int) $l['id'], $completed, true)) {
            $doneCount++;
        }
    }
}
$percent = $totalLessons > 0 ? (int) round($doneCount / $totalLessons * 100) : 0;
?>
<div class="tcm-page-head">
    <div><h2><?= e($course['title']) ?></h2><p><?= $doneCount ?> of <?= $totalLessons ?> lessons complete</p></div>
    <a class="tcm-btn" href="<?= base_url('/student') ?>"><i class="bi bi-arrow-left"></i> Dashboard</a>
</div>

<div class="tcm-card" style="margin-bottom:18px;">
    <div class="flex-between" style="margin-bottom:8px;">
        <strong>Your progress</strong><span class="tcm-badge purple"><?= $percent ?>%</span>
    </div>
    <div class="tcm-progress"><span style="width:<?= $percent ?>%"></span></div>
    <?php if ($percent >= 100): ?>
        <p class="muted" style="margin:12px 0 0;"><i class="bi bi-patch-check-fill" style="color:var(--tcm-accent);"></i>
            Course complete — your certificate is now in your portfolio!</p>
    <?php endif; ?>
</div>

<?php foreach ($curriculum as $m): ?>
    <div class="tcm-card" style="margin-bottom:14px;">
        <h3 class="mt-0"><?= e($m['title']) ?></h3>
        <?php foreach ($m['lessons'] as $l): $isDone = in_array((int)$l['id'], $completed, true); ?>
            <form method="post" action="<?= base_url('/student/learn/' . $course['id'] . '/lessons/' . $l['id']) ?>"
                  class="flex-between" style="padding:8px 0;border-bottom:1px solid var(--tcm-border);">
                <?= csrf_field() ?>
                <span>
                    <i class="bi <?= $isDone ? 'bi-check-circle-fill' : 'bi-circle' ?>" style="color:<?= $isDone ? 'var(--tcm-accent)' : 'var(--tcm-muted)' ?>;"></i>
                    <?= e($l['title']) ?>
                    <span class="tcm-badge gray"><?= e($l['type']) ?></span>
                </span>
                <button class="tcm-btn sm"><?= $isDone ? 'Mark undone' : 'Mark done' ?></button>
            </form>
        <?php endforeach; ?>
        <?php if ($m['lessons'] === []): ?><p class="muted mb-0">No lessons in this module yet.</p><?php endif; ?>
    </div>
<?php endforeach; ?>
<?php if ($curriculum === []): ?><div class="tcm-card"><p class="muted mb-0">Curriculum will be available soon.</p></div><?php endif; ?>
