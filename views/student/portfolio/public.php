<style>
@keyframes fadeInUp { from{opacity:0;transform:translateY(28px)} to{opacity:1;transform:translateY(0)} }
@keyframes slideIn  { from{opacity:0;transform:translateX(-18px)} to{opacity:1;transform:translateX(0)} }
@keyframes avatarScale { 0%{transform:scale(.85);opacity:0} 60%{transform:scale(1.04)} 100%{transform:scale(1);opacity:1} }
@keyframes ringRotate  { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
@keyframes badgePop    { 0%{transform:scale(0);opacity:0} 70%{transform:scale(1.1)} 100%{transform:scale(1);opacity:1} }
@keyframes pfpulse     { 0%,100%{opacity:1} 50%{opacity:.3} }

.pf-animate { opacity:0; animation:fadeInUp .6s ease forwards; }

.pf-page { max-width:780px; margin:0 auto; padding:28px 18px 60px; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; }

.pf-hero-card { background:#fff;border:1px solid #ececec;border-radius:22px;padding:36px 28px 28px;text-align:center;margin-bottom:18px;position:relative;overflow:hidden;box-shadow:0 2px 16px rgba(0,0,0,.05); }
.pf-hero-card::before { content:'';position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,#6366f1,#8b5cf6,#ec4899,#f59e0b);border-radius:22px 22px 0 0; }

.pf-avatar-wrap { position:relative;display:inline-block;margin-bottom:16px;animation:avatarScale .7s cubic-bezier(.34,1.56,.64,1) .1s both; }
.pf-avatar-ring { position:absolute;inset:-5px;border-radius:50%;background:conic-gradient(#6366f1,#8b5cf6,#ec4899,#f59e0b,#6366f1);animation:ringRotate 4s linear infinite;z-index:0; }
.pf-avatar-ring::after { content:'';position:absolute;inset:4px;border-radius:50%;background:#fff; }
.pf-avatar { position:relative;z-index:1;width:88px;height:88px;border-radius:50%;overflow:hidden;display:grid;place-items:center;font-size:2rem;font-weight:800;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff; }
.pf-avatar img { width:100%;height:100%;object-fit:cover; }

.pf-name { font-size:1.45rem;font-weight:800;color:#111;margin-bottom:5px;letter-spacing:-.02em; }
.pf-headline { font-size:.88rem;color:#666;margin-bottom:10px;line-height:1.5; }
.pf-location { display:inline-flex;align-items:center;gap:4px;font-size:.78rem;color:#aaa;margin-bottom:14px; }

.pf-available { display:inline-flex;align-items:center;gap:6px;background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;border-radius:20px;padding:4px 14px;font-size:.75rem;font-weight:600;margin-bottom:14px;animation:badgePop .5s ease .4s both; }
.pf-available-dot { width:7px;height:7px;border-radius:50%;background:#22c55e;animation:pfpulse 1.5s infinite; }

.pf-socials { display:flex;gap:10px;justify-content:center;flex-wrap:wrap; }
.pf-social-link { display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:50%;border:1.5px solid #ececec;color:#444;font-size:.95rem;text-decoration:none;transition:background .2s,color .2s,border-color .2s,transform .2s; }
.pf-social-link:hover { background:#111;color:#fff;border-color:#111;transform:translateY(-2px); }

.pf-stats-bar { display:flex;justify-content:center;background:#f9f9f9;border:1px solid #ececec;border-radius:14px;margin-bottom:18px;overflow:hidden; }
.pf-stat-item { flex:1;text-align:center;padding:14px 8px;border-right:1px solid #ececec; }
.pf-stat-item:last-child { border-right:none; }
.pf-stat-val { font-size:1.2rem;font-weight:800;color:#111;display:block; }
.pf-stat-lbl { font-size:.7rem;color:#888;margin-top:2px;display:block; }

.pf-section-card { background:#fff;border:1px solid #ececec;border-radius:16px;padding:22px 22px 18px;margin-bottom:16px;box-shadow:0 1px 6px rgba(0,0,0,.03); }
.pf-section-title { display:flex;align-items:center;gap:8px;font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6366f1;margin-bottom:14px; }
.pf-bio { font-size:.88rem;color:#444;line-height:1.75; }

.pf-skill-list { display:flex;flex-wrap:wrap;gap:8px; }
.pf-skill-pill { background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:20px;padding:5px 14px;font-size:.78rem;font-weight:600;transition:background .2s,color .2s,transform .18s,box-shadow .18s;cursor:default; }
.pf-skill-pill:hover { background:#6366f1;color:#fff;border-color:#6366f1;transform:translateY(-2px) scale(1.04);box-shadow:0 4px 12px rgba(99,102,241,.25); }

.pf-projects { display:grid;gap:12px; }
.pf-project-card { border:1px solid #ececec;border-radius:12px;padding:16px 18px;transition:box-shadow .2s,transform .2s;position:relative;overflow:hidden; }
.pf-project-card:hover { box-shadow:0 6px 24px rgba(0,0,0,.08);transform:translateY(-3px); }
.pf-project-card::before { content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:linear-gradient(180deg,#6366f1,#8b5cf6);border-radius:3px 0 0 3px; }
.pf-project-head { display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:6px; }
.pf-project-title { font-weight:700;font-size:.92rem;color:#111; }
.pf-project-desc  { font-size:.82rem;color:#666;line-height:1.6;margin-bottom:10px; }
.pf-project-stack { display:inline-flex;align-items:center;gap:6px;font-size:.75rem;color:#6366f1;background:#eef2ff;padding:3px 10px;border-radius:20px;margin-bottom:10px; }
.pf-project-links { display:flex;gap:8px; }
.pf-project-link { display:inline-flex;align-items:center;gap:5px;font-size:.78rem;font-weight:600;color:#555;background:#f5f5f5;border:1px solid #ececec;padding:4px 12px;border-radius:8px;text-decoration:none;transition:background .15s,color .15s; }
.pf-project-link:hover { background:#111;color:#fff;border-color:#111; }

.pf-ach-list { display:grid;gap:10px; }
.pf-ach-item { display:flex;align-items:flex-start;gap:14px;padding:12px 14px;background:#fafafa;border:1px solid #ececec;border-radius:12px; }
.pf-ach-icon { width:38px;height:38px;border-radius:50%;flex-shrink:0;background:linear-gradient(135deg,#fef3c7,#fde68a);display:grid;place-items:center;font-size:.9rem;color:#d97706; }
.pf-ach-title { font-weight:700;font-size:.87rem;color:#111;margin-bottom:2px; }
.pf-ach-meta  { font-size:.75rem;color:#888; }

.pf-cert-grid { display:grid;gap:10px; }
.pf-cert-card { display:flex;align-items:center;gap:14px;padding:12px 14px;border:1px solid #ececec;border-radius:12px;background:#f9fffe; }
.pf-cert-icon { width:38px;height:38px;border-radius:10px;flex-shrink:0;background:linear-gradient(135deg,#d1fae5,#a7f3d0);display:grid;place-items:center;font-size:1rem;color:#059669; }
.pf-cert-title { font-weight:700;font-size:.87rem;color:#111;margin-bottom:2px; }
.pf-cert-num   { font-size:.73rem;color:#888; }

.pf-footer { text-align:center;padding:20px 0 0;font-size:.78rem;color:#aaa; }
.pf-footer a { color:#6366f1;text-decoration:none;font-weight:600; }
</style>

<div class="pf-page">

    <!-- Hero -->
    <div class="pf-hero-card">
        <div class="pf-avatar-wrap">
            <div class="pf-avatar-ring"></div>
            <div class="pf-avatar">
                <?php if (!empty($owner['avatar'])): ?>
                    <img src="<?= base_url('/uploads/' . e($owner['avatar'])) ?>" alt="<?= e($owner['name']) ?>">
                <?php else: ?>
                    <?= e(strtoupper(substr($owner['name'], 0, 1))) ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="pf-name"><?= e($owner['name']) ?></div>
        <div class="pf-headline"><?= e($profile['headline'] ?? 'Learner at The Code Munk') ?></div>
        <?php if (!empty($profile['location'])): ?>
            <div class="pf-location"><i class="bi bi-geo-alt-fill"></i> <?= e($profile['location']) ?></div>
        <?php endif; ?>
        <?php if (!empty($profile['goal'])): ?>
            <div style="margin-bottom:12px;">
                <span class="pf-available"><span class="pf-available-dot"></span> Available for opportunities</span>
            </div>
        <?php endif; ?>
        <div class="pf-socials">
            <?php if (!empty($profile['github_url'])): ?><a href="<?= e($profile['github_url']) ?>" target="_blank" class="pf-social-link" title="GitHub"><i class="bi bi-github"></i></a><?php endif; ?>
            <?php if (!empty($profile['linkedin_url'])): ?><a href="<?= e($profile['linkedin_url']) ?>" target="_blank" class="pf-social-link" title="LinkedIn"><i class="bi bi-linkedin"></i></a><?php endif; ?>
            <?php if (!empty($profile['website_url'])): ?><a href="<?= e($profile['website_url']) ?>" target="_blank" class="pf-social-link" title="Website"><i class="bi bi-globe2"></i></a><?php endif; ?>
            <?php if (!empty($profile['twitter_url'])): ?><a href="<?= e($profile['twitter_url']) ?>" target="_blank" class="pf-social-link" title="X"><i class="bi bi-twitter-x"></i></a><?php endif; ?>
        </div>
    </div>

    <!-- Stats bar -->
    <div class="pf-stats-bar pf-animate">
        <div class="pf-stat-item"><span class="pf-stat-val"><?= count($projects ?? []) ?></span><span class="pf-stat-lbl">Projects</span></div>
        <div class="pf-stat-item"><span class="pf-stat-val"><?= count($skills ?? []) ?></span><span class="pf-stat-lbl">Skills</span></div>
        <div class="pf-stat-item"><span class="pf-stat-val"><?= count($achievements ?? []) ?></span><span class="pf-stat-lbl">Achievements</span></div>
        <div class="pf-stat-item"><span class="pf-stat-val"><?= count($certificates ?? []) ?></span><span class="pf-stat-lbl">Certificates</span></div>
    </div>

    <!-- About -->
    <?php if (!empty($profile['bio'])): ?>
    <div class="pf-section-card pf-animate">
        <div class="pf-section-title"><i class="bi bi-person-lines-fill"></i> About</div>
        <div class="pf-bio"><?= nl2br(e($profile['bio'])) ?></div>
    </div>
    <?php endif; ?>

    <!-- Skills -->
    <?php if (!empty($skills)): ?>
    <div class="pf-section-card pf-animate">
        <div class="pf-section-title"><i class="bi bi-stars"></i> Skills</div>
        <div class="pf-skill-list">
            <?php foreach ($skills as $s): ?><span class="pf-skill-pill"><?= e($s['name']) ?></span><?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Projects -->
    <?php if (!empty($projects)): ?>
    <div class="pf-section-card pf-animate">
        <div class="pf-section-title"><i class="bi bi-code-slash"></i> Projects</div>
        <div class="pf-projects">
            <?php foreach ($projects as $p): ?>
            <div class="pf-project-card pf-animate">
                <div class="pf-project-head">
                    <div class="pf-project-title"><?= e($p['title']) ?></div>
                    <?php if ($p['is_featured']): ?><span style="font-size:.68rem;font-weight:700;background:#fff3e0;color:#e67700;padding:2px 8px;border-radius:12px;white-space:nowrap;">★ Featured</span><?php endif; ?>
                </div>
                <?php if (!empty($p['description'])): ?><div class="pf-project-desc"><?= e($p['description']) ?></div><?php endif; ?>
                <?php if (!empty($p['tech_stack'])): ?><div class="pf-project-stack"><i class="bi bi-layers"></i> <?= e($p['tech_stack']) ?></div><?php endif; ?>
                <?php if (!empty($p['repo_url']) || !empty($p['live_url'])): ?>
                <div class="pf-project-links">
                    <?php if (!empty($p['repo_url'])): ?><a href="<?= e($p['repo_url']) ?>" target="_blank" class="pf-project-link"><i class="bi bi-github"></i> Code</a><?php endif; ?>
                    <?php if (!empty($p['live_url'])): ?><a href="<?= e($p['live_url']) ?>" target="_blank" class="pf-project-link"><i class="bi bi-arrow-up-right-square"></i> Live Demo</a><?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Achievements -->
    <?php if (!empty($achievements)): ?>
    <div class="pf-section-card pf-animate">
        <div class="pf-section-title"><i class="bi bi-trophy-fill"></i> Achievements</div>
        <div class="pf-ach-list">
            <?php foreach ($achievements as $a): ?>
            <div class="pf-ach-item pf-animate">
                <div class="pf-ach-icon"><i class="bi bi-trophy-fill"></i></div>
                <div>
                    <div class="pf-ach-title"><?= e($a['title']) ?></div>
                    <div class="pf-ach-meta">
                        <?php if (!empty($a['issuer'])): ?><i class="bi bi-building" style="font-size:.72rem;"></i> <?= e($a['issuer']) ?><?php endif; ?>
                        <?php if (!empty($a['achieved_on'])): ?> · <?= e(date('M Y', strtotime($a['achieved_on']))) ?><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Certificates -->
    <?php if (!empty($certificates)): ?>
    <div class="pf-section-card pf-animate">
        <div class="pf-section-title"><i class="bi bi-patch-check-fill"></i> Certificates</div>
        <div class="pf-cert-grid">
            <?php foreach ($certificates as $c): ?>
            <div class="pf-cert-card pf-animate">
                <div class="pf-cert-icon"><i class="bi bi-patch-check-fill"></i></div>
                <div>
                    <div class="pf-cert-title"><?= e($c['title']) ?></div>
                    <div class="pf-cert-num">#<?= e($c['certificate_number']) ?> · <?= e(date('d M Y', strtotime($c['issued_at']))) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="pf-footer pf-animate">Powered by <a href="<?= base_url('/') ?>">The Code Munk</a></div>

</div>

<script>
(function(){
    if(!('IntersectionObserver' in window)){
        document.querySelectorAll('.pf-animate').forEach(function(el){el.style.opacity='1';el.style.transform='none';});
        return;
    }
    var obs = new IntersectionObserver(function(entries){
        entries.forEach(function(e){
            if(e.isIntersecting){
                e.target.style.animationPlayState='running';
                obs.unobserve(e.target);
            }
        });
    },{threshold:0.1});
    document.querySelectorAll('.pf-animate').forEach(function(el){
        el.style.animationPlayState='paused';
        obs.observe(el);
    });
    // Above fold items run immediately
    document.querySelectorAll('.pf-hero-card,.pf-stats-bar').forEach(function(el){
        el.style.animationPlayState='running';el.style.opacity='1';
    });
})();
</script>
