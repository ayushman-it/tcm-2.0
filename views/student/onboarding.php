<style>
/* ── Onboarding specific ─────────────────────────── */
.ob-header {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 6px;
}
.ob-icon {
    width: 44px; height: 44px;
    background: #111;
    border-radius: 12px;
    display: grid; place-items: center;
    flex-shrink: 0;
    font-size: 1.2rem;
    color: #fff;
}
.ob-title {
    font-size: 1.3rem;
    font-weight: 800;
    color: #111;
    letter-spacing: -.4px;
    margin: 0 0 4px;
    line-height: 1.25;
}
.ob-subtitle {
    font-size: .85rem;
    color: #888;
    margin: 0;
    line-height: 1.55;
}

/* Progress steps */
.ob-steps {
    display: flex;
    align-items: center;
    gap: 0;
    margin: 22px 0 0;
}
.ob-step {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
}
.ob-step-num {
    width: 28px; height: 28px;
    border-radius: 50%;
    display: grid; place-items: center;
    font-size: .75rem;
    font-weight: 700;
    flex-shrink: 0;
    transition: background .2s, color .2s;
}
.ob-step-num.done   { background: #111; color: #fff; }
.ob-step-num.active { background: #111; color: #fff; box-shadow: 0 0 0 3px rgba(0,0,0,.1); }
.ob-step-num.todo   { background: #f0f0f0; color: #aaa; }
.ob-step-label {
    font-size: .72rem;
    font-weight: 600;
    color: #888;
    white-space: nowrap;
}
.ob-step-label.active { color: #111; }
.ob-step-line {
    flex: 1;
    height: 2px;
    background: #ececec;
    margin: 0 8px;
    border-radius: 2px;
}
.ob-step-line.done { background: #111; }

/* Section separator */
.ob-section {
    margin: 24px 0 0;
}
.ob-section-label {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 16px;
}
.ob-section-label span {
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: #999;
    white-space: nowrap;
}
.ob-section-label::before,
.ob-section-label::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #ececec;
}
.ob-section-label::before { display: none; }

/* Field helper text */
.ob-hint {
    font-size: .72rem;
    color: #aaa;
    margin-top: 4px;
    line-height: 1.4;
}

/* Level cards */
.ob-level-group {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}
.ob-level-card {
    border: 1.5px solid #e5e5e5;
    border-radius: 12px;
    padding: 14px 12px;
    cursor: pointer;
    text-align: center;
    transition: border-color .15s, background .15s, transform .15s;
    position: relative;
}
.ob-level-card:hover { border-color: #bbb; background: #fafafa; transform: translateY(-1px); }
.ob-level-card.selected { border-color: #111; background: #111; }
.ob-level-card input[type="radio"] {
    position: absolute; opacity: 0; width: 0; height: 0;
}
.ob-level-emoji { font-size: 1.5rem; display: block; margin-bottom: 6px; }
.ob-level-title {
    font-size: .8rem;
    font-weight: 700;
    color: #111;
    display: block;
}
.ob-level-card.selected .ob-level-title { color: #fff; }
.ob-level-desc {
    font-size: .7rem;
    color: #888;
    margin-top: 2px;
    display: block;
}
.ob-level-card.selected .ob-level-desc { color: rgba(255,255,255,.6); }

/* Submit section */
.ob-submit-wrap {
    margin-top: 28px;
    padding-top: 22px;
    border-top: 1px solid #f0f0f0;
}
.ob-cta {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    width: 100%;
    padding: 14px;
    background: #111;
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: .95rem;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: background .2s, transform .15s, box-shadow .2s;
    letter-spacing: -.1px;
}
.ob-cta:hover {
    background: #333;
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(0,0,0,.15);
}
.ob-cta:active { transform: scale(.98); box-shadow: none; }
.ob-note {
    text-align: center;
    font-size: .75rem;
    color: #bbb;
    margin-top: 12px;
    line-height: 1.5;
}
.ob-note i { font-size: .7rem; }

@media (max-width: 480px) {
    .ob-level-group { grid-template-columns: 1fr; gap: 8px; }
    .ob-level-card { display: flex; align-items: center; gap: 12px; text-align: left; padding: 12px; }
    .ob-level-emoji { margin-bottom: 0; font-size: 1.3rem; flex-shrink: 0; }
    .ob-steps .ob-step-label { display: none; }
}
</style>

<!-- ── Card Header ─────────────────────────────── -->
<div class="auth-card-header">

    <!-- Brand + title -->
    <div class="ob-header">
        <div class="ob-icon">
            <i class="bi bi-person-check"></i>
        </div>
        <div>
            <h1 class="ob-title">Complete your profile</h1>
            <p class="ob-subtitle">
                Help us personalise your experience at The Code Munk.
                Takes less than 2 minutes.
            </p>
        </div>
    </div>

    <!-- Step indicator -->
    <div class="ob-steps">
        <div class="ob-step">
            <div class="ob-step-num done"><i class="bi bi-check" style="font-size:.8rem;"></i></div>
            <div class="ob-step-label">Account</div>
        </div>
        <div class="ob-step-line done"></div>
        <div class="ob-step">
            <div class="ob-step-num active">2</div>
            <div class="ob-step-label active">Profile</div>
        </div>
        <div class="ob-step-line"></div>
        <div class="ob-step">
            <div class="ob-step-num todo">3</div>
            <div class="ob-step-label">Dashboard</div>
        </div>
    </div>

</div>

<!-- ── Card Body ──────────────────────────────── -->
<div class="auth-card-body">

<?php
$_flash_success = flash('success');
$_flash_error   = flash('error');
?>
<?php if ($_flash_success): ?>
    <div class="tcm-flash success" style="margin-bottom:20px;">
        <i class="bi bi-check-circle-fill"></i> <?= e($_flash_success) ?>
    </div>
<?php endif; ?>
<?php if ($_flash_error): ?>
    <div class="tcm-flash error" style="margin-bottom:20px;">
        <i class="bi bi-exclamation-triangle-fill"></i> <?= e($_flash_error) ?>
    </div>
<?php endif; ?>

<form method="post" action="<?= base_url('/student/onboarding') ?>">
    <?= csrf_field() ?>

    <!-- ── Section 1: About you ─── -->
    <div class="ob-section">
        <div class="ob-section-label">
            <span>About You</span>
        </div>

        <div class="tcm-field">
            <label for="headline">Professional Headline</label>
            <input class="tcm-input" id="headline" name="headline"
                   placeholder="e.g. Aspiring Full Stack Developer"
                   value="<?= e($profile['headline'] ?? '') ?>">
            <div class="ob-hint">
                <i class="bi bi-info-circle"></i>
                Shown on your public portfolio page
            </div>
        </div>

        <div class="tcm-field">
            <label for="bio">Short Bio</label>
            <textarea class="tcm-textarea" id="bio" name="bio"
                      placeholder="Tell us what you're learning, building, or working towards…"
                      style="min-height:90px;"><?= e($profile['bio'] ?? '') ?></textarea>
        </div>

        <div class="tcm-grid-2">
            <div class="tcm-field">
                <label for="location">Location</label>
                <input class="tcm-input" id="location" name="location"
                       placeholder="e.g. Delhi, India"
                       value="<?= e($profile['location'] ?? '') ?>">
            </div>
            <div class="tcm-field">
                <label for="goal">Your Main Goal</label>
                <input class="tcm-input" id="goal" name="goal"
                       placeholder="e.g. Land my first dev job"
                       value="<?= e($profile['goal'] ?? '') ?>">
            </div>
        </div>
    </div>

    <!-- ── Section 2: Education ─── -->
    <div class="ob-section">
        <div class="ob-section-label"><span>Education</span></div>

        <div class="tcm-grid-2">
            <div class="tcm-field">
                <label for="college">College / Organisation</label>
                <input class="tcm-input" id="college" name="college"
                       placeholder="e.g. IIT Delhi"
                       value="<?= e($profile['college'] ?? '') ?>">
            </div>
            <div class="tcm-field">
                <label for="graduation_year">Graduation Year</label>
                <input class="tcm-input" type="number" id="graduation_year"
                       name="graduation_year" placeholder="2026"
                       min="2020" max="2035"
                       value="<?= e((string)($profile['graduation_year'] ?? '')) ?>">
            </div>
        </div>
    </div>

    <!-- ── Section 3: Experience level ─── -->
    <div class="ob-section">
        <div class="ob-section-label"><span>Experience Level</span></div>

        <input type="hidden" name="experience_level"
               id="exp_hidden"
               value="<?= e($profile['experience_level'] ?? 'beginner') ?>">

        <div class="ob-level-group">
            <?php
            $levels = [
                'beginner'     => ['🌱', 'Beginner',     'Starting from scratch'],
                'intermediate' => ['⚡', 'Intermediate',  'Some coding experience'],
                'advanced'     => ['🔥', 'Advanced',      'Working professionally'],
            ];
            $current = $profile['experience_level'] ?? 'beginner';
            foreach ($levels as $val => [$emoji, $title, $desc]):
            ?>
            <label class="ob-level-card <?= $current === $val ? 'selected' : '' ?>"
                   onclick="selectLevel('<?= $val ?>', this)">
                <input type="radio" name="_experience_level_ui" value="<?= $val ?>"
                       <?= $current === $val ? 'checked' : '' ?>>
                <span class="ob-level-emoji"><?= $emoji ?></span>
                <span class="ob-level-title"><?= $title ?></span>
                <span class="ob-level-desc"><?= $desc ?></span>
            </label>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ── Section 4: Social links ─── -->
    <div class="ob-section">
        <div class="ob-section-label"><span>Social Links <em style="font-style:normal;color:#ccc;font-weight:400;">(optional)</em></span></div>

        <div class="tcm-grid-2">
            <div class="tcm-field">
                <label for="github_url">
                    <i class="bi bi-github"></i> GitHub
                </label>
                <input class="tcm-input" id="github_url" name="github_url"
                       placeholder="https://github.com/you"
                       value="<?= e($profile['github_url'] ?? '') ?>">
            </div>
            <div class="tcm-field">
                <label for="linkedin_url">
                    <i class="bi bi-linkedin"></i> LinkedIn
                </label>
                <input class="tcm-input" id="linkedin_url" name="linkedin_url"
                       placeholder="https://linkedin.com/in/you"
                       value="<?= e($profile['linkedin_url'] ?? '') ?>">
            </div>
        </div>
    </div>

    <!-- ── Submit ─── -->
    <div class="ob-submit-wrap">
        <button type="submit" class="ob-cta">
            <i class="bi bi-check2-circle"></i>
            Save Profile &amp; Go to Dashboard
            <i class="bi bi-arrow-right"></i>
        </button>
        <p class="ob-note">
            <i class="bi bi-lock-fill"></i>
            Your data is secure. You can update your profile anytime from the dashboard.
        </p>
    </div>

</form>

</div><!-- /.auth-card-body -->

<script>
function selectLevel(val, el) {
    // Update hidden input
    document.getElementById('exp_hidden').value = val;
    // Update card styles
    document.querySelectorAll('.ob-level-card').forEach(function(c) {
        c.classList.remove('selected');
    });
    el.classList.add('selected');
}
// Init selected state on load (in case of browser back)
(function() {
    var val = document.getElementById('exp_hidden').value;
    document.querySelectorAll('.ob-level-card').forEach(function(c) {
        var radio = c.querySelector('input[type="radio"]');
        if (radio && radio.value === val) c.classList.add('selected');
    });
})();
</script>
