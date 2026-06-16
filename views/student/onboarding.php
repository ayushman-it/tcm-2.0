<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Profile · The Code Munk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('/assets/dashboard.css') ?>">

<style>
/* ─────────────────────────────────────────────────────────
   ONBOARDING FULL-PAGE LAYOUT
───────────────────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

html, body {
    height: 100%;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    background: #f7f7f7;
    color: #111;
    -webkit-font-smoothing: antialiased;
}

/* ── Shell: two-column full height ── */
.ob-shell {
    display: grid;
    grid-template-columns: 460px 1fr;
    min-height: 100vh;
}

/* ════════════════════════════════════════════════
   LEFT PANEL
════════════════════════════════════════════════ */
.ob-left {
    background: #111;
    color: #fff;
    display: flex;
    flex-direction: column;
    padding: 48px 44px;
    position: sticky;
    top: 0;
    height: 100vh;
    overflow: hidden;
}

/* Brand */
.ob-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1rem;
    font-weight: 800;
    color: #fff;
    text-decoration: none;
    letter-spacing: -.2px;
    margin-bottom: 56px;
}
.ob-brand-icon {
    width: 34px; height: 34px;
    background: #fff;
    border-radius: 9px;
    display: grid; place-items: center;
    font-size: .95rem;
    color: #111;
    flex-shrink: 0;
}

/* Headline block */
.ob-left-tag {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.15);
    border-radius: 999px;
    padding: 5px 14px;
    font-size: .72rem;
    font-weight: 700;
    color: rgba(255,255,255,.65);
    letter-spacing: .1em;
    text-transform: uppercase;
    margin-bottom: 20px;
}
.ob-left-tag-dot {
    width: 6px; height: 6px;
    background: #fff;
    border-radius: 50%;
    flex-shrink: 0;
    animation: blink 1.6s ease infinite;
}
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }

.ob-left h1 {
    font-size: 2.4rem;
    font-weight: 900;
    line-height: 1.1;
    letter-spacing: -.06em;
    margin-bottom: 16px;
    color: #fff;
}
.ob-left h1 em {
    font-style: normal;
    color: rgba(255,255,255,.45);
}
.ob-left p {
    font-size: .9rem;
    color: rgba(255,255,255,.55);
    line-height: 1.7;
    max-width: 320px;
    margin-bottom: 40px;
}

/* Feature list */
.ob-features {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 14px;
    margin-bottom: 40px;
}
.ob-feature {
    display: flex;
    align-items: flex-start;
    gap: 14px;
}
.ob-feature-icon {
    width: 36px; height: 36px;
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 10px;
    display: grid; place-items: center;
    font-size: .95rem;
    color: #fff;
    flex-shrink: 0;
    margin-top: 1px;
}
.ob-feature-text strong {
    display: block;
    font-size: .875rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 2px;
}
.ob-feature-text span {
    font-size: .78rem;
    color: rgba(255,255,255,.45);
    line-height: 1.5;
}

/* Social proof */
.ob-proof {
    margin-top: auto;
    padding-top: 28px;
    border-top: 1px solid rgba(255,255,255,.1);
}
.ob-proof-avatars {
    display: flex;
    margin-bottom: 10px;
}
.ob-proof-avatar {
    width: 30px; height: 30px;
    border-radius: 50%;
    background: rgba(255,255,255,.2);
    border: 2px solid #111;
    display: grid; place-items: center;
    font-size: .65rem;
    font-weight: 800;
    color: #fff;
    margin-right: -8px;
    flex-shrink: 0;
}
.ob-proof-avatar:first-child { margin-left: 0; }
.ob-proof-text {
    font-size: .8rem;
    color: rgba(255,255,255,.5);
    margin-top: 6px;
    margin-left: 8px;
    line-height: 1.5;
}
.ob-proof-text strong { color: rgba(255,255,255,.85); }

/* Decorative dots */
.ob-deco {
    position: absolute;
    bottom: -60px;
    right: -60px;
    width: 260px;
    height: 260px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(255,255,255,.05) 0%, transparent 70%);
    pointer-events: none;
}

/* ════════════════════════════════════════════════
   RIGHT PANEL
════════════════════════════════════════════════ */
.ob-right {
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.ob-right-inner {
    flex: 1;
    padding: 48px 56px 56px;
    max-width: 640px;
    width: 100%;
    margin: 0 auto;
}

/* Steps */
.ob-steps-row {
    display: flex;
    align-items: center;
    gap: 0;
    margin-bottom: 36px;
}
.ob-step {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
}
.ob-step-bubble {
    width: 30px; height: 30px;
    border-radius: 50%;
    display: grid; place-items: center;
    font-size: .75rem;
    font-weight: 800;
    flex-shrink: 0;
    transition: .2s;
}
.ob-step-bubble.done   { background: #111; color: #fff; }
.ob-step-bubble.active {
    background: #111; color: #fff;
    box-shadow: 0 0 0 4px rgba(17,17,17,.12);
}
.ob-step-bubble.todo   { background: #ececec; color: #aaa; }
.ob-step-name {
    font-size: .76rem;
    font-weight: 600;
    color: #aaa;
    white-space: nowrap;
}
.ob-step-name.active { color: #111; }
.ob-step-bar {
    flex: 1;
    height: 2px;
    background: #ececec;
    border-radius: 2px;
    margin: 0 6px;
}
.ob-step-bar.done { background: #111; }

/* Section heading */
.ob-section-head {
    margin-bottom: 28px;
}
.ob-section-head h2 {
    font-size: 1.5rem;
    font-weight: 800;
    color: #111;
    letter-spacing: -.5px;
    margin-bottom: 6px;
    line-height: 1.2;
}
.ob-section-head p {
    font-size: .875rem;
    color: #888;
    line-height: 1.55;
}

/* Divider labels */
.ob-divider {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 24px 0 20px;
}
.ob-divider::before, .ob-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #ececec;
}
.ob-divider span {
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: #bbb;
    white-space: nowrap;
}

/* Field */
.ob-field { margin-bottom: 16px; }
.ob-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: .78rem;
    font-weight: 700;
    color: #444;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: 6px;
}
.ob-label i { color: #888; font-size: .82rem; }
.ob-hint {
    font-size: .72rem;
    color: #bbb;
    margin-top: 4px;
    line-height: 1.4;
    display: flex;
    align-items: center;
    gap: 4px;
}
.ob-hint i { font-size: .68rem; }

/* Level picker */
.ob-levels {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}
.ob-level {
    position: relative;
    border: 1.5px solid #e5e5e5;
    border-radius: 12px;
    padding: 16px 14px;
    cursor: pointer;
    text-align: center;
    transition: border-color .15s, background .15s, transform .15s, box-shadow .15s;
    user-select: none;
}
.ob-level:hover {
    border-color: #aaa;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,.06);
}
.ob-level.active {
    border-color: #111;
    background: #111;
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(0,0,0,.15);
}
.ob-level input {
    position: absolute; opacity: 0; width: 0; height: 0;
}
.ob-level-emoji {
    font-size: 1.6rem;
    display: block;
    margin-bottom: 8px;
    line-height: 1;
}
.ob-level-title {
    font-size: .82rem;
    font-weight: 800;
    color: #111;
    display: block;
    margin-bottom: 3px;
}
.ob-level.active .ob-level-title { color: #fff; }
.ob-level-desc {
    font-size: .7rem;
    color: #888;
    display: block;
    line-height: 1.4;
}
.ob-level.active .ob-level-desc { color: rgba(255,255,255,.5); }

/* CTA button */
.ob-submit {
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #f0f0f0;
}
.ob-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    padding: 15px 24px;
    background: #111;
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: .95rem;
    font-weight: 700;
    font-family: 'Inter', inherit;
    cursor: pointer;
    transition: background .2s, transform .15s, box-shadow .2s;
    letter-spacing: -.1px;
    position: relative;
    overflow: hidden;
}
.ob-btn::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,.06) 0%, transparent 60%);
}
.ob-btn:hover {
    background: #2a2a2a;
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(0,0,0,.2);
}
.ob-btn:active { transform: scale(.98); box-shadow: none; }
.ob-btn-note {
    text-align: center;
    font-size: .72rem;
    color: #bbb;
    margin-top: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}

/* ── Flash ── */
.ob-flash {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 10px;
    margin-bottom: 24px;
    font-size: .85rem;
    font-weight: 500;
    border: 1px solid;
}
.ob-flash.success { background: #f0fdf4; border-color: #bbf7d0; color: #16a34a; }
.ob-flash.error   { background: #fef2f2; border-color: #fecaca; color: #dc2626; }

/* ── Responsive ── */
@media (max-width: 900px) {
    .ob-shell { grid-template-columns: 1fr; }
    .ob-left  { display: none; }
    .ob-right-inner { padding: 32px 24px 48px; max-width: 100%; }
}
@media (max-width: 480px) {
    .ob-right-inner { padding: 24px 18px 40px; }
    .ob-levels { grid-template-columns: 1fr; gap: 8px; }
    .ob-level { display: flex; align-items: center; gap: 12px; text-align: left; padding: 12px 14px; }
    .ob-level-emoji { font-size: 1.4rem; margin-bottom: 0; flex-shrink: 0; }
    .ob-step-name { display: none; }
}
</style>
</head>
<body>

<div class="ob-shell">

    <!-- ══════════════════════════════════════
         LEFT PANEL
    ══════════════════════════════════════ -->
    <aside class="ob-left">

        <!-- Brand -->
        <a href="<?= base_url('/') ?>" class="ob-brand">
            <div class="ob-brand-icon">
                <i class="bi bi-code-slash"></i>
            </div>
            The Code Munk
        </a>

        <!-- Tag -->
        <div class="ob-left-tag">
            <span class="ob-left-tag-dot"></span>
            Profile Setup
        </div>

        <!-- Headline -->
        <h1>
            Your developer<br>
            journey <em>starts</em><br>
            right here.
        </h1>

        <p>
            Set up your profile in under 2 minutes and unlock
            your personalised dashboard, live classes and a
            portfolio that gets you noticed.
        </p>

        <!-- Features -->
        <ul class="ob-features">
            <li class="ob-feature">
                <div class="ob-feature-icon">
                    <i class="bi bi-journal-code"></i>
                </div>
                <div class="ob-feature-text">
                    <strong>Personalised Course Feed</strong>
                    <span>Get course and program recommendations based on your goal and experience level.</span>
                </div>
            </li>
            <li class="ob-feature">
                <div class="ob-feature-icon">
                    <i class="bi bi-briefcase"></i>
                </div>
                <div class="ob-feature-text">
                    <strong>Public Developer Portfolio</strong>
                    <span>Showcase your projects, skills and certificates to recruiters and clients.</span>
                </div>
            </li>
            <li class="ob-feature">
                <div class="ob-feature-icon">
                    <i class="bi bi-broadcast"></i>
                </div>
                <div class="ob-feature-text">
                    <strong>Live Classes & Mentorship</strong>
                    <span>Join daily live sessions, doubt clearing and 1:1 mentorship with industry pros.</span>
                </div>
            </li>
            <li class="ob-feature">
                <div class="ob-feature-icon">
                    <i class="bi bi-patch-check"></i>
                </div>
                <div class="ob-feature-text">
                    <strong>Verified Certificates</strong>
                    <span>Earn sharable certificates when you complete any course or program.</span>
                </div>
            </li>
        </ul>

        <!-- Social proof -->
        <div class="ob-proof">
            <div class="ob-proof-avatars">
                <?php foreach (['R','A','P','S','K'] as $l): ?>
                    <div class="ob-proof-avatar"><?= $l ?></div>
                <?php endforeach; ?>
            </div>
            <div class="ob-proof-text">
                <strong>1,200+ students</strong> already learning at The Code Munk
            </div>
        </div>

        <!-- Decorative glow -->
        <div class="ob-deco"></div>

    </aside>

    <!-- ══════════════════════════════════════
         RIGHT PANEL
    ══════════════════════════════════════ -->
    <main class="ob-right">
        <div class="ob-right-inner">

            <!-- Flash messages -->
            <?php
            $fs = flash('success');
            $fe = flash('error');
            if ($fs): ?>
                <div class="ob-flash success">
                    <i class="bi bi-check-circle-fill"></i> <?= e($fs) ?>
                </div>
            <?php endif;
            if ($fe): ?>
                <div class="ob-flash error">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?= e($fe) ?>
                </div>
            <?php endif; ?>

            <!-- Step indicator -->
            <div class="ob-steps-row">
                <div class="ob-step">
                    <div class="ob-step-bubble done">
                        <i class="bi bi-check" style="font-size:.75rem;"></i>
                    </div>
                    <div class="ob-step-name">Account</div>
                </div>
                <div class="ob-step-bar done"></div>
                <div class="ob-step">
                    <div class="ob-step-bubble active">2</div>
                    <div class="ob-step-name active">Profile</div>
                </div>
                <div class="ob-step-bar"></div>
                <div class="ob-step">
                    <div class="ob-step-bubble todo">3</div>
                    <div class="ob-step-name">Dashboard</div>
                </div>
            </div>

            <!-- Heading -->
            <div class="ob-section-head">
                <h2>Set up your profile</h2>
                <p>
                    Welcome, <strong><?= e(explode(' ', $user['name'])[0]) ?></strong>!
                    A complete profile helps us tailor your experience
                    and makes your portfolio stand out.
                </p>
            </div>

            <!-- FORM -->
            <form method="post" action="<?= base_url('/student/onboarding') ?>">
                <?= csrf_field() ?>

                <!-- ── About ── -->
                <div class="ob-divider"><span>About You</span></div>

                <div class="ob-field">
                    <label class="ob-label" for="headline">
                        <i class="bi bi-person-badge"></i> Professional Headline
                    </label>
                    <input class="tcm-input" id="headline" name="headline"
                           placeholder="e.g. Aspiring Full Stack Developer"
                           value="<?= e($profile['headline'] ?? '') ?>">
                    <div class="ob-hint">
                        <i class="bi bi-info-circle"></i>
                        This appears on your public portfolio page
                    </div>
                </div>

                <div class="ob-field">
                    <label class="ob-label" for="bio">
                        <i class="bi bi-chat-left-text"></i> Short Bio
                    </label>
                    <textarea class="tcm-textarea" id="bio" name="bio"
                              style="min-height:96px;"
                              placeholder="What are you learning and building? What drives you?"><?= e($profile['bio'] ?? '') ?></textarea>
                </div>

                <div class="tcm-grid-2">
                    <div class="ob-field">
                        <label class="ob-label" for="location">
                            <i class="bi bi-geo-alt"></i> Location
                        </label>
                        <input class="tcm-input" id="location" name="location"
                               placeholder="e.g. Mumbai, India"
                               value="<?= e($profile['location'] ?? '') ?>">
                    </div>
                    <div class="ob-field">
                        <label class="ob-label" for="goal">
                            <i class="bi bi-bullseye"></i> Main Goal
                        </label>
                        <input class="tcm-input" id="goal" name="goal"
                               placeholder="e.g. Land my first dev job"
                               value="<?= e($profile['goal'] ?? '') ?>">
                    </div>
                </div>

                <!-- ── Education ── -->
                <div class="ob-divider"><span>Education</span></div>

                <div class="tcm-grid-2">
                    <div class="ob-field">
                        <label class="ob-label" for="college">
                            <i class="bi bi-building"></i> College / Org
                        </label>
                        <input class="tcm-input" id="college" name="college"
                               placeholder="e.g. IIT Delhi"
                               value="<?= e($profile['college'] ?? '') ?>">
                    </div>
                    <div class="ob-field">
                        <label class="ob-label" for="graduation_year">
                            <i class="bi bi-mortarboard"></i> Grad Year
                        </label>
                        <input class="tcm-input" type="number" id="graduation_year"
                               name="graduation_year" placeholder="2026"
                               min="2020" max="2035"
                               value="<?= e((string)($profile['graduation_year'] ?? '')) ?>">
                    </div>
                </div>

                <!-- ── Experience Level ── -->
                <div class="ob-divider"><span>Experience Level</span></div>

                <input type="hidden" name="experience_level" id="exp_hidden"
                       value="<?= e($profile['experience_level'] ?? 'beginner') ?>">

                <div class="ob-levels">
                    <?php
                    $levels  = [
                        'beginner'     => ['🌱', 'Beginner',     'Starting from scratch'],
                        'intermediate' => ['⚡', 'Intermediate',  'Some coding experience'],
                        'advanced'     => ['🔥', 'Advanced',      'Working professionally'],
                    ];
                    $current = $profile['experience_level'] ?? 'beginner';
                    foreach ($levels as $val => [$emoji, $title, $desc]):
                    ?>
                    <label class="ob-level <?= $current === $val ? 'active' : '' ?>"
                           onclick="pickLevel('<?= $val ?>', this)">
                        <input type="radio" name="_exp_ui" value="<?= $val ?>"
                               <?= $current === $val ? 'checked' : '' ?>>
                        <span class="ob-level-emoji"><?= $emoji ?></span>
                        <span class="ob-level-title"><?= $title ?></span>
                        <span class="ob-level-desc"><?= $desc ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>

                <!-- ── Social Links ── -->
                <div class="ob-divider">
                    <span>Social Links <em style="font-style:normal;font-weight:400;color:#ccc;">(optional)</em></span>
                </div>

                <div class="tcm-grid-2">
                    <div class="ob-field">
                        <label class="ob-label" for="github_url">
                            <i class="bi bi-github"></i> GitHub
                        </label>
                        <input class="tcm-input" id="github_url" name="github_url"
                               placeholder="https://github.com/you"
                               value="<?= e($profile['github_url'] ?? '') ?>">
                    </div>
                    <div class="ob-field">
                        <label class="ob-label" for="linkedin_url">
                            <i class="bi bi-linkedin"></i> LinkedIn
                        </label>
                        <input class="tcm-input" id="linkedin_url" name="linkedin_url"
                               placeholder="https://linkedin.com/in/you"
                               value="<?= e($profile['linkedin_url'] ?? '') ?>">
                    </div>
                </div>

                <!-- ── Submit ── -->
                <div class="ob-submit">
                    <button type="submit" class="ob-btn">
                        <i class="bi bi-check2-circle"></i>
                        Save Profile &amp; Go to Dashboard
                        <i class="bi bi-arrow-right"></i>
                    </button>
                    <div class="ob-btn-note">
                        <i class="bi bi-lock-fill" style="font-size:.68rem;"></i>
                        Your data is private. Update anytime from your dashboard.
                    </div>
                </div>

            </form>

        </div>
    </main>

</div>

<script>
function pickLevel(val, el) {
    document.getElementById('exp_hidden').value = val;
    document.querySelectorAll('.ob-level').forEach(function(c) {
        c.classList.remove('active');
    });
    el.classList.add('active');
}
</script>

</body>
</html>
