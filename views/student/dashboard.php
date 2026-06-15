<div class="tcm-card" style="background:linear-gradient(135deg, rgba(108,92,231,.25), rgba(0,210,168,.12));">
    <h2 class="mt-0">Welcome back, <?= e(explode(' ', $user['name'])[0]) ?> 👋</h2>
    <p class="muted mb-0">Keep building. Your next milestone is just a lesson away.</p>
</div>

<div class="tcm-stat-grid" style="margin-top:18px;">
    <div class="tcm-stat"><i class="bi bi-journal-code icon"></i><div class="label">Enrolled courses</div><div class="value"><?= count($enrollments) ?></div></div>
    <div class="tcm-stat"><i class="bi bi-calendar-check icon"></i><div class="label">Events joined</div><div class="value"><?= count($registrations) ?></div></div>
    <div class="tcm-stat"><i class="bi bi-patch-check icon"></i><div class="label">Certificates</div><div class="value"><?= count($certificates) ?></div></div>
    <div class="tcm-stat">
        <i class="bi bi-graph-up-arrow icon"></i>
        <div class="label">Portfolio strength</div>
        <div class="value"><?= (int)$portfolioStrength ?>%</div>
        <div class="tcm-progress" style="margin-top:8px;"><span style="width:<?= (int)$portfolioStrength ?>%"></span></div>
    </div>
</div>

<div class="tcm-grid-2" style="margin-top:18px;align-items:start;">
    <div class="tcm-card">
        <div class="flex-between" style="margin-bottom:8px;">
            <h3 class="mt-0 mb-0">My courses</h3>
            <a href="<?= base_url('/student/courses') ?>" style="font-size:.85rem;">Browse more</a>
        </div>
        <?php foreach ($enrollments as $en): ?>
            <div style="padding:10px 0;border-bottom:1px solid var(--tcm-border);">
                <div class="flex-between">
                    <span><i class="bi <?= e($en['icon'] ?? 'bi-journal-code') ?>"></i> <?= e($en['title']) ?></span>
                    <a class="tcm-btn sm" href="<?= base_url('/student/learn/' . $en['course_id']) ?>">Continue</a>
                </div>
                <div class="tcm-progress" style="margin-top:8px;"><span style="width:<?= (int)$en['progress'] ?>%"></span></div>
                <span class="muted" style="font-size:.78rem;"><?= (int)$en['progress'] ?>% complete</span>
            </div>
        <?php endforeach; ?>
        <?php if ($enrollments === []): ?>
            <p class="muted">You haven't enrolled in any course yet.
                <a href="<?= base_url('/student/courses') ?>">Explore courses →</a></p>
        <?php endif; ?>
    </div>

    <div class="tcm-card">
        <div class="flex-between" style="margin-bottom:8px;">
            <h3 class="mt-0 mb-0">Upcoming live sessions</h3>
            <a href="<?= base_url('/student/programs') ?>" style="font-size:.85rem;">Programs</a>
        </div>
        <?php foreach (($liveSessions ?? []) as $ls): ?>
            <div class="flex-between" style="padding:10px 0;border-bottom:1px solid var(--tcm-border);">
                <div>
                    <span><i class="bi bi-broadcast" style="color:var(--tcm-accent);"></i> <?= e($ls['title']) ?></span>
                    <div class="muted" style="font-size:.78rem;"><?= e($ls['program_title'] ?? $ls['course_title'] ?? '') ?></div>
                </div>
                <div style="text-align:right;">
                    <div class="muted" style="font-size:.8rem;">
                        <?= $ls['session_date'] ? e(date('d M', strtotime($ls['session_date']))) : '' ?>
                        <?= $ls['start_time'] ? e(date('h:i A', strtotime($ls['start_time']))) : '' ?>
                    </div>
                    <?php if (!empty($ls['meeting_url']) && $ls['status'] === 'live'): ?>
                        <a class="tcm-btn sm primary" target="_blank" href="<?= e($ls['meeting_url']) ?>">Join</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (($liveSessions ?? []) === []): ?>
            <p class="muted">No live sessions scheduled. <a href="<?= base_url('/student/programs') ?>">Join a program →</a></p>
        <?php endif; ?>
    </div>

    <div class="tcm-card">
        <div class="flex-between" style="margin-bottom:8px;">
            <h3 class="mt-0 mb-0">Upcoming events</h3>
            <a href="<?= base_url('/student/events') ?>" style="font-size:.85rem;">All events</a>
        </div>
        <?php $shown = 0; foreach ($registrations as $r): if ($r['event_status'] === 'past') continue; $shown++; ?>
            <div class="flex-between" style="padding:10px 0;border-bottom:1px solid var(--tcm-border);">
                <span><?= e($r['title']) ?></span>
                <span class="muted" style="font-size:.8rem;"><?= $r['event_date'] ? e(date('d M', strtotime($r['event_date']))) : '' ?></span>
            </div>
        <?php endforeach; ?>
        <?php if ($shown === 0): ?>
            <p class="muted">No upcoming events. <a href="<?= base_url('/student/events') ?>">Join one →</a></p>
        <?php endif; ?>
    </div>
</div>
