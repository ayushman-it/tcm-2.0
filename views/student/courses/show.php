<?php
$seatsLeft  = max(0, (int)($course['total_seats'] ?? 0) - (int)($course['seats_filled'] ?? 0));
$totalSeats = (int)($course['total_seats'] ?? 0);
$seatPct    = $totalSeats > 0 ? round(($course['seats_filled'] / $totalSeats) * 100) : 0;
$discPct    = ($course['original_price'] ?? 0) > 0
    ? round((1 - (float)$course['price'] / (float)$course['original_price']) * 100) : 0;
?>
<style>
.cds-hero { background:#fff;border:1px solid #ececec;border-radius:16px;padding:28px;margin-bottom:20px; }
.cds-badges { display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px; }
.cds-badge { display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:.75rem;font-weight:600; }
.cds-badge.cat  { background:#f0f4ff;color:#3b5bdb; }
.cds-badge.best { background:#fff3e0;color:#e67700; }
.cds-hero h1 { font-size:1.55rem;font-weight:800;margin:0 0 10px;color:#111;line-height:1.3; }
.cds-hero-desc { color:#555;font-size:.92rem;line-height:1.65;margin-bottom:18px; }
.cds-stats { display:flex;flex-wrap:wrap;margin-bottom:18px; }
.cds-stat { display:flex;flex-direction:column;align-items:center;padding:10px 18px;border-right:1px solid #ececec;text-align:center; }
.cds-stat:first-child { padding-left:0; }
.cds-stat:last-child  { border-right:none; }
.cds-stat strong { font-size:1.05rem;font-weight:800;color:#111; }
.cds-stat small  { font-size:.72rem;color:#888;margin-top:2px; }
.cds-meta { display:flex;gap:10px;flex-wrap:wrap; }
.cds-meta span { display:inline-flex;align-items:center;gap:6px;font-size:.8rem;color:#555;background:#f7f7f7;padding:5px 11px;border-radius:20px;border:1px solid #ececec; }
.cds-meta span i { color:#111; }
.cds-layout { display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start; }
@media(max-width:860px){.cds-layout{grid-template-columns:1fr;}}
.cds-price-card { background:#fff;border:1px solid #ececec;border-radius:16px;padding:22px;position:sticky;top:80px; }
.cds-price-row { display:flex;align-items:baseline;gap:10px;margin-bottom:4px; }
.cds-price { font-size:1.7rem;font-weight:800;color:#111; }
.cds-orig  { font-size:.95rem;color:#aaa;text-decoration:line-through; }
.cds-disc  { font-size:.72rem;font-weight:700;color:#fff;background:#22c55e;padding:2px 8px;border-radius:20px; }
.cds-seats-wrap { margin:14px 0; }
.cds-seats-bar  { height:6px;background:#ececec;border-radius:99px;overflow:hidden;margin-bottom:6px; }
.cds-seats-fill { height:100%;background:#111;border-radius:99px;transition:width .6s ease; }
.cds-seats-txt  { font-size:.78rem;color:#666; }
.cds-enroll-btn { display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:13px;border-radius:10px;background:#111;color:#fff;font-weight:700;font-size:.95rem;text-decoration:none;border:none;cursor:pointer;transition:background .2s,transform .15s;margin-top:14px;font-family:inherit; }
.cds-enroll-btn:hover { background:#333;transform:translateY(-1px);color:#fff; }
.cds-enroll-btn.secondary { background:#fff;color:#111;border:1.5px solid #ececec;margin-top:10px; }
.cds-enroll-btn.secondary:hover { background:#f5f5f5;transform:none; }
.cds-includes { list-style:none;padding:0;margin:14px 0 0; }
.cds-includes li { display:flex;align-items:center;gap:8px;font-size:.82rem;color:#444;padding:5px 0;border-bottom:1px solid #f5f5f5; }
.cds-includes li:last-child { border-bottom:none; }
.cds-includes li i { color:#22c55e;font-size:.9rem; }
.cds-content-card { background:#fff;border:1px solid #ececec;border-radius:16px;padding:24px;margin-bottom:20px; }
.cds-content-card h3 { font-size:1rem;font-weight:700;margin:0 0 14px;color:#111;display:flex;align-items:center;gap:8px; }
.cds-content-card h3 i { color:#888; }
.cds-module { border:1px solid #ececec;border-radius:12px;margin-bottom:10px;overflow:hidden; }
.cds-module summary { display:flex;align-items:center;justify-content:space-between;padding:14px 18px;cursor:pointer;list-style:none;font-weight:600;font-size:.9rem;background:#fafafa;user-select:none; }
.cds-module summary::-webkit-details-marker { display:none; }
.cds-module[open] summary { background:#f5f5f5;border-bottom:1px solid #ececec; }
.cds-module-left { display:flex;align-items:center;gap:12px; }
.cds-module-num  { width:32px;height:32px;border-radius:50%;background:#111;color:#fff;font-size:.75rem;font-weight:700;display:grid;place-items:center;flex-shrink:0; }
.cds-module-meta { font-size:.75rem;color:#888;font-weight:400;margin-top:2px; }
.cds-module-arrow { transition:transform .25s;font-size:.8rem;color:#888; }
.cds-module[open] .cds-module-arrow { transform:rotate(180deg); }
.cds-module-body { padding:6px 18px 14px; }
.cds-lesson { display:flex;align-items:center;gap:10px;padding:7px 0;border-bottom:1px solid #f5f5f5;font-size:.83rem;color:#333; }
.cds-lesson:last-child { border-bottom:none; }
.cds-lesson i { color:#888;flex-shrink:0; }
.cds-lesson span { flex:1; }
.cds-lesson small { font-size:.68rem;background:#f0f0f0;color:#666;padding:2px 7px;border-radius:10px;white-space:nowrap; }
.cds-lesson.project i { color:#f59e0b; }
.cds-lesson.project small { background:#fff3e0;color:#e67700; }
</style>

<div class="tcm-page-head">
    <div><h2><?= e($course['title']) ?></h2><p><?= e($course['category_name'] ?? '') ?></p></div>
    <a class="tcm-btn" href="<?= base_url('/student/courses') ?>"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="cds-hero">
    <div class="cds-badges">
        <?php if (!empty($course['category_name'])): ?>
            <span class="cds-badge cat"><i class="bi bi-mortarboard-fill"></i> <?= e($course['category_name']) ?></span>
        <?php endif; ?>
        <?php if ((int)($course['is_bestseller'] ?? 0) === 1): ?>
            <span class="cds-badge best"><i class="bi bi-fire"></i> Bestseller</span>
        <?php endif; ?>
    </div>
    <h1><?= e($course['title']) ?></h1>
    <p class="cds-hero-desc"><?= e($course['description'] ?? '') ?></p>
    <div class="cds-stats">
        <div class="cds-stat"><strong><?= e($course['rating'] ?? '—') ?></strong><small><i class="bi bi-star-fill" style="color:#f59e0b;"></i> Rating</small></div>
        <div class="cds-stat"><strong><?= number_format((int)($course['students_count'] ?? 0)) ?>+</strong><small>Students</small></div>
        <div class="cds-stat"><strong><?= e($course['duration'] ?? '—') ?></strong><small>Duration</small></div>
        <div class="cds-stat"><strong><?= e(ucfirst($course['level'] ?? 'All')) ?></strong><small>Level</small></div>
    </div>
    <div class="cds-meta">
        <?php if (!empty($course['starts_at'])): ?><span><i class="bi bi-calendar-event"></i> Starts <?= e(date('d M Y', strtotime($course['starts_at']))) ?></span><?php endif; ?>
        <?php if (!empty($course['schedule'])): ?><span><i class="bi bi-clock"></i> <?= e($course['schedule']) ?></span><?php endif; ?>
        <?php if (!empty($course['language'])): ?><span><i class="bi bi-translate"></i> <?= e($course['language']) ?></span><?php endif; ?>
        <?php if ((int)($course['certificate'] ?? 0)): ?><span><i class="bi bi-patch-check-fill"></i> Certificate</span><?php endif; ?>
    </div>
</div>

<div class="cds-layout">
    <div>
        <div class="cds-content-card">
            <h3><i class="bi bi-collection"></i> Curriculum</h3>
            <?php if (!empty($curriculum)): ?>
                <?php foreach ($curriculum as $idx => $mod): ?>
                <details class="cds-module" <?= $idx === 0 ? 'open' : '' ?>>
                    <summary>
                        <div class="cds-module-left">
                            <div class="cds-module-num"><?= str_pad($idx + 1, 2, '0', STR_PAD_LEFT) ?></div>
                            <div>
                                <div><?= e($mod['title']) ?></div>
                                <div class="cds-module-meta"><?= count($mod['lessons'] ?? []) ?> lessons<?php if (!empty($mod['summary'])): ?> · <?= e($mod['summary']) ?><?php endif; ?></div>
                            </div>
                        </div>
                        <i class="bi bi-chevron-down cds-module-arrow"></i>
                    </summary>
                    <div class="cds-module-body">
                        <?php foreach (($mod['lessons'] ?? []) as $lesson): ?>
                            <?php $isProj = ($lesson['type'] ?? '') === 'project'; ?>
                            <div class="cds-lesson <?= $isProj ? 'project' : '' ?>">
                                <i class="bi <?= $isProj ? 'bi-folder2-open' : 'bi-play-circle' ?>"></i>
                                <span><?= e($lesson['title']) ?></span>
                                <small><?= e(ucfirst($lesson['type'] ?? 'live')) ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </details>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color:#888;font-size:.88rem;margin:0;">Curriculum coming soon.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="cds-price-card">
        <div class="cds-price-row">
            <div class="cds-price"><?= money($course['price'] ?? 0) ?></div>
            <?php if (!empty($course['original_price']) && (float)$course['original_price'] > 0): ?>
                <div class="cds-orig"><?= money($course['original_price']) ?></div>
                <?php if ($discPct > 0): ?><div class="cds-disc"><?= $discPct ?>% Off</div><?php endif; ?>
            <?php endif; ?>
        </div>
        <?php if ($totalSeats > 0): ?>
        <div class="cds-seats-wrap">
            <div class="cds-seats-bar"><div class="cds-seats-fill" style="width:<?= $seatPct ?>%"></div></div>
            <div class="cds-seats-txt"><strong><?= $seatsLeft ?></strong> seats left out of <?= $totalSeats ?></div>
        </div>
        <?php endif; ?>
        <?php if ($enrolled): ?>
            <a class="cds-enroll-btn" href="<?= base_url('/student/learn/' . (int)$course['id']) ?>"><i class="bi bi-play-fill"></i> Continue Learning</a>
        <?php else: ?>
            <form method="post" action="<?= base_url('/student/courses/' . (int)$course['id'] . '/buy') ?>">
                <?= csrf_field() ?>
                <button type="submit" class="cds-enroll-btn"><i class="bi bi-cart-check"></i> <?= (float)($course['price'] ?? 0) > 0 ? 'Buy Now' : 'Enroll Free' ?></button>
            </form>
        <?php endif; ?>
        <a class="cds-enroll-btn secondary" href="<?= base_url('/student/courses') ?>"><i class="bi bi-arrow-left"></i> Browse More</a>
        <ul class="cds-includes" style="margin-top:18px;">
            <?php if (!empty($course['duration'])): ?><li><i class="bi bi-clock-fill"></i> <?= e($course['duration']) ?> of content</li><?php endif; ?>
            <?php if ((int)($course['certificate'] ?? 0)): ?><li><i class="bi bi-patch-check-fill"></i> Completion certificate</li><?php endif; ?>
            <?php if (!empty($course['language'])): ?><li><i class="bi bi-translate"></i> <?= e($course['language']) ?></li><?php endif; ?>
            <li><i class="bi bi-infinity"></i> Lifetime access</li>
        </ul>
    </div>
</div>
