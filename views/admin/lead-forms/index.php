<div class="tcm-page-head">
    <div>
        <h2>Lead Forms</h2>
        <p>Shareable capture pages for events, courses and campaigns.</p>
    </div>
    <a href="<?= base_url('/admin/lead-forms/create') ?>" class="tcm-btn primary">
        <i class="bi bi-plus-lg"></i> New Form
    </a>
</div>

<?php if (empty($forms)): ?>
    <div class="tcm-card">
        <div class="tcm-empty">
            <i class="bi bi-link-45deg"></i>
            No lead forms yet.<br>
            Create your first shareable form to start capturing leads.
            <br><br>
            <a href="<?= base_url('/admin/lead-forms/create') ?>" class="tcm-btn primary sm">
                <i class="bi bi-plus-lg"></i> Create First Form
            </a>
        </div>
    </div>
<?php else: ?>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:14px;">
    <?php foreach ($forms as $f): ?>
    <div class="tcm-card tcm-card-hover">

        <!-- Header -->
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:12px;">
            <div>
                <div style="font-weight:700;font-size:.9rem;color:#111;"><?= e($f['title']) ?></div>
                <div style="font-size:.74rem;color:var(--muted);margin-top:2px;">
                    <span class="tcm-badge <?= $f['context_type'] === 'event' ? 'amber' : ($f['context_type'] === 'course' ? 'purple' : 'gray') ?>"
                          style="font-size:.62rem;">
                        <?= e($f['context_type']) ?>
                    </span>
                    <?php if ($f['context_title']): ?>
                        · <?= e(mb_strimwidth($f['context_title'], 0, 30, '…')) ?>
                    <?php endif; ?>
                </div>
            </div>
            <span class="tcm-badge <?= $f['status'] === 'active' ? 'green' : 'gray' ?>">
                <?= e($f['status']) ?>
            </span>
        </div>

        <!-- Stats -->
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;
                    background:#f9f9f9;border-radius:9px;padding:10px 12px;margin-bottom:14px;">
            <div style="text-align:center;">
                <div style="font-size:1.1rem;font-weight:800;color:#111;"><?= number_format($f['views']) ?></div>
                <div style="font-size:.68rem;color:var(--muted);">Views</div>
            </div>
            <div style="text-align:center;border-left:1px solid #ececec;">
                <div style="font-size:1.1rem;font-weight:800;color:#111;"><?= number_format($f['submissions']) ?></div>
                <div style="font-size:.68rem;color:var(--muted);">Leads</div>
            </div>
            <div style="text-align:center;border-left:1px solid #ececec;">
                <div style="font-size:1.1rem;font-weight:800;color:#111;">
                    <?= $f['views'] > 0 ? round($f['submissions'] / $f['views'] * 100) : 0 ?>%
                </div>
                <div style="font-size:.68rem;color:var(--muted);">Conv.</div>
            </div>
        </div>

        <!-- Share URL -->
        <?php $shareUrl = base_url('/form/' . $f['slug']); ?>
        <div style="display:flex;align-items:center;gap:6px;background:#f5f5f5;
                    border:1px solid #e5e5e5;border-radius:8px;padding:7px 10px;margin-bottom:12px;">
            <i class="bi bi-link-45deg" style="color:var(--muted);font-size:.82rem;"></i>
            <span style="font-size:.74rem;color:#555;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                /form/<?= e($f['slug']) ?>
            </span>
            <button onclick="copyLink('<?= e($shareUrl) ?>', this)"
                    class="tcm-btn sm" style="padding:3px 8px;font-size:.7rem;">
                <i class="bi bi-copy"></i>
            </button>
            <a href="<?= $shareUrl ?>" target="_blank"
               class="tcm-btn sm" style="padding:3px 8px;font-size:.7rem;">
                <i class="bi bi-box-arrow-up-right"></i>
            </a>
        </div>

        <!-- Actions -->
        <div style="display:flex;gap:8px;">
            <a href="<?= base_url('/admin/lead-forms/' . $f['id'] . '/edit') ?>"
               class="tcm-btn sm" style="flex:1;justify-content:center;">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="<?= $shareUrl ?>" target="_blank"
               class="tcm-btn sm primary" style="flex:1;justify-content:center;">
                <i class="bi bi-share"></i> Share
            </a>
            <form method="post"
                  action="<?= base_url('/admin/lead-forms/' . $f['id'] . '/delete') ?>"
                  onsubmit="return confirm('Delete this form?')">
                <?= csrf_field() ?>
                <button class="tcm-btn sm danger" style="padding:5px 10px;">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php endif; ?>

<script>
function copyLink(url, btn) {
    navigator.clipboard.writeText(url).then(function() {
        var orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check2"></i>';
        btn.style.color = '#16a34a';
        setTimeout(function() { btn.innerHTML = orig; btn.style.color = ''; }, 2000);
    });
}
</script>
