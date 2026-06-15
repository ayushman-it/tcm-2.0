<div class="tcm-page-head">
    <div><h2>Events</h2><p>Workshops, bootcamps and live sessions.</p></div>
</div>

<div class="tcm-card" style="margin-bottom:18px;">
    <form method="get" class="d-flex gap-8 flex-wrap items-center">
        <input class="tcm-input" name="q" placeholder="Search events..." value="<?= e($_GET['q'] ?? '') ?>" style="flex:1;min-width:180px;">
        <select class="tcm-select" name="status" style="width:150px;" onchange="this.form.submit()">
            <option value="">All status</option>
            <?php foreach (['ongoing','upcoming','past'] as $s): ?>
                <option value="<?= $s ?>" <?= ($_GET['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
        </select>
        <select class="tcm-select" name="type" style="width:130px;" onchange="this.form.submit()">
            <option value="">All types</option>
            <option value="free" <?= ($_GET['type'] ?? '') === 'free' ? 'selected' : '' ?>>Free</option>
            <option value="paid" <?= ($_GET['type'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
        </select>
        <button class="tcm-btn primary">Filter</button>
    </form>
</div>

<div class="grid-cards">
    <?php foreach ($events as $ev): $isJoined = in_array((int)$ev['id'], $joined, true); $seatsLeft = max(0, (int)$ev['total_seats'] - (int)$ev['seats_filled']); ?>
        <div class="tcm-card">
            <div class="flex-between">
                <?php $cls = $ev['status'] === 'ongoing' ? 'green' : ($ev['status'] === 'upcoming' ? 'amber' : 'gray'); ?>
                <span class="tcm-badge <?= $cls ?>"><?= e($ev['status']) ?></span>
                <span class="tcm-badge <?= $ev['type'] === 'free' ? 'green' : 'purple' ?>"><?= e($ev['type']) ?></span>
            </div>
            <h3 style="margin:12px 0 4px;"><?= e($ev['title']) ?></h3>
            <p class="muted" style="font-size:.85rem;min-height:38px;"><?= e($ev['description'] ?? '') ?></p>
            <div class="muted d-flex gap-8 flex-wrap" style="font-size:.8rem;margin-bottom:10px;">
                <span><i class="bi bi-calendar-event"></i> <?= $ev['event_date'] ? e(date('d M Y', strtotime($ev['event_date']))) : 'TBA' ?></span>
                <?php if ($ev['event_time']): ?><span><i class="bi bi-clock"></i> <?= e(date('h:i A', strtotime($ev['event_time']))) ?></span><?php endif; ?>
            </div>
            <?php if ($ev['status'] === 'past'): ?>
                <?php if ($ev['recording_url']): ?>
                    <a class="tcm-btn" style="width:100%;justify-content:center;" target="_blank" href="<?= e($ev['recording_url']) ?>"><i class="bi bi-play-circle"></i> Watch recording</a>
                <?php else: ?>
                    <button class="tcm-btn" style="width:100%;justify-content:center;" disabled>Completed</button>
                <?php endif; ?>
            <?php elseif ($isJoined): ?>
                <button class="tcm-btn green" style="width:100%;justify-content:center;background:rgba(0,210,168,.14);color:var(--tcm-accent);" disabled><i class="bi bi-check2"></i> Registered</button>
            <?php elseif ($seatsLeft <= 0): ?>
                <button class="tcm-btn" style="width:100%;justify-content:center;" disabled>Full</button>
            <?php else: ?>
                <form method="post" action="<?= base_url('/student/events/' . $ev['id'] . '/join') ?>">
                    <?= csrf_field() ?>
                    <button class="tcm-btn primary" style="width:100%;justify-content:center;">
                        <?= $ev['type'] === 'paid' ? 'Register · ' . money($ev['price']) : 'Join Free' ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    <?php if ($events === []): ?><p class="muted">No events found.</p><?php endif; ?>
</div>
