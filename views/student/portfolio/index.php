<!-- Page header -->
<div class="tcm-page-head">
    <div>
        <h2>My Portfolio</h2>
        <p>Build your developer profile — projects, skills and achievements.</p>
    </div>
    <a class="tcm-btn primary" target="_blank"
       href="<?= base_url('/portfolio/' . $user['id']) ?>">
        <i class="bi bi-box-arrow-up-right"></i> View Public Page
    </a>
</div>

<!-- Portfolio strength -->
<div class="tcm-card" style="margin-bottom:16px;">
    <div class="flex-between" style="margin-bottom:10px;">
        <div>
            <span style="font-size:.85rem;font-weight:700;color:var(--white);">Portfolio Strength</span>
            <span class="muted" style="font-size:.8rem;margin-left:8px;">
                — Add more content to strengthen your profile
            </span>
        </div>
        <span class="tcm-badge <?= (int)$strength >= 80 ? 'green' : ((int)$strength >= 40 ? 'amber' : 'gray') ?>">
            <?= (int)$strength ?>%
        </span>
    </div>
    <div class="tcm-progress thick">
        <span style="width:<?= (int)$strength ?>%"></span>
    </div>
</div>

<div class="tcm-grid-2" style="align-items:start;">

    <!-- ── LEFT: Projects ── -->
    <div>
        <div class="tcm-card">
            <h3 class="mt-0" style="margin-bottom:14px;">
                <i class="bi bi-folder2-open" style="color:var(--muted);margin-right:8px;"></i>
                Projects
            </h3>

            <!-- Project list -->
            <?php foreach ($projects as $p): ?>
            <div class="tcm-row">
                <div class="tcm-row-main">
                    <div class="tcm-row-title">
                        <?= e($p['title']) ?>
                        <?php if ($p['is_featured']): ?>
                            <span class="tcm-badge amber" style="font-size:.6rem;">Featured</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($p['tech_stack']): ?>
                        <div class="tcm-row-sub"><?= e($p['tech_stack']) ?></div>
                    <?php endif; ?>
                    <div class="d-flex gap-8" style="margin-top:5px;">
                        <?php if ($p['repo_url']): ?>
                            <a href="<?= e($p['repo_url']) ?>" target="_blank"
                               style="font-size:.76rem;color:var(--muted);">
                                <i class="bi bi-github"></i> Repo
                            </a>
                        <?php endif; ?>
                        <?php if ($p['live_url']): ?>
                            <a href="<?= e($p['live_url']) ?>" target="_blank"
                               style="font-size:.76rem;color:var(--muted);">
                                <i class="bi bi-arrow-up-right-square"></i> Live
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <form method="post"
                      action="<?= base_url('/student/portfolio/projects/'.$p['id'].'/delete') ?>">
                    <?= csrf_field() ?>
                    <button class="tcm-btn sm danger" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
            <?php endforeach; ?>

            <?php if ($projects === []): ?>
                <div class="tcm-empty" style="padding:16px 0 12px;">
                    <i class="bi bi-folder-x"></i>
                    No projects yet — add your first one below.
                </div>
            <?php endif; ?>

            <!-- Add project form -->
            <div style="margin-top:18px;padding-top:16px;border-top:1px solid var(--bd);">
                <div style="font-size:.75rem;font-weight:700;color:var(--muted);
                            text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;">
                    Add a project
                </div>
                <form method="post"
                      action="<?= base_url('/student/portfolio/projects') ?>">
                    <?= csrf_field() ?>
                    <div class="tcm-field">
                        <label>Project title *</label>
                        <input class="tcm-input" name="title" required
                               placeholder="e.g. E-commerce App">
                    </div>
                    <div class="tcm-field">
                        <label>Description</label>
                        <textarea class="tcm-textarea" name="description"
                                  style="min-height:70px;"
                                  placeholder="What did you build? What problem does it solve?"></textarea>
                    </div>
                    <div class="tcm-field">
                        <label>Tech stack</label>
                        <input class="tcm-input" name="tech_stack"
                               placeholder="React, Node.js, MySQL">
                    </div>
                    <div class="tcm-grid-2">
                        <div class="tcm-field">
                            <label>Repo URL</label>
                            <input class="tcm-input" name="repo_url"
                                   placeholder="https://github.com/...">
                        </div>
                        <div class="tcm-field">
                            <label>Live URL</label>
                            <input class="tcm-input" name="live_url"
                                   placeholder="https://...">
                        </div>
                    </div>
                    <label style="display:flex;align-items:center;gap:8px;
                                  font-size:.82rem;color:var(--muted);cursor:pointer;margin-bottom:14px;">
                        <input type="checkbox" name="is_featured" value="1"
                               style="accent-color:var(--white);width:15px;height:15px;">
                        Feature this project on my portfolio
                    </label>
                    <button class="tcm-btn primary" type="submit">
                        <i class="bi bi-plus-lg"></i> Add Project
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ── RIGHT: Skills, Achievements, Certs ── -->
    <div>

        <!-- Skills -->
        <div class="tcm-card">
            <h3 class="mt-0" style="margin-bottom:14px;">
                <i class="bi bi-lightning" style="color:var(--muted);margin-right:8px;"></i>
                Skills
            </h3>

            <?php foreach ($skills as $s): ?>
            <div style="padding:8px 0;border-bottom:1px solid var(--bd);">
                <div class="flex-between" style="margin-bottom:5px;">
                    <span style="font-size:.85rem;font-weight:500;color:var(--txt);">
                        <?= e($s['name']) ?>
                    </span>
                    <div class="d-flex items-center gap-8">
                        <span class="muted" style="font-size:.76rem;"><?= (int)$s['level'] ?>%</span>
                        <form method="post"
                              action="<?= base_url('/student/portfolio/skills/'.$s['id'].'/delete') ?>">
                            <?= csrf_field() ?>
                            <button class="tcm-btn sm danger"
                                    style="padding:2px 7px;" title="Remove">
                                <i class="bi bi-x"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="tcm-progress">
                    <span style="width:<?= (int)$s['level'] ?>%"></span>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if ($skills === []): ?>
                <p class="muted" style="font-size:.84rem;padding:4px 0 8px;">No skills yet.</p>
            <?php endif; ?>

            <form method="post"
                  action="<?= base_url('/student/portfolio/skills') ?>"
                  class="d-flex gap-8 items-center"
                  style="margin-top:14px;">
                <?= csrf_field() ?>
                <input class="tcm-input" name="name"
                       placeholder="Skill (e.g. React)" required style="flex:1;">
                <input class="tcm-input" type="number" name="level"
                       min="0" max="100" value="70"
                       style="width:72px;" title="Proficiency %">
                <button class="tcm-btn primary" type="submit"
                        style="padding:9px 13px;" title="Add skill">
                    <i class="bi bi-plus"></i>
                </button>
            </form>
        </div>

        <!-- Achievements -->
        <div class="tcm-card" style="margin-top:14px;">
            <h3 class="mt-0" style="margin-bottom:14px;">
                <i class="bi bi-trophy" style="color:var(--muted);margin-right:8px;"></i>
                Achievements
            </h3>

            <?php foreach ($achievements as $a): ?>
            <div class="tcm-row">
                <div class="tcm-row-main">
                    <div class="tcm-row-title"><?= e($a['title']) ?></div>
                    <?php if ($a['issuer']): ?>
                        <div class="tcm-row-sub"><?= e($a['issuer']) ?></div>
                    <?php endif; ?>
                </div>
                <form method="post"
                      action="<?= base_url('/student/portfolio/achievements/'.$a['id'].'/delete') ?>">
                    <?= csrf_field() ?>
                    <button class="tcm-btn sm danger" style="padding:2px 7px;">
                        <i class="bi bi-x"></i>
                    </button>
                </form>
            </div>
            <?php endforeach; ?>

            <?php if ($achievements === []): ?>
                <p class="muted" style="font-size:.84rem;padding:4px 0 8px;">No achievements yet.</p>
            <?php endif; ?>

            <form method="post"
                  action="<?= base_url('/student/portfolio/achievements') ?>"
                  style="margin-top:14px;">
                <?= csrf_field() ?>
                <div class="tcm-field">
                    <input class="tcm-input" name="title"
                           placeholder="Achievement title" required>
                </div>
                <div class="tcm-grid-2">
                    <div class="tcm-field" style="margin-bottom:10px;">
                        <input class="tcm-input" name="issuer" placeholder="Issuer / Platform">
                    </div>
                    <div class="tcm-field" style="margin-bottom:10px;">
                        <input class="tcm-input" type="date" name="achieved_on">
                    </div>
                </div>
                <button class="tcm-btn primary" type="submit">
                    <i class="bi bi-plus-lg"></i> Add Achievement
                </button>
            </form>
        </div>

        <!-- Certificates -->
        <?php if ($certificates !== []): ?>
        <div class="tcm-card" style="margin-top:14px;">
            <h3 class="mt-0" style="margin-bottom:14px;">
                <i class="bi bi-patch-check" style="color:var(--muted);margin-right:8px;"></i>
                Certificates
            </h3>
            <?php foreach ($certificates as $cert): ?>
            <div class="tcm-row">
                <div class="tcm-row-main">
                    <div class="tcm-row-title">
                        <i class="bi bi-patch-check-fill" style="color:#4ade80;"></i>
                        <?= e($cert['title']) ?>
                    </div>
                    <div class="tcm-row-sub">
                        #<?= e($cert['certificate_number']) ?> ·
                        <?= e(date('d M Y', strtotime($cert['issued_at']))) ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="tcm-card" style="margin-top:14px;">
            <div class="tcm-empty" style="padding:16px 0 8px;">
                <i class="bi bi-patch-check"></i>
                Complete a course to earn your first certificate.
            </div>
        </div>
        <?php endif; ?>

    </div>

</div>
