<div style="text-align:center;padding:30px 0;">
    <div class="tcm-avatar" style="width:84px;height:84px;font-size:2rem;margin:0 auto 14px;">
        <?= e(strtoupper(substr($owner['name'], 0, 1))) ?>
    </div>
    <h1 style="margin:0;"><?= e($owner['name']) ?></h1>
    <p class="muted" style="margin:6px 0;"><?= e($profile['headline'] ?? 'Learner at The Code Munk') ?></p>
    <div class="d-flex gap-8" style="justify-content:center;font-size:1.3rem;margin-top:8px;">
        <?php if (!empty($profile['github_url'])): ?><a href="<?= e($profile['github_url']) ?>" target="_blank"><i class="bi bi-github"></i></a><?php endif; ?>
        <?php if (!empty($profile['linkedin_url'])): ?><a href="<?= e($profile['linkedin_url']) ?>" target="_blank"><i class="bi bi-linkedin"></i></a><?php endif; ?>
        <?php if (!empty($profile['website_url'])): ?><a href="<?= e($profile['website_url']) ?>" target="_blank"><i class="bi bi-globe"></i></a><?php endif; ?>
    </div>
</div>

<?php if (!empty($profile['bio'])): ?>
    <div class="tcm-card"><p class="mb-0"><?= nl2br(e($profile['bio'])) ?></p></div>
<?php endif; ?>

<?php if ($skills !== []): ?>
<div class="tcm-card" style="margin-top:18px;">
    <h3 class="mt-0">Skills</h3>
    <div class="d-flex gap-8 flex-wrap">
        <?php foreach ($skills as $s): ?>
            <span class="tcm-badge purple"><?= e($s['name']) ?></span>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php if ($projects !== []): ?>
<div style="margin-top:18px;">
    <h3>Projects</h3>
    <div class="grid-cards">
        <?php foreach ($projects as $p): ?>
            <div class="tcm-card">
                <strong><?= e($p['title']) ?></strong>
                <p class="muted" style="font-size:.86rem;"><?= e($p['description'] ?? '') ?></p>
                <div class="muted" style="font-size:.8rem;"><?= e($p['tech_stack'] ?? '') ?></div>
                <div class="d-flex gap-8" style="margin-top:8px;font-size:.85rem;">
                    <?php if ($p['repo_url']): ?><a href="<?= e($p['repo_url']) ?>" target="_blank">Code</a><?php endif; ?>
                    <?php if ($p['live_url']): ?><a href="<?= e($p['live_url']) ?>" target="_blank">Live demo</a><?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php if ($certificates !== [] || $achievements !== []): ?>
<div class="tcm-card" style="margin-top:18px;">
    <h3 class="mt-0">Achievements & Certificates</h3>
    <?php foreach ($achievements as $a): ?>
        <div style="padding:6px 0;"><i class="bi bi-trophy"></i> <?= e($a['title']) ?> <span class="muted">· <?= e($a['issuer'] ?? '') ?></span></div>
    <?php endforeach; ?>
    <?php foreach ($certificates as $c): ?>
        <div style="padding:6px 0;"><i class="bi bi-patch-check-fill" style="color:var(--tcm-accent);"></i> <?= e($c['title']) ?></div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<p class="muted" style="text-align:center;margin:30px 0;font-size:.82rem;">
    Powered by <a href="<?= base_url('/') ?>">The Code Munk</a>
</p>
