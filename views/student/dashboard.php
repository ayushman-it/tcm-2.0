<?php
$firstName = explode(' ', $user['name'])[0];
$h         = (int) date('H');
$greeting  = $h < 12 ? 'Good morning' : ($h < 17 ? 'Good afternoon' : 'Good evening');
$pct       = (int) $portfolioStrength;
$dash      = round(15.9 * 2 * M_PI * $pct / 100, 2);
$gap       = round(15.9 * 2 * M_PI - $dash, 2);
$avatarSrc = !empty($user['avatar']) ? base_url('/uploads/' . $user['avatar']) : null;
?>

<!-- Welcome banner -->
<div class="tcm-welcome">
    <div class="flex-between flex-wrap gap-12">
        <div class="d-flex items-center gap-12 flex-wrap">

            <!-- Avatar widget — click to edit profile -->
            <a href="<?= base_url('/student/profile') ?>" title="Edit profile"
               style="text-decoration:none;flex-shrink:0;position:relative;display:inline-block;">
                <div style="width:56px;height:56px;border-radius:50%;overflow:hidden;
                            border:2px solid #e0e0e0;box-shadow:0 2px 10px rgba(0,0,0,.09);
                            display:grid;place-items:center;
                            background:<?= $avatarSrc ? '#f5f5f5' : '#111' ?>;
                            transition:transform .2s,box-shadow .2s;"
                     onmouseover="this.style.transform='scale(1.07)';this.style.boxShadow='0 4px 16px rgba(0,0,0,.15)'"
                     onmouseout="this.style.transform='scale(1)';this.style.boxShadow='0 2px 10px rgba(0,0,0,.09)'">
                    <?php if ($avatarSrc): ?>
                        <img src="<?= e($avatarSrc) ?>" alt="<?= e($user['name']) ?>"
                             style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    <?php else: ?>
                        <span style="font-size:1.3rem;font-weight:800;color:#fff;">
                            <?= e(strtoupper(substr($user['name'], 0, 1))) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <span style="position:absolute;bottom:0;right:0;width:18px;height:18px;border-radius:50%;
                             background:#fff;border:1.5px solid #e0e0e0;color:#111;
                             display:grid;place-items:center;font-size:.55rem;
                             box-shadow:0 1px 4px rgba(0,0,0,.12);">
                    <i class="bi bi-pencil-fill"></i>
                </span>
            </a>

            <div>
                <div class="tcm-welcome-tag">
                    <span class="tcm-welcome-tag-dot"></span>
                    <?= date('l, d M Y') ?>
                </div>
                <h2><?= e($greeting) ?>, <?= e($firstName) ?> 👋</h2>
                <p class="mb-0">Keep building — your next milestone is just a lesson away.</p>
            </div>
        </div>

        <div class="d-flex gap-6 flex-wrap" style="align-items:center;">
            <a href="<?= base_url('/student/courses') ?>" class="tcm-btn primary">
                <i class="bi bi-journal-code"></i> Browse Courses
            </a>
            <a href="<?= base_url('/student/programs') ?>" class="tcm-btn">
                <i class="bi bi-stack"></i> Programs
            </a>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="tcm-stat-grid">
    <div class="tcm-stat"><i class="bi bi-journal-code icon"></i><div class="label">Enrolled</div><div class="value"><?= count($enrollments) ?></div></div>
    <div class="tcm-stat"><i class="bi bi-calendar-check icon"></i><div class="label">Events</div><div class="value"><?= count($registrations) ?></div></div>
    <div class="tcm-stat"><i class="bi bi-patch-check icon"></i><div class="label">Certificates</div><div class="value"><?= count($certificates) ?></div></div>
    <div class="tcm-stat">
        <i class="bi bi-graph-up-arrow icon"></i>
        <div class="label">Portfolio</div>
        <div class="value"><?= $pct ?>%</div>
        <div class="tcm-progress thick" style="margin-top:10px;"><span style="width:<?= $pct ?>%"></span></div>
    </div>
</div>

<!-- 2-col grid -->
<div class="tcm-grid-2" style="margin-top:16px;align-items:start;">

    <!-- My Courses -->
    <div class="tcm-card">
        <div class="tcm-card-head">
            <h3><i class="bi bi-journal-code"></i> My Courses</h3>
            <a href="<?= base_url('/student/courses') ?>" class="tcm-btn ghost sm">Browse <i class="bi bi-arrow-right"></i></a>
        </div>
        <?php if ($enrollments === []): ?>
            <div class="tcm-empty"><i class="bi bi-journal-x"></i>No courses enrolled yet.<br><a href="<?= base_url('/student/courses') ?>">Explore courses →</a></div>
        <?php else: ?>
            <?php foreach ($enrollments as $en): ?>
            <div class="tcm-row">
                <div class="tcm-row-main">
                    <div class="tcm-row-title"><i class="bi <?= e($en['icon'] ?? 'bi-journal-code') ?>"></i><?= e($en['title']) ?></div>
                    <div class="tcm-progress" style="margin-top:7px;"><span style="width:<?= (int)$en['progress'] ?>%"></span></div>
                    <div class="tcm-row-sub"><?= (int)$en['progress'] ?>% complete</div>
                </div>
                <a class="tcm-btn primary sm" href="<?= base_url('/student/learn/'.$en['course_id']) ?>">Continue</a>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Live Sessions -->
    <div class="tcm-card">
        <div class="tcm-card-head">
            <h3><i class="bi bi-broadcast"></i> Live Sessions</h3>
            <a href="<?= base_url('/student/programs') ?>" class="tcm-btn ghost sm">Programs <i class="bi bi-arrow-right"></i></a>
        </div>
        <?php if (empty($liveSessions)): ?>
            <div class="tcm-empty"><i class="bi bi-broadcast"></i>No sessions scheduled.<br><a href="<?= base_url('/student/programs') ?>">Join a program →</a></div>
        <?php else: ?>
            <?php foreach ($liveSessions as $ls): ?>
            <div class="tcm-row">
                <div class="tcm-row-main">
                    <div class="tcm-row-title">
                        <?php if ($ls['status'] === 'live'): ?><span class="tcm-badge green" style="font-size:.6rem;">● LIVE</span><?php endif; ?>
                        <?= e($ls['title']) ?>
                    </div>
                    <div class="tcm-row-sub"><?= e($ls['program_title'] ?? $ls['course_title'] ?? '') ?></div>
                </div>
                <div class="tcm-row-meta">
                    <?= $ls['session_date'] ? e(date('d M', strtotime($ls['session_date']))) : '' ?><br>
                    <?= $ls['start_time']   ? e(date('h:i A', strtotime($ls['start_time'])))  : '' ?>
                    <?php if (!empty($ls['meeting_url']) && $ls['status'] === 'live'): ?>
                        <a class="tcm-btn primary sm" href="<?= e($ls['meeting_url']) ?>" target="_blank" style="margin-top:6px;display:inline-flex;"><i class="bi bi-camera-video"></i> Join</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Upcoming Events -->
    <div class="tcm-card">
        <div class="tcm-card-head">
            <h3><i class="bi bi-calendar-event"></i> Upcoming Events</h3>
            <a href="<?= base_url('/student/events') ?>" class="tcm-btn ghost sm">All <i class="bi bi-arrow-right"></i></a>
        </div>
        <?php $shown = 0; foreach ($registrations as $r): if ($r['event_status'] === 'past') continue; $shown++; ?>
        <div class="tcm-row">
            <div class="tcm-row-title" style="flex:1;min-width:0;">
                <i class="bi bi-circle" style="font-size:.5rem;flex-shrink:0;"></i>
                <?= e($r['title']) ?>
            </div>
            <div class="tcm-row-meta"><?= $r['event_date'] ? e(date('d M', strtotime($r['event_date']))) : '' ?></div>
        </div>
        <?php endforeach; ?>
        <?php if ($shown === 0): ?>
            <div class="tcm-empty"><i class="bi bi-calendar-x"></i>No upcoming events.<br><a href="<?= base_url('/student/events') ?>">Find events →</a></div>
        <?php endif; ?>
    </div>

    <!-- Portfolio -->
    <div class="tcm-card">
        <div class="tcm-card-head">
            <h3><i class="bi bi-briefcase"></i> Portfolio</h3>
            <a href="<?= base_url('/student/portfolio') ?>" class="tcm-btn ghost sm">Edit <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="d-flex items-center gap-12" style="padding:6px 0 16px;">
            <div style="position:relative;width:72px;height:72px;flex-shrink:0;">
                <svg viewBox="0 0 36 36" width="72" height="72" style="transform:rotate(-90deg);">
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#ececec" stroke-width="2.5"/>
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#111" stroke-width="2.5"
                            stroke-dasharray="<?= $dash ?> <?= $gap ?>" stroke-linecap="round"/>
                </svg>
                <div style="position:absolute;inset:0;display:grid;place-items:center;font-size:.82rem;font-weight:800;color:#111;">
                    <?= $pct ?>%
                </div>
            </div>
            <div>
                <div style="font-size:.9rem;font-weight:700;color:#111;">
                    <?= $pct >= 80 ? 'Strong profile!' : ($pct >= 40 ? 'Keep going' : 'Just starting') ?>
                </div>
                <div class="muted" style="font-size:.8rem;margin-top:3px;line-height:1.5;">
                    <?= $pct < 100 ? 'Add projects, skills &amp; achievements<br>to boost your score.' : 'Your portfolio is complete 🎉' ?>
                </div>
            </div>
        </div>
        <div class="tcm-divider"></div>
        <div class="d-flex gap-8 flex-wrap" style="margin-top:14px;">
            <a href="<?= base_url('/student/portfolio') ?>" class="tcm-btn primary sm"><i class="bi bi-plus-lg"></i> Add Project</a>
            <a href="<?= base_url('/portfolio/'.($user['id']??0)) ?>" target="_blank" class="tcm-btn sm"><i class="bi bi-box-arrow-up-right"></i> View Public</a>
        </div>
    </div>

</div>
