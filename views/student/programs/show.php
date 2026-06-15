<?php use TCM\Models\Program; $highlights = Program::highlightList($program['highlights'] ?? null); ?>
<div class="tcm-page-head">
    <div><h2><?= e($program['title']) ?></h2><p><?= e(Program::TYPES[$program['type']] ?? '') ?></p></div>
    <a class="tcm-btn" href="<?= base_url('/student/programs') ?>"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="tcm-grid-2" style="align-items:start;">
    <div class="tcm-card">
        <p><?= nl2br(e($program['description'] ?? '')) ?></p>
        <div class="d-flex gap-8 flex-wrap muted" style="font-size:.85rem;margin:10px 0;">
            <?php if ($program['duration']): ?><span><i class="bi bi-clock"></i> <?= e($program['duration']) ?></span><?php endif; ?>
            <?php if ($program['schedule']): ?><span><i class="bi bi-calendar-week"></i> <?= e($program['schedule']) ?></span><?php endif; ?>
            <span><i class="bi bi-laptop"></i> <?= e(ucfirst($program['mode'])) ?></span>
        </div>

        <?php if ($highlights !== []): ?>
            <h3>What's included</h3>
            <ul style="padding-left:18px;">
                <?php foreach ($highlights as $h): ?><li class="muted" style="padding:2px 0;"><?= e($h) ?></li><?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ($courses !== []): ?>
            <h3>Courses in this program</h3>
            <?php foreach ($courses as $c): ?>
                <div class="flex-between" style="padding:6px 0;border-bottom:1px solid var(--tcm-border);">
                    <span><i class="bi <?= e($c['icon'] ?? 'bi-journal-code') ?>"></i> <?= e($c['title']) ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($sessions !== []): ?>
            <h3>Live sessions</h3>
            <?php foreach ($sessions as $s): ?>
                <div class="flex-between" style="padding:6px 0;border-bottom:1px solid var(--tcm-border);">
                    <span><i class="bi bi-broadcast" style="color:var(--tcm-accent);"></i> <?= e($s['title']) ?></span>
                    <span class="muted" style="font-size:.8rem;"><?= $s['session_date'] ? e(date('d M', strtotime($s['session_date']))) : '' ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="tcm-card">
        <div class="flex-between">
            <strong style="font-size:1.5rem;"><?= (float)$program['price'] > 0 ? money($program['price']) : 'Free' ?></strong>
            <?php if ($program['original_price']): ?><span class="muted" style="text-decoration:line-through;"><?= money($program['original_price']) ?></span><?php endif; ?>
        </div>
        <?php if ((int)$program['total_seats'] > 0): ?>
            <p class="muted" style="font-size:.85rem;margin-top:10px;"><i class="bi bi-people"></i> <?= (int)$program['seats_left'] ?> of <?= (int)$program['total_seats'] ?> seats left</p>
        <?php endif; ?>
        <div style="margin-top:14px;">
            <?php if ($joined): ?>
                <button class="tcm-btn green" style="width:100%;justify-content:center;background:rgba(0,210,168,.14);color:var(--tcm-accent);" disabled><i class="bi bi-check2"></i> You're in this program</button>
            <?php elseif ($program['type'] === 'internship'): ?>
                <a class="tcm-btn primary" style="width:100%;justify-content:center;" href="<?= base_url('/student/internships/' . $program['id'] . '/apply') ?>">
                    <i class="bi bi-file-earmark-person"></i> Apply Now
                </a>
                <p class="muted" style="font-size:.78rem;text-align:center;margin:10px 0 0;">Submit your resume — our team reviews every application.</p>
            <?php else: ?>
                <form method="post" action="<?= base_url('/student/programs/' . $program['id'] . '/enquire') ?>">
                    <?= csrf_field() ?>
                    <button class="tcm-btn primary" style="width:100%;justify-content:center;">
                        <?php if ((float)$program['price'] > 0): ?>
                            <i class="bi bi-whatsapp"></i> Enquire on WhatsApp
                        <?php else: ?>
                            <i class="bi bi-check2-circle"></i> Join Program
                        <?php endif; ?>
                    </button>
                </form>
                <?php if ((float)$program['price'] > 0): ?>
                    <p class="muted" style="font-size:.78rem;text-align:center;margin:10px 0 0;">We'll confirm your seat and fees over WhatsApp.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
