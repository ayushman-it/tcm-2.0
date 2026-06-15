<?php use TCM\Models\InternshipApplication; ?>
<div class="tcm-page-head">
    <div><h2><?= e($app['full_name']) ?></h2><p>Application for <?= e($app['program_title']) ?></p></div>
    <a class="tcm-btn" href="<?= base_url('/admin/internships') ?>"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="tcm-grid-2" style="align-items:start;">
    <div class="tcm-card">
        <h3 class="mt-0">Applicant details</h3>
        <p><strong>Email:</strong> <?= e($app['email']) ?></p>
        <p><strong>Phone:</strong> <?= e($app['phone'] ?? '—') ?></p>
        <p><strong>College:</strong> <?= e($app['college'] ?? '—') ?></p>
        <p><strong>Skills:</strong> <?= e($app['skills'] ?? '—') ?></p>
        <p><strong>Portfolio:</strong>
            <?php if ($app['portfolio_url']): ?><a href="<?= e($app['portfolio_url']) ?>" target="_blank"><?= e($app['portfolio_url']) ?></a><?php else: ?>—<?php endif; ?>
        </p>
        <p class="mb-0"><strong>Motivation:</strong></p>
        <p class="muted"><?= nl2br(e($app['why'] ?? '')) ?></p>

        <?php if ($app['resume_file']): ?>
            <a class="tcm-btn primary" href="<?= base_url('/admin/internships/' . $app['id'] . '/resume') ?>">
                <i class="bi bi-download"></i> Download résumé
            </a>
        <?php else: ?>
            <p class="muted">No résumé attached.</p>
        <?php endif; ?>
    </div>

    <form method="post" action="<?= base_url('/admin/internships/' . $app['id']) ?>" class="tcm-card">
        <?= csrf_field() ?>
        <h3 class="mt-0">Review</h3>
        <div class="tcm-field">
            <label>Status</label>
            <select class="tcm-select" name="status">
                <?php foreach (InternshipApplication::STATUSES as $k => $lbl): ?>
                    <option value="<?= $k ?>" <?= $app['status'] === $k ? 'selected' : '' ?>><?= e($lbl) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="tcm-field">
            <label>Internal notes</label>
            <textarea class="tcm-textarea" name="notes" placeholder="Notes for your team..."><?= e($app['notes'] ?? '') ?></textarea>
        </div>
        <button class="tcm-btn primary"><i class="bi bi-check2"></i> Save Review</button>
        <?php if ($app['reviewed_at']): ?>
            <p class="muted" style="font-size:.8rem;margin:10px 0 0;">Last reviewed <?= e(date('d M Y H:i', strtotime($app['reviewed_at']))) ?></p>
        <?php endif; ?>
    </form>
</div>
