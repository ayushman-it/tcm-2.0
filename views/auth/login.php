<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In · The Code Munk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body {
    height: 100%;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    background: #f7f7f7;
    color: #111;
    -webkit-font-smoothing: antialiased;
}

/* ── Shell ─────────────────────────────────────── */
.ln-shell {
    display: grid;
    grid-template-columns: 1fr 520px;
    min-height: 100vh;
}

/* ── Left: Branding ─────────────────────────────── */
.ln-left {
    background: #111;
    color: #fff;
    display: flex;
    flex-direction: column;
    padding: 48px 52px;
    position: sticky;
    top: 0;
    height: 100vh;
    overflow: hidden;
}
.ln-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1rem;
    font-weight: 800;
    color: #fff;
    text-decoration: none;
    margin-bottom: 60px;
}
.ln-brand-icon {
    width: 34px; height: 34px;
    background: #fff;
    border-radius: 9px;
    display: grid; place-items: center;
    font-size: .95rem; color: #111;
    flex-shrink: 0;
}
.ln-tag {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.15);
    border-radius: 999px;
    padding: 5px 14px;
    font-size: .72rem;
    font-weight: 700;
    color: rgba(255,255,255,.6);
    letter-spacing: .1em;
    text-transform: uppercase;
    margin-bottom: 20px;
}
.ln-tag-dot {
    width: 6px; height: 6px;
    background: #fff;
    border-radius: 50%;
    animation: blink 1.6s ease infinite;
}
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.25} }
.ln-left h1 {
    font-size: 2.6rem;
    font-weight: 900;
    line-height: 1.08;
    letter-spacing: -.06em;
    margin-bottom: 18px;
    color: #fff;
}
.ln-left h1 em { font-style: normal; color: rgba(255,255,255,.4); }
.ln-left p {
    font-size: .9rem;
    color: rgba(255,255,255,.5);
    line-height: 1.7;
    max-width: 340px;
    margin-bottom: 44px;
}
.ln-features {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 14px;
    margin-bottom: auto;
}
.ln-feature {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: .85rem;
    color: rgba(255,255,255,.6);
}
.ln-feature i {
    width: 30px; height: 30px;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 8px;
    display: grid; place-items: center;
    font-size: .9rem; color: #fff;
    flex-shrink: 0;
}
.ln-footer {
    padding-top: 28px;
    border-top: 1px solid rgba(255,255,255,.1);
    font-size: .75rem;
    color: rgba(255,255,255,.3);
    line-height: 1.6;
}
.ln-footer a { color: rgba(255,255,255,.45); }
/* Deco */
.ln-deco {
    position: absolute;
    bottom: -80px; right: -80px;
    width: 320px; height: 320px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(255,255,255,.04) 0%, transparent 70%);
    pointer-events: none;
}

/* ── Right: Form panel ─────────────────────────── */
.ln-right {
    background: #fff;
    border-left: 1px solid #ececec;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    min-height: 100vh;
}
.ln-right-inner {
    flex: 1;
    padding: 52px 48px 52px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    max-width: 100%;
}

/* Back link */
.ln-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: .8rem;
    font-weight: 600;
    color: #888;
    text-decoration: none;
    margin-bottom: 36px;
    transition: color .15s;
}
.ln-back:hover { color: #111; }

/* Heading */
.ln-heading {
    margin-bottom: 28px;
}
.ln-heading h2 {
    font-size: 1.6rem;
    font-weight: 800;
    color: #111;
    letter-spacing: -.5px;
    margin-bottom: 6px;
}
.ln-heading p {
    font-size: .875rem;
    color: #888;
    line-height: 1.55;
}

/* Role tabs */
.ln-tabs {
    display: grid;
    grid-template-columns: 1fr 1fr;
    background: #f5f5f5;
    border-radius: 12px;
    padding: 4px;
    gap: 4px;
    margin-bottom: 28px;
}
.ln-tab {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 10px 16px;
    border-radius: 9px;
    font-size: .83rem;
    font-weight: 600;
    color: #888;
    cursor: pointer;
    border: none;
    background: transparent;
    font-family: inherit;
    transition: background .15s, color .15s, box-shadow .15s;
}
.ln-tab i { font-size: .88rem; }
.ln-tab.active {
    background: #fff;
    color: #111;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
}
.ln-tab.active.admin-tab { background: #111; color: #fff; }

/* Form */
.ln-form-section { display: none; }
.ln-form-section.active { display: block; }

.ln-field { margin-bottom: 16px; }
.ln-label {
    display: block;
    font-size: .76rem;
    font-weight: 700;
    color: #555;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: 6px;
}
.ln-input {
    width: 100%;
    padding: 11px 14px;
    border: 1.5px solid #e5e5e5;
    border-radius: 10px;
    font-size: .9rem;
    font-family: inherit;
    color: #111;
    background: #fff;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
}
.ln-input:focus {
    border-color: #111;
    box-shadow: 0 0 0 3px rgba(17,17,17,.07);
}
.ln-input::placeholder { color: #ccc; }

/* Password field with toggle */
.ln-pw-wrap { position: relative; }
.ln-pw-wrap .ln-input { padding-right: 42px; }
.ln-pw-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: #bbb;
    font-size: 1rem;
    padding: 0;
    transition: color .15s;
}
.ln-pw-toggle:hover { color: #666; }

/* Submit button */
.ln-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 13px;
    border: none;
    border-radius: 11px;
    font-size: .9rem;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: background .18s, transform .12s, box-shadow .18s;
    margin-top: 4px;
    letter-spacing: -.1px;
}
.ln-btn.student {
    background: #111;
    color: #fff;
}
.ln-btn.student:hover {
    background: #333;
    transform: translateY(-1px);
    box-shadow: 0 5px 18px rgba(0,0,0,.18);
}
.ln-btn.admin-btn {
    background: #111;
    color: #fff;
}
.ln-btn.admin-btn:hover {
    background: #1a1a1a;
    transform: translateY(-1px);
    box-shadow: 0 5px 18px rgba(0,0,0,.22);
}
.ln-btn:active { transform: scale(.98) !important; box-shadow: none !important; }

/* Flash */
.ln-flash {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 11px 14px;
    border-radius: 9px;
    margin-bottom: 20px;
    font-size: .84rem;
    font-weight: 500;
    border: 1px solid;
}
.ln-flash.success { background: #f0fdf4; border-color: #bbf7d0; color: #15803d; }
.ln-flash.error   { background: #fef2f2; border-color: #fecaca; color: #dc2626; }

/* Footer links */
.ln-form-footer {
    margin-top: 20px;
    text-align: center;
    font-size: .8rem;
    color: #aaa;
    line-height: 1.6;
}
.ln-form-footer a { color: #111; font-weight: 600; }

/* Admin badge */
.ln-admin-badge {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #f9f9f9;
    border: 1px solid #e5e5e5;
    border-radius: 10px;
    padding: 12px 14px;
    margin-bottom: 20px;
    font-size: .8rem;
    color: #666;
}
.ln-admin-badge i {
    font-size: 1.1rem;
    color: #111;
    flex-shrink: 0;
}
.ln-admin-badge strong { color: #111; display: block; margin-bottom: 1px; font-size: .82rem; }

/* Divider */
.ln-divider {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 20px 0;
}
.ln-divider::before, .ln-divider::after {
    content: ''; flex: 1; height: 1px; background: #f0f0f0;
}
.ln-divider span { font-size: .72rem; color: #ccc; font-weight: 600; letter-spacing: .05em; }

/* Google button */
.ln-google {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    padding: 11px 16px;
    border: 1.5px solid #e5e5e5;
    border-radius: 10px;
    background: #fff;
    font-size: .85rem;
    font-weight: 600;
    color: #333;
    cursor: pointer;
    text-decoration: none;
    font-family: inherit;
    transition: border-color .15s, background .15s;
    margin-bottom: 6px;
}
.ln-google:hover { border-color: #bbb; background: #fafafa; color: #111; }
.ln-google svg { width: 16px; height: 16px; flex-shrink: 0; }

/* ── Responsive ── */
@media (max-width: 900px) {
    .ln-shell { grid-template-columns: 1fr; }
    .ln-left  { display: none; }
    .ln-right { border-left: none; }
    .ln-right-inner { padding: 36px 24px 48px; justify-content: flex-start; }
    .ln-back { margin-bottom: 28px; }
}
@media (max-width: 420px) {
    .ln-right-inner { padding: 24px 18px 36px; }
    .ln-heading h2 { font-size: 1.4rem; }
}
</style>
</head>
<body>

<div class="ln-shell">

    <!-- ── Left Panel ──────────────────────────────── -->
    <aside class="ln-left">
        <a href="<?= base_url('/') ?>" class="ln-brand">
            <div class="ln-brand-icon"><i class="bi bi-code-slash"></i></div>
            The Code Munk
        </a>

        <div class="ln-tag">
            <span class="ln-tag-dot"></span>
            Welcome back
        </div>

        <h1>
            Learn. Build.<br>
            <em>Get</em> Hired.
        </h1>

        <p>
            Sign in to access your personalised dashboard,
            live classes, projects and developer portfolio.
        </p>

        <ul class="ln-features">
            <li class="ln-feature">
                <i class="bi bi-journal-code"></i>
                Access all your enrolled courses
            </li>
            <li class="ln-feature">
                <i class="bi bi-broadcast"></i>
                Join upcoming live sessions
            </li>
            <li class="ln-feature">
                <i class="bi bi-briefcase"></i>
                Build and share your portfolio
            </li>
            <li class="ln-feature">
                <i class="bi bi-patch-check"></i>
                Track certificates and progress
            </li>
        </ul>

        <div class="ln-footer">
            New to The Code Munk?
            <a href="<?= base_url('/auth/register') ?>">Create a free account →</a><br>
            <br>© <?= date('Y') ?> The Code Munk. All rights reserved.
        </div>

        <div class="ln-deco"></div>
    </aside>

    <!-- ── Right Panel ─────────────────────────────── -->
    <main class="ln-right">
        <div class="ln-right-inner">

            <!-- Back link -->
            <a href="<?= base_url('/') ?>" class="ln-back">
                <i class="bi bi-arrow-left"></i> Back to homepage
            </a>

            <!-- Heading -->
            <div class="ln-heading">
                <h2>Sign in to your account</h2>
                <p>Choose your account type and enter your credentials below.</p>
            </div>

            <!-- Flash messages -->
            <?php
            $flashError   = flash('error');
            $flashSuccess = flash('success');
            ?>
            <?php if ($flashError): ?>
                <div class="ln-flash error">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <?= e($flashError) ?>
                </div>
            <?php endif; ?>
            <?php if ($flashSuccess): ?>
                <div class="ln-flash success">
                    <i class="bi bi-check-circle-fill"></i>
                    <?= e($flashSuccess) ?>
                </div>
            <?php endif; ?>

            <!-- Role tabs -->
            <div class="ln-tabs" role="tablist">
                <button class="ln-tab active" id="tab-student"
                        onclick="switchTab('student')" type="button">
                    <i class="bi bi-person-circle"></i> Student
                </button>
                <button class="ln-tab admin-tab" id="tab-admin"
                        onclick="switchTab('admin')" type="button">
                    <i class="bi bi-shield-lock"></i> Admin
                </button>
            </div>

            <!-- ── Student form ── -->
            <div class="ln-form-section active" id="section-student">

                <!-- Google login -->
                <a href="<?= base_url('/auth/google') ?>" class="ln-google">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    Continue with Google
                </a>

                <div class="ln-divider"><span>or sign in with email</span></div>

                <form method="post" action="<?= base_url('/auth/login') ?>">
                    <?= csrf_field() ?>
                    <div class="ln-field">
                        <label class="ln-label" for="s-email">Email address</label>
                        <input class="ln-input" type="email" id="s-email" name="email"
                               value="<?= e(old('email')) ?>"
                               placeholder="you@gmail.com"
                               autocomplete="email" required autofocus>
                    </div>
                    <div class="ln-field">
                        <label class="ln-label" for="s-password">Password</label>
                        <div class="ln-pw-wrap">
                            <input class="ln-input" type="password" id="s-password"
                                   name="password"
                                   placeholder="Your password"
                                   autocomplete="current-password" required>
                            <button type="button" class="ln-pw-toggle"
                                    onclick="togglePw('s-password', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="ln-btn student">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Sign In
                    </button>
                </form>

                <div class="ln-form-footer">
                    Don't have an account?
                    <a href="<?= base_url('/auth/register') ?>">Create one free</a>
                </div>

            </div>

            <!-- ── Admin form ── -->
            <div class="ln-form-section" id="section-admin">

                <div class="ln-admin-badge">
                    <i class="bi bi-shield-fill-check"></i>
                    <div>
                        <strong>Admin Access</strong>
                        Restricted to authorised team members only.
                    </div>
                </div>

                <form method="post" action="<?= base_url('/auth/login') ?>">
                    <?= csrf_field() ?>
                    <div class="ln-field">
                        <label class="ln-label" for="a-email">Admin email</label>
                        <input class="ln-input" type="email" id="a-email" name="email"
                               placeholder="admin@thecodemunk.com"
                               autocomplete="username" required>
                    </div>
                    <div class="ln-field">
                        <label class="ln-label" for="a-password">Password</label>
                        <div class="ln-pw-wrap">
                            <input class="ln-input" type="password" id="a-password"
                                   name="password"
                                   placeholder="Admin password"
                                   autocomplete="current-password" required>
                            <button type="button" class="ln-pw-toggle"
                                    onclick="togglePw('a-password', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="ln-btn admin-btn">
                        <i class="bi bi-shield-lock"></i>
                        Access Admin Panel
                    </button>
                </form>

                <div class="ln-form-footer">
                    Not an admin?
                    <a href="javascript:void(0)" onclick="switchTab('student')">
                        Switch to student login
                    </a>
                </div>

            </div>

        </div>
    </main>

</div>

<script>
function switchTab(role) {
    // Update tab buttons
    document.querySelectorAll('.ln-tab').forEach(function(t) {
        t.classList.remove('active');
    });
    document.getElementById('tab-' + role).classList.add('active');

    // Update form sections
    document.querySelectorAll('.ln-form-section').forEach(function(s) {
        s.classList.remove('active');
    });
    document.getElementById('section-' + role).classList.add('active');

    // Focus email field
    var emailField = document.getElementById(role === 'admin' ? 'a-email' : 's-email');
    if (emailField) setTimeout(function() { emailField.focus(); }, 50);
}

function togglePw(inputId, btn) {
    var inp = document.getElementById(inputId);
    var icon = btn.querySelector('i');
    if (!inp) return;
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        inp.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

// Auto-switch to admin tab if email looks like admin (from old() value)
(function() {
    var email = document.getElementById('s-email');
    if (email && email.value && email.value.indexOf('admin') !== -1) {
        switchTab('admin');
    }
})();
</script>
</body>
</html>
