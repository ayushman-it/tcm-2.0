<?php use TCM\Models\Program; ?>
<div class="tcm-page-head">
    <div><h2>Programs</h2><p>Live classes, learning tracks, internships and the Summer Campus.</p></div>
</div>

<div class="tcm-card" style="margin-bottom:18px;">
    <form method="get" class="d-flex gap-8 flex-wrap items-center">
        <input class="tcm-input" name="q" placeholder="Search programs..." value="<?= e($_GET['q'] ?? '') ?>" style="flex:1;min-width:200px;">
        <select class="tcm-select" name="type" style="width:200px;" onchange="this.form.submit()">
            <option value="">All types</option>
            <?php foreach (Program::TYPES as $k => $lbl): ?>
                <option value="<?= $k ?>" <?= ($_GET['type'] ?? '') === $k ? 'selected' : '' ?>><?= e($lbl) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="tcm-btn primary">Search</button>
    </form>
</div>

<div class="grid-cards">
    <?php foreach ($programs as $p): $isJoined = in_array((int)$p['id'], $joined, true); ?>
        <div class="tcm-card">
            <div class="flex-between">
                <div style="font-size:1.6rem;color:var(--tcm-primary-2);"><i class="bi <?= e($p['icon'] ?? 'bi-stack') ?>"></i></div>
                <span class="tcm-badge purple"><?= e(Program::TYPES[$p['type']] ?? $p['type']) ?></span>
            </div>
            <h3 style="margin:12px 0 4px;"><?= e($p['title']) ?></h3>
            <p class="muted" style="font-size:.86rem;min-height:40px;"><?= e($p['subtitle'] ?? '') ?></p>
            <div class="muted d-flex gap-8 flex-wrap" style="font-size:.8rem;margin-bottom:10px;">
                <?php if ($p['duration']): ?><span><i class="bi bi-clock"></i> <?= e($p['duration']) ?></span><?php endif; ?>
                <span><i class="bi bi-laptop"></i> <?= e(ucfirst($p['mode'])) ?></span>
            </div>
            <div class="flex-between" style="margin-bottom:12px;">
                <strong><?= (float)$p['price'] > 0 ? money($p['price']) : 'Free' ?></strong>
                <?php if ($p['seats_left']): ?><span class="muted" style="font-size:.78rem;"><?= (int)$p['seats_left'] ?> seats left</span><?php endif; ?>
            </div>
            <div class="d-flex gap-8">
                <a class="tcm-btn" href="<?= base_url('/student/programs/' . $p['slug']) ?>" style="flex:1;justify-content:center;">Details</a>
                <?php if ($isJoined): ?>
                    <button class="tcm-btn green" style="flex:1;justify-content:center;background:rgba(0,210,168,.14);color:var(--tcm-accent);" disabled>Joined</button>
                <?php elseif ($p['type'] === 'internship'): ?>
                    <a class="tcm-btn primary" style="flex:1;justify-content:center;" href="<?= base_url('/student/internships/' . $p['id'] . '/apply') ?>">Apply</a>
                <?php else: ?>
                    <form method="post" action="<?= base_url('/student/programs/' . $p['id'] . '/enquire') ?>" style="flex:1;">
                        <?= csrf_field() ?>
                        <button class="tcm-btn primary" style="width:100%;justify-content:center;">
                            <?= (float)$p['price'] > 0 ? 'Enquire' : 'Join' ?>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if ($programs === []): ?><p class="muted">No programs found.</p><?php endif; ?>
</div>
