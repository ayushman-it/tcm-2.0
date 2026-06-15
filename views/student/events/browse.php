<style>
.evb-filter-bar {
    background: #fff; border: 1px solid #ececec; border-radius: 14px;
    padding: 16px 20px; margin-bottom: 22px;
    display: flex; gap: 10px; flex-wrap: wrap; align-items: center;
}
.evb-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 18px; }
.evb-card {
    background: #fff; border: 1px solid #ececec; border-radius: 16px;
    overflow: hidden; display: flex; flex-direction: column;
    transition: box-shadow .2s, transform .2s;
}
.evb-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.07); transform: translateY(-2px); }
.evb-card-top { padding: 14px 18px 12px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f5f5f5; }
.evb-status { display: inline-flex; align-items: center; gap: 5px; font-size: .72rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; }
.evb-status.ongoing  { background: #dcfce7; color: #16a34a; }
.evb-status.upcoming { background: #fff3e0; color: #d97706; }
.evb-status.past     { background: #f3f4f6; color: #6b7280; }
.evb-status-dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; animation: evbpulse 1.2s infinite; }
@keyframes evbpulse { 0%,100%{opacity:1} 50%{opacity:.35} }
.evb-type { font-size: .7rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; }
.evb-type.free { background: #f0fdf4; color: #16a34a; }
.evb-type.paid { background: #faf5ff; color: #7c3aed; }
.evb-card-body { padding: 14px 18px; flex: 1; }
.evb-card-body h3 { font-size: .95rem; font-weight: 700; color: #111; margin: 0 0 6px; }
.evb-card-body p  { font-size: .8rem; color: #666; line-height: 1.55; margin: 0 0 12px; }
.evb-meta { display: flex; flex-wrap: wrap; gap: 7px; margin-bottom: 14px; }
.evb-meta span { display: inline-flex; align-items: center; gap: 5px; font-size: .75rem; color: #555; background: #f7f7f7; padding: 4px 10px; border-radius: 20px; }
.evb-meta span i { color: #111; }
.evb-dots { display: flex; flex-wrap: wrap; gap: 5px; margin-bottom: 8px; }
.evb-dot  { width: 11px; height: 11px; border-radius: 50%; background: #ececec; }
.evb-dot.filled { background: #111; }
.evb-seats-txt  { font-size: .75rem; color: #888; margin-bottom: 12px; }
.evb-card-foot { padding: 0 18px 16px; }
.evb-btn { display: flex; align-items: center; justify-content: center; gap: 7px; width: 100%; padding: 10px; border-radius: 10px; font-size: .85rem; font-weight: 700; border: none; cursor: pointer; text-decoration: none; transition: background .2s, transform .15s; font-family: inherit; }
.evb-btn.primary    { background: #111; color: #fff; }
.evb-btn.primary:hover { background: #333; transform: translateY(-1px); color: #fff; }
.evb-btn.registered { background: #dcfce7; color: #16a34a; cursor: default; }
.evb-btn.disabled   { background: #f3f4f6; color: #9ca3af; cursor: default; }
.evb-btn.recording  { background: #f0f4ff; color: #3b5bdb; border: 1px solid #dbe4ff; }
.evb-btn.recording:hover { background: #e0e7ff; color: #3b5bdb; }
.evb-empty { grid-column: 1/-1; text-align: center; padding: 48px 20px; color: #aaa; font-size: .9rem; }
.evb-empty i { font-size: 2.4rem; display: block; margin-bottom: 12px; }
</style>

<div class="tcm-page-head">
    <div><h2>Events</h2><p>Workshops, bootcamps and live sessions open for you.</p></div>
</div>

<div class="evb-filter-bar">
    <form method="get" style="display:contents;">
        <input class="tcm-input" name="q" placeholder="Search events…"
               value="<?= e($_GET['q'] ?? '') ?>" style="flex:1;min-width:170px;">
        <select class="tcm-select" name="status" style="width:150px;" onchange="this.form.submit()">
            <option value="">All Status</option>
            <?php foreach (['ongoing','upcoming','past'] as $s): ?>
                <option value="<?= $s ?>" <?= ($_GET['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
        </select>
        <select class="tcm-select" name="type" style="width:130px;" onchange="this.form.submit()">
            <option value="">All Types</option>
            <option value="free" <?= ($_GET['type'] ?? '') === 'free' ? 'selected' : '' ?>>Free</option>
            <option value="paid" <?= ($_GET['type'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
        </select>
        <button class="tcm-btn primary">Filter</button>
    </form>
</div>

<div class="evb-grid">
    <?php if (empty($events)): ?>
        <div class="evb-empty"><i class="bi bi-calendar-x"></i>No events found. Try adjusting your filters.</div>
    <?php endif; ?>

    <?php foreach ($events as $ev):
        $isJoined  = in_array((int)$ev['id'], $joined, true);
        $total     = max(1, (int)$ev['total_seats']);
        $filled    = (int)$ev['seats_filled'];
        $seatsLeft = max(0, $total - $filled);
        $isFull    = $seatsLeft <= 0;
        $dotCount  = min($total, 20);
        $dotFilled = (int)round(($filled / $total) * $dotCount);
    ?>
    <div class="evb-card">
        <div class="evb-card-top">
            <span class="evb-status <?= $ev['status'] ?>">
                <?php if ($ev['status'] === 'ongoing'): ?><span class="evb-status-dot"></span>
                <?php else: ?><i class="bi <?= $ev['status']==='past' ? 'bi-check-circle' : 'bi-calendar-check' ?>"></i><?php endif; ?>
                <?= ucfirst($ev['status']) ?>
            </span>
            <span class="evb-type <?= $ev['type'] === 'paid' ? 'paid' : 'free' ?>">
                <?= $ev['type'] === 'paid' ? '<i class="bi bi-currency-rupee"></i> Paid' : '<i class="bi bi-gift"></i> Free' ?>
            </span>
        </div>
        <div class="evb-card-body">
            <h3><?= e($ev['title']) ?></h3>
            <?php if (!empty($ev['description'])): ?>
                <p><?= e(mb_strimwidth($ev['description'], 0, 110, '…')) ?></p>
            <?php endif; ?>
            <div class="evb-meta">
                <span><i class="bi bi-calendar-event"></i> <?= $ev['event_date'] ? e(date('d M Y', strtotime($ev['event_date']))) : 'TBA' ?></span>
                <?php if ($ev['event_time']): ?><span><i class="bi bi-clock"></i> <?= e(date('h:i A', strtotime($ev['event_time']))) ?></span><?php endif; ?>
                <?php if (!empty($ev['mode'])): ?><span><i class="bi bi-laptop"></i> <?= e(ucfirst($ev['mode'])) ?></span><?php endif; ?>
            </div>
            <?php if ($ev['status'] !== 'past'): ?>
            <div class="evb-dots">
                <?php for ($d = 0; $d < $dotCount; $d++): ?>
                    <span class="evb-dot <?= $d < $dotFilled ? 'filled' : '' ?>"></span>
                <?php endfor; ?>
            </div>
            <div class="evb-seats-txt"><strong><?= $filled ?></strong> / <?= $total ?> seats filled<?= !$isFull ? ' · <strong>'.$seatsLeft.'</strong> left' : '' ?></div>
            <?php endif; ?>
        </div>
        <div class="evb-card-foot">
            <?php if ($ev['status'] === 'past'): ?>
                <?php if (!empty($ev['recording_url'])): ?>
                    <a class="evb-btn recording" href="<?= e($ev['recording_url']) ?>" target="_blank"><i class="bi bi-play-circle"></i> Watch Recording</a>
                <?php else: ?>
                    <button class="evb-btn disabled" disabled><i class="bi bi-check2-circle"></i> Completed</button>
                <?php endif; ?>
            <?php elseif ($isJoined): ?>
                <button class="evb-btn registered" disabled><i class="bi bi-check2"></i> Registered</button>
            <?php elseif ($isFull): ?>
                <button class="evb-btn disabled" disabled><i class="bi bi-people-fill"></i> Event Full</button>
            <?php else: ?>
                <form method="post" action="<?= base_url('/student/events/' . (int)$ev['id'] . '/join') ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="evb-btn primary">
                        <?= $ev['type'] === 'paid' ? '<i class="bi bi-cart-check"></i> Register · ' . money((float)($ev['price'] ?? 0)) : '<i class="bi bi-calendar-plus"></i> Join Free' ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
