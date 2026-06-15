<div class="tcm-page-head">
    <div><h2>Leads</h2><p>Enquiries captured from the site, dashboard and WhatsApp CTAs.</p></div>
</div>

<div class="tcm-stat-grid" style="margin-bottom:18px;">
    <div class="tcm-stat"><i class="bi bi-stars icon"></i><div class="label">New</div><div class="value"><?= (int)$counts['new'] ?></div></div>
    <div class="tcm-stat"><i class="bi bi-chat-dots icon"></i><div class="label">Contacted</div><div class="value"><?= (int)$counts['contacted'] ?></div></div>
    <div class="tcm-stat"><i class="bi bi-check2-circle icon"></i><div class="label">Converted</div><div class="value"><?= (int)$counts['converted'] ?></div></div>
</div>

<div class="tcm-card">
    <form method="get" class="d-flex gap-8 flex-wrap" style="margin-bottom:16px;">
        <input class="tcm-input" name="q" placeholder="Search name / email / phone..." value="<?= e($_GET['q'] ?? '') ?>" style="flex:1;min-width:200px;">
        <select class="tcm-select" name="status" style="width:160px;" onchange="this.form.submit()">
            <option value="">All status</option>
            <?php foreach (['new','contacted','converted','lost'] as $s): ?>
                <option value="<?= $s ?>" <?= ($_GET['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="tcm-btn primary">Filter</button>
    </form>

    <table class="tcm-table">
        <thead><tr><th>Contact</th><th>Interest</th><th>Source</th><th>Status</th><th>When</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($leads as $l): ?>
            <tr>
                <td>
                    <strong><?= e($l['name']) ?></strong><br>
                    <span class="muted" style="font-size:.78rem;">
                        <?= e($l['phone'] ?? '') ?><?= $l['phone'] && $l['email'] ? ' · ' : '' ?><?= e($l['email'] ?? '') ?>
                    </span>
                </td>
                <td>
                    <span class="tcm-badge purple"><?= e($l['interest_type']) ?></span>
                    <div class="muted" style="font-size:.8rem;"><?= e($l['interest_title'] ?? '—') ?></div>
                </td>
                <td class="muted" style="font-size:.8rem;"><?= e($l['source']) ?></td>
                <td>
                    <?php $cls = ['new'=>'amber','contacted'=>'purple','converted'=>'green','lost'=>'red'][$l['status']] ?? 'gray'; ?>
                    <span class="tcm-badge <?= $cls ?>"><?= e($l['status']) ?></span>
                </td>
                <td class="muted" style="font-size:.8rem;"><?= e(date('d M', strtotime($l['created_at']))) ?></td>
                <td>
                    <div class="d-flex gap-8 flex-wrap">
                        <?php if (!empty($l['wa_link'])): ?>
                            <a class="tcm-btn sm" target="_blank" href="<?= e($l['wa_link']) ?>" title="Reply on WhatsApp"><i class="bi bi-whatsapp" style="color:#25d366;"></i></a>
                        <?php endif; ?>
                        <?php if ($l['status'] !== 'contacted'): ?>
                        <form method="post" action="<?= base_url('/admin/leads/' . $l['id'] . '/status') ?>">
                            <?= csrf_field() ?><input type="hidden" name="status" value="contacted">
                            <button class="tcm-btn sm" title="Mark contacted"><i class="bi bi-chat-dots"></i></button>
                        </form>
                        <?php endif; ?>
                        <?php if ($l['status'] !== 'converted' && $l['interest_type'] !== 'contact' && $l['interest_type'] !== 'general'): ?>
                        <form method="post" action="<?= base_url('/admin/leads/' . $l['id'] . '/convert') ?>" onsubmit="return confirm('Convert lead and enrol the student? (payment settled offline)');">
                            <?= csrf_field() ?>
                            <button class="tcm-btn sm primary" title="Convert & enrol"><i class="bi bi-check2"></i></button>
                        </form>
                        <?php endif; ?>
                        <form method="post" action="<?= base_url('/admin/leads/' . $l['id'] . '/delete') ?>">
                            <?= csrf_field() ?>
                            <button class="tcm-btn sm danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if ($leads === []): ?><tr><td colspan="6" class="muted">No leads yet.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
