<div class="tcm-page-head">
    <div><h2>My Portfolio</h2><p>Showcase your projects, skills and achievements.</p></div>
    <a class="tcm-btn" target="_blank" href="<?= base_url('/portfolio/' . $user['id']) ?>"><i class="bi bi-box-arrow-up-right"></i> View public page</a>
</div>

<div class="tcm-card" style="margin-bottom:18px;">
    <div class="flex-between" style="margin-bottom:8px;">
        <strong>Portfolio strength</strong><span class="tcm-badge purple"><?= (int)$strength ?>%</span>
    </div>
    <div class="tcm-progress"><span style="width:<?= (int)$strength ?>%"></span></div>
    <p class="muted" style="margin:10px 0 0;font-size:.85rem;">Add projects, skills and achievements to strengthen your profile and stand out to recruiters.</p>
</div>

<div class="tcm-grid-2" style="align-items:start;">
    <!-- Projects -->
    <div class="tcm-card">
        <h3 class="mt-0">Projects</h3>
        <?php foreach ($projects as $p): ?>
            <div class="flex-between" style="padding:10px 0;border-bottom:1px solid var(--tcm-border);">
                <div>
                    <strong><?= e($p['title']) ?></strong>
                    <?php if ($p['is_featured']): ?><span class="tcm-badge amber">Featured</span><?php endif; ?>
                    <div class="muted" style="font-size:.82rem;"><?= e($p['tech_stack'] ?? '') ?></div>
                    <div class="d-flex gap-8" style="font-size:.8rem;margin-top:4px;">
                        <?php if ($p['repo_url']): ?><a href="<?= e($p['repo_url']) ?>" target="_blank">Repo</a><?php endif; ?>
                        <?php if ($p['live_url']): ?><a href="<?= e($p['live_url']) ?>" target="_blank">Live</a><?php endif; ?>
                    </div>
                </div>
                <form method="post" action="<?= base_url('/student/portfolio/projects/' . $p['id'] . '/delete') ?>">
                    <?= csrf_field() ?>
                    <button class="tcm-btn sm danger"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        <?php endforeach; ?>
        <?php if ($projects === []): ?><p class="muted">No projects yet.</p><?php endif; ?>

        <form method="post" action="<?= base_url('/student/portfolio/projects') ?>" style="margin-top:14px;">
            <?= csrf_field() ?>
            <div class="tcm-field"><label>Project title</label><input class="tcm-input" name="title" required></div>
            <div class="tcm-field"><label>Description</label><textarea class="tcm-textarea" name="description" style="min-height:70px;"></textarea></div>
            <div class="tcm-field"><label>Tech stack</label><input class="tcm-input" name="tech_stack" placeholder="React, Node.js, MySQL"></div>
            <div class="tcm-grid-2">
                <div class="tcm-field"><label>Repo URL</label><input class="tcm-input" name="repo_url"></div>
                <div class="tcm-field"><label>Live URL</label><input class="tcm-input" name="live_url"></div>
            </div>
            <label class="muted" style="font-size:.85rem;"><input type="checkbox" name="is_featured" value="1"> Feature this project</label>
            <div style="margin-top:12px;"><button class="tcm-btn primary"><i class="bi bi-plus-lg"></i> Add project</button></div>
        </form>
    </div>

    <div>
        <!-- Skills -->
        <div class="tcm-card">
            <h3 class="mt-0">Skills</h3>
            <?php foreach ($skills as $s): ?>
                <div style="padding:8px 0;">
                    <div class="flex-between">
                        <span><?= e($s['name']) ?></span>
                        <form method="post" action="<?= base_url('/student/portfolio/skills/' . $s['id'] . '/delete') ?>">
                            <?= csrf_field() ?>
                            <button class="tcm-btn sm danger" style="padding:2px 8px;"><i class="bi bi-x"></i></button>
                        </form>
                    </div>
                    <div class="tcm-progress" style="margin-top:4px;"><span style="width:<?= (int)$s['level'] ?>%"></span></div>
                </div>
            <?php endforeach; ?>
            <?php if ($skills === []): ?><p class="muted">No skills added.</p><?php endif; ?>
            <form method="post" action="<?= base_url('/student/portfolio/skills') ?>" class="d-flex gap-8 items-center" style="margin-top:10px;">
                <?= csrf_field() ?>
                <input class="tcm-input" name="name" placeholder="Skill" required style="flex:1;">
                <input class="tcm-input" type="number" name="level" min="0" max="100" value="60" style="width:80px;">
                <button class="tcm-btn primary"><i class="bi bi-plus"></i></button>
            </form>
        </div>

        <!-- Achievements -->
        <div class="tcm-card" style="margin-top:18px;">
            <h3 class="mt-0">Achievements</h3>
            <?php foreach ($achievements as $a): ?>
                <div class="flex-between" style="padding:8px 0;border-bottom:1px solid var(--tcm-border);">
                    <div>
                        <strong><?= e($a['title']) ?></strong>
                        <div class="muted" style="font-size:.8rem;"><?= e($a['issuer'] ?? '') ?></div>
                    </div>
                    <form method="post" action="<?= base_url('/student/portfolio/achievements/' . $a['id'] . '/delete') ?>">
                        <?= csrf_field() ?>
                        <button class="tcm-btn sm danger" style="padding:2px 8px;"><i class="bi bi-x"></i></button>
                    </form>
                </div>
            <?php endforeach; ?>
            <?php if ($achievements === []): ?><p class="muted">No achievements yet.</p><?php endif; ?>
            <form method="post" action="<?= base_url('/student/portfolio/achievements') ?>" style="margin-top:10px;">
                <?= csrf_field() ?>
                <div class="tcm-field"><input class="tcm-input" name="title" placeholder="Achievement title" required></div>
                <div class="tcm-grid-2">
                    <div class="tcm-field" style="margin-bottom:8px;"><input class="tcm-input" name="issuer" placeholder="Issuer"></div>
                    <div class="tcm-field" style="margin-bottom:8px;"><input class="tcm-input" type="date" name="achieved_on"></div>
                </div>
                <button class="tcm-btn primary"><i class="bi bi-plus-lg"></i> Add</button>
            </form>
        </div>

        <!-- Certificates -->
        <div class="tcm-card" style="margin-top:18px;">
            <h3 class="mt-0">Certificates</h3>
            <?php foreach ($certificates as $cert): ?>
                <div style="padding:8px 0;border-bottom:1px solid var(--tcm-border);">
                    <i class="bi bi-patch-check-fill" style="color:var(--tcm-accent);"></i> <?= e($cert['title']) ?>
                    <div class="muted" style="font-size:.78rem;">#<?= e($cert['certificate_number']) ?> · <?= e(date('d M Y', strtotime($cert['issued_at']))) ?></div>
                </div>
            <?php endforeach; ?>
            <?php if ($certificates === []): ?><p class="muted mb-0">Complete a course to earn your first certificate.</p><?php endif; ?>
        </div>
    </div>
</div>
