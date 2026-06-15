/**
 * auth.js – Auth Modal Logic
 * The Code Munk
 *
 * Views:
 *  - landing   → choose method (Google / Email OTP / Email+Password)
 *  - email-otp → enter email → send OTP → enter OTP
 *  - login     → email + password login
 *  - register  → email + password register
 *  - forgot    → enter email → send OTP → enter OTP → new password
 *  - success   → welcome screen
 */

(function () {
    'use strict';

    /* ─── HTML template ─────────────────────────────────── */
    const MODAL_HTML = `
    <div class="auth-overlay" id="authOverlay">
        <div class="auth-modal" role="dialog" aria-modal="true" aria-label="Sign in">

            <button class="auth-close" id="authClose" aria-label="Close">
                <i class="bi bi-x-lg"></i>
            </button>

            <!-- Brand strip -->
            <div class="auth-brand">
                <span class="auth-brand-logo">TCM</span>
            </div>

            <div class="auth-modal-body">
            <div class="auth-view active" id="view-landing">

                <div class="auth-view-header">
                    <h2 class="auth-heading">Sign In</h2>
                    <p class="auth-subheading">Access your courses, dashboard and community.</p>
                </div>

                <!-- Google -->
                <a href="#" class="auth-google-btn" id="googleLoginBtn">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    Continue with Google
                </a>

                <div class="auth-divider"><span>or continue with email</span></div>

                <!-- Method tabs -->
                <div class="auth-tabs">
                    <button class="auth-tab-btn active" data-tab="otp">
                        <i class="bi bi-envelope"></i> OTP Login
                    </button>
                    <button class="auth-tab-btn" data-tab="password">
                        <i class="bi bi-lock"></i> Password
                    </button>
                </div>

                <!-- OTP sub-form -->
                <div id="landing-otp-form">
                    <div class="auth-form-group">
                        <label>Email Address</label>
                        <div class="auth-input-btn-wrap">
                            <input type="email" id="landing-email" placeholder="you@gmail.com" autocomplete="email">
                            <button class="auth-send-otp-btn" id="landingSendOtp">Send OTP</button>
                        </div>
                        <span class="auth-field-err">Enter a valid email.</span>
                    </div>
                </div>

                <!-- Password sub-form -->
                <div id="landing-pw-form" style="display:none">
                    <div class="auth-form-group">
                        <label>Email Address</label>
                        <input type="email" id="landing-pw-email" placeholder="you@gmail.com" autocomplete="email">
                        <span class="auth-field-err">Enter a valid email.</span>
                    </div>
                    <button class="auth-submit-btn" id="landingPwNext">
                        <span class="auth-btn-label">Continue <i class="bi bi-arrow-right"></i></span>
                        <span class="auth-btn-spin"></span>
                    </button>
                </div>

                <p class="auth-terms">
                    By continuing you agree to our <a href="#">Terms</a> &amp; <a href="#">Privacy Policy</a>.
                </p>
            </div>

            <!-- ========== EMAIL OTP VIEW ========== -->
            <div class="auth-view" id="view-email-otp">
                <button class="auth-back-btn" data-back="landing">
                    <i class="bi bi-arrow-left"></i> Back
                </button>
                <div class="auth-view-header">
                    <h2 class="auth-heading">Enter OTP</h2>
                    <p class="auth-subheading" id="otp-sent-to">Sent to <strong>you@email.com</strong></p>
                </div>

                <div class="auth-form-group">
                    <div class="auth-otp-wrap" id="otpInputs">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="one-time-code">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
                    </div>
                    <span class="auth-field-err">Please enter the 6-digit OTP.</span>
                </div>

                <div class="auth-otp-timer" id="otpTimer">
                    Resend in <span id="otpCountdown">30</span>s
                </div>

                <button class="auth-submit-btn" id="verifyOtpBtn">
                    <span class="auth-btn-label">Verify &amp; Sign In</span>
                    <span class="auth-btn-spin"></span>
                </button>
            </div>

            <!-- ========== LOGIN VIEW ========== -->
            <div class="auth-view" id="view-login">
                <button class="auth-back-btn" data-back="landing">
                    <i class="bi bi-arrow-left"></i> Back
                </button>
                <div class="auth-view-header">
                    <h2 class="auth-heading">Welcome Back</h2>
                    <p class="auth-subheading" id="login-email-display">Signing in as <strong></strong></p>
                </div>

                <div class="auth-form-group">
                    <label>Password</label>
                    <div class="auth-pw-wrap">
                        <input type="password" id="loginPassword" placeholder="Your password" autocomplete="current-password">
                        <button class="auth-pw-toggle" data-target="loginPassword" type="button">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <span class="auth-field-err">Incorrect password.</span>
                </div>

                <button class="auth-submit-btn" id="loginSubmitBtn">
                    <span class="auth-btn-label">Sign In <i class="bi bi-arrow-right"></i></span>
                    <span class="auth-btn-spin"></span>
                </button>

                <div class="auth-link-row">
                    <span class="auth-text-link" id="goToRegister">Create account</span>
                    <span class="auth-text-link primary" id="goToForgot">Forgot password?</span>
                </div>
            </div>

            <!-- ========== REGISTER VIEW ========== -->
            <div class="auth-view" id="view-register">
                <button class="auth-back-btn" data-back="login">
                    <i class="bi bi-arrow-left"></i> Back
                </button>
                <div class="auth-view-header">
                    <h2 class="auth-heading">Create Account</h2>
                    <p class="auth-subheading">Join 1,200+ students at TCM.</p>
                </div>

                <div class="auth-form-group">
                    <label>Full Name</label>
                    <input type="text" id="regName" placeholder="Rahul Sharma" autocomplete="name">
                    <span class="auth-field-err">Please enter your name.</span>
                </div>
                <div class="auth-form-group">
                    <label>Email</label>
                    <input type="email" id="regEmail" placeholder="you@gmail.com" autocomplete="email">
                    <span class="auth-field-err">Enter a valid email.</span>
                </div>
                <div class="auth-form-group">
                    <label>Password</label>
                    <div class="auth-pw-wrap">
                        <input type="password" id="regPassword" placeholder="Min 8 characters" autocomplete="new-password">
                        <button class="auth-pw-toggle" data-target="regPassword" type="button">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <span class="auth-field-err">Min 8 characters.</span>
                </div>

                <button class="auth-submit-btn" id="registerBtn">
                    <span class="auth-btn-label">Create Account</span>
                    <span class="auth-btn-spin"></span>
                </button>

                <p class="auth-terms">
                    By registering you agree to our <a href="#">Terms</a> &amp; <a href="#">Privacy Policy</a>.
                </p>
            </div>

            <!-- ========== FORGOT – STEP 1 ========== -->
            <div class="auth-view" id="view-forgot-email">
                <button class="auth-back-btn" data-back="login">
                    <i class="bi bi-arrow-left"></i> Back
                </button>
                <div class="auth-view-header">
                    <h2 class="auth-heading">Forgot Password</h2>
                    <p class="auth-subheading">We'll send an OTP to reset your password.</p>
                </div>

                <div class="auth-form-group">
                    <label>Email Address</label>
                    <div class="auth-input-btn-wrap">
                        <input type="email" id="forgotEmail" placeholder="you@gmail.com">
                        <button class="auth-send-otp-btn" id="forgotSendOtp">Send OTP</button>
                    </div>
                    <span class="auth-field-err">Enter a valid email.</span>
                </div>
            </div>

            <!-- ========== FORGOT – STEP 2 ========== -->
            <div class="auth-view" id="view-forgot-otp">
                <button class="auth-back-btn" data-back="forgot-email">
                    <i class="bi bi-arrow-left"></i> Back
                </button>
                <div class="auth-view-header">
                    <h2 class="auth-heading">Reset Password</h2>
                    <p class="auth-subheading">Enter OTP and choose a new password.</p>
                </div>

                <div class="auth-form-group">
                    <label>OTP</label>
                    <div class="auth-otp-wrap" id="forgotOtpInputs">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
                    </div>
                </div>
                <div class="auth-form-group">
                    <label>New Password</label>
                    <div class="auth-pw-wrap">
                        <input type="password" id="newPassword" placeholder="Min 8 characters" autocomplete="new-password">
                        <button class="auth-pw-toggle" data-target="newPassword" type="button">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <span class="auth-field-err">Min 8 characters.</span>
                </div>
                <div class="auth-form-group">
                    <label>Confirm Password</label>
                    <div class="auth-pw-wrap">
                        <input type="password" id="confirmPassword" placeholder="Repeat password">
                        <button class="auth-pw-toggle" data-target="confirmPassword" type="button">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <span class="auth-field-err">Passwords do not match.</span>
                </div>

                <button class="auth-submit-btn" id="resetPasswordBtn">
                    <span class="auth-btn-label">Reset Password</span>
                    <span class="auth-btn-spin"></span>
                </button>
            </div>

            <!-- ========== SUCCESS VIEW ========== -->
            <div class="auth-view" id="view-success">
                <div class="auth-success">
                    <div class="auth-success-icon">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <h3 id="successTitle">You're In!</h3>
                    <p id="successMsg">Welcome to The Code Munk.</p>
                </div>
            </div>

            </div><!-- end auth-modal-body -->

        </div>
    </div>`;

    /* ─── helpers ─────────────────────────────────────── */
    const $ = id => document.getElementById(id);
    const isEmail = v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim());

    function showView(id) {
        document.querySelectorAll('.auth-view').forEach(v => v.classList.remove('active'));
        const el = $('view-' + id);
        if (el) el.classList.add('active');
    }

    function setLoading(btn, on) {
        btn.classList.toggle('loading', on);
        btn.disabled = on;
    }

    function fieldError(inputEl, msg) {
        const group = inputEl.closest('.auth-form-group');
        if (!group) return;
        group.classList.add('has-error');
        const err = group.querySelector('.auth-field-err');
        if (err && msg) err.textContent = msg;
    }

    function clearErrors() {
        document.querySelectorAll('.auth-form-group.has-error').forEach(g => g.classList.remove('has-error'));
    }

    /* ─── OTP box auto-advance ───────────────────────── */
    function setupOtpInputs(wrapperId) {
        const wrap = $(wrapperId);
        if (!wrap) return;
        const inputs = wrap.querySelectorAll('input');
        inputs.forEach((inp, i) => {
            inp.addEventListener('input', () => {
                inp.value = inp.value.replace(/\D/, '');
                if (inp.value && i < inputs.length - 1) inputs[i + 1].focus();
            });
            inp.addEventListener('keydown', e => {
                if (e.key === 'Backspace' && !inp.value && i > 0) inputs[i - 1].focus();
            });
            inp.addEventListener('paste', e => {
                e.preventDefault();
                const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
                [...text].slice(0, 6).forEach((ch, j) => {
                    if (inputs[j]) inputs[j].value = ch;
                });
                if (inputs[Math.min(text.length, 5)]) inputs[Math.min(text.length, 5)].focus();
            });
        });
    }

    function getOtp(wrapperId) {
        return [...($(wrapperId)?.querySelectorAll('input') || [])].map(i => i.value).join('');
    }

    /* ─── countdown ──────────────────────────────────── */
    let countdownTimer = null;
    function startCountdown(secs, display, resendEl) {
        clearInterval(countdownTimer);
        let s = secs;
        display.textContent = s;
        if (resendEl) resendEl.disabled = true;
        countdownTimer = setInterval(() => {
            s--;
            display.textContent = s;
            if (s <= 0) {
                clearInterval(countdownTimer);
                if (display.closest('.auth-otp-timer')) {
                    display.closest('.auth-otp-timer').innerHTML =
                        '<span class="auth-text-link primary" id="resendOtp" style="cursor:pointer">Resend OTP</span>';
                    const resend = $('resendOtp');
                    if (resend) resend.onclick = () => { startCountdown(30, display); };
                }
                if (resendEl) resendEl.disabled = false;
            }
        }, 1000);
    }

    /* ─── simulate async (replace with real API later) ─ */
    function fakeAsync(ms) { return new Promise(r => setTimeout(r, ms)); }

    /* ─── Base path from meta tag ────────────────────── */
    const BASE = document.querySelector('meta[name="app-base"]')?.content || '';

    /* ─── Real API helper ────────────────────────────── */
    async function apiPost(endpoint, data) {
        const resp = await fetch(BASE + endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(data),
        });
        return resp.json();
    }

    /* ─── state ─────────────────────────────────────── */
    let authEmail = '';

    /* ─── INIT ──────────────────────────────────────── */
    function init() {
        // Inject modal HTML
        document.body.insertAdjacentHTML('beforeend', MODAL_HTML);

        const overlay = $('authOverlay');
        const closeBtn = $('authClose');

        // OTP inputs
        setupOtpInputs('otpInputs');
        setupOtpInputs('forgotOtpInputs');

        // Close
        closeBtn.addEventListener('click', closeModal);
        overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

        /* ─── Back buttons ─── */
        document.querySelectorAll('.auth-back-btn[data-back]').forEach(btn => {
            btn.addEventListener('click', () => showView(btn.dataset.back));
        });

        /* ─── Tab switcher (OTP vs Password) ─── */
        document.querySelectorAll('.auth-tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.auth-tab-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                clearErrors();
                if (btn.dataset.tab === 'otp') {
                    $('landing-otp-form').style.display = '';
                    $('landing-pw-form').style.display = 'none';
                } else {
                    $('landing-otp-form').style.display = 'none';
                    $('landing-pw-form').style.display = '';
                }
            });
        });

        /* ─── Google ─── */
        $('googleLoginBtn').addEventListener('click', e => {
            e.preventDefault();
            // Redirect to backend Google OAuth route
            // BASE_PATH is injected by the backend via a <meta> tag, or falls back to empty string
            const base = document.querySelector('meta[name="app-base"]')?.content || '';
            window.location.href = base + '/auth/google';
        });

        /* ─── Send OTP (landing) ─── */
        $('landingSendOtp').addEventListener('click', async () => {
            clearErrors();
            const emailEl = $('landing-email');
            if (!isEmail(emailEl.value)) { fieldError(emailEl, 'Enter a valid email.'); return; }
            authEmail = emailEl.value.trim();

            const btn = $('landingSendOtp');
            btn.disabled = true;
            btn.textContent = 'Sending…';

            const res = await apiPost('/auth/otp/request', { email: authEmail }).catch(() => null);

            if (!res || !res.success) {
                btn.disabled = false;
                btn.textContent = 'Send OTP';
                fieldError(emailEl, res?.message || 'Failed to send OTP. Try again.');
                return;
            }

            btn.textContent = 'Sent ✓';
            $('otp-sent-to').innerHTML = `We sent a 6-digit OTP to <strong>${authEmail}</strong>`;
            showView('email-otp');
            startCountdown(30, $('otpCountdown'));
            document.querySelector('#otpInputs input')?.focus();
        });

        /* ─── Verify OTP (email-otp view) ─── */
        $('verifyOtpBtn').addEventListener('click', async () => {
            const otp = getOtp('otpInputs');
            if (otp.length < 6) {
                fieldError(document.querySelector('#view-email-otp .auth-form-group'), 'Please enter the 6-digit OTP.');
                return;
            }
            const btn = $('verifyOtpBtn');
            setLoading(btn, true);

            const res = await apiPost('/auth/otp/verify', { email: authEmail, otp }).catch(() => null);

            setLoading(btn, false);
            if (!res || !res.success) {
                fieldError(document.querySelector('#view-email-otp .auth-form-group'), res?.message || 'Invalid or expired OTP.');
                return;
            }
            showSuccess('You\'re In! 🎉', 'Signed in successfully. Redirecting…');
            setTimeout(() => { window.location.href = res.data?.redirect || (BASE + '/student'); }, 1200);
        });

        /* ─── Continue with password (landing) ─── */
        $('landingPwNext').addEventListener('click', async () => {
            clearErrors();
            const emailEl = $('landing-pw-email');
            if (!isEmail(emailEl.value)) { fieldError(emailEl, 'Enter a valid email.'); return; }
            authEmail = emailEl.value.trim();

            $('login-email-display').innerHTML = `Signing in as <strong>${authEmail}</strong>`;
            $('regEmail').value = authEmail;
            showView('login');
        });

        /* ─── Login submit ─── */
        $('loginSubmitBtn').addEventListener('click', async () => {
            clearErrors();
            const pwEl = $('loginPassword');
            if (!pwEl.value) { fieldError(pwEl, 'Please enter your password.'); return; }

            const btn = $('loginSubmitBtn');
            setLoading(btn, true);

            const res = await apiPost('/auth/login', { email: authEmail, password: pwEl.value }).catch(() => null);

            setLoading(btn, false);
            if (!res || !res.success) {
                fieldError(pwEl, res?.message || 'Invalid email or password.');
                return;
            }
            showSuccess('Welcome back! 👋', `Signed in as ${authEmail}`);
            setTimeout(() => { window.location.href = res.data?.redirect || (BASE + '/student'); }, 1200);
        });

        /* ─── Go to register ─── */
        $('goToRegister').addEventListener('click', () => showView('register'));

        /* ─── Go to forgot ─── */
        $('goToForgot').addEventListener('click', () => {
            $('forgotEmail').value = authEmail;
            showView('forgot-email');
        });

        /* ─── Register ─── */
        $('registerBtn').addEventListener('click', async () => {
            clearErrors();
            let ok = true;
            const nameEl = $('regName');
            const emailEl = $('regEmail');
            const pwEl   = $('regPassword');

            if (!nameEl.value.trim()) { fieldError(nameEl, 'Please enter your name.'); ok = false; }
            if (!isEmail(emailEl.value)) { fieldError(emailEl, 'Enter a valid email.'); ok = false; }
            if (pwEl.value.length < 8) { fieldError(pwEl, 'Password must be at least 8 characters.'); ok = false; }
            if (!ok) return;

            const btn = $('registerBtn');
            setLoading(btn, true);

            const res = await apiPost('/auth/register', {
                name: nameEl.value.trim(),
                email: emailEl.value.trim(),
                password: pwEl.value,
                password_confirmation: pwEl.value,
            }).catch(() => null);

            setLoading(btn, false);
            if (!res || !res.success) {
                fieldError(emailEl, res?.message || 'Registration failed. Try again.');
                return;
            }
            showSuccess('Account Created! 🎉', `Welcome, ${nameEl.value.split(' ')[0]}! Your journey starts now.`);
            setTimeout(() => { window.location.href = res.data?.redirect || (BASE + '/student/onboarding'); }, 1200);
        });

        /* ─── Forgot – Send OTP ─── */
        $('forgotSendOtp').addEventListener('click', async () => {
            clearErrors();
            const emailEl = $('forgotEmail');
            if (!isEmail(emailEl.value)) { fieldError(emailEl, 'Enter a valid email.'); return; }

            const btn = $('forgotSendOtp');
            btn.disabled = true; btn.textContent = 'Sending…';

            const res = await apiPost('/auth/password/request', { email: emailEl.value.trim() }).catch(() => null);

            if (!res || !res.success) {
                btn.disabled = false; btn.textContent = 'Send OTP';
                fieldError(emailEl, res?.message || 'Failed to send OTP.');
                return;
            }
            btn.textContent = 'Sent ✓';
            showView('forgot-otp');
            document.querySelector('#forgotOtpInputs input')?.focus();
        });

        /* ─── Reset password ─── */
        $('resetPasswordBtn').addEventListener('click', async () => {
            clearErrors();
            const newPw  = $('newPassword');
            const confPw = $('confirmPassword');
            let ok = true;

            const otp = getOtp('forgotOtpInputs');
            if (otp.length < 6) { ok = false; }
            if (newPw.value.length < 8) { fieldError(newPw, 'Password must be at least 8 characters.'); ok = false; }
            if (newPw.value !== confPw.value) { fieldError(confPw, 'Passwords do not match.'); ok = false; }
            if (!ok) return;

            const btn = $('resetPasswordBtn');
            setLoading(btn, true);

            const res = await apiPost('/auth/password/reset', {
                email: $('forgotEmail').value.trim(),
                otp,
                password: newPw.value,
            }).catch(() => null);

            setLoading(btn, false);
            if (!res || !res.success) {
                fieldError(confPw, res?.message || 'Reset failed. Please try again.');
                return;
            }
            showSuccess('Password Reset! ✅', 'Your password has been updated.');
            setTimeout(() => { window.location.href = res.data?.redirect || (BASE + '/student'); }, 1200);
        });

        /* ─── Password toggle ─── */
        document.querySelectorAll('.auth-pw-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                const inp = $(btn.dataset.target);
                const icon = btn.querySelector('i');
                if (!inp) return;
                if (inp.type === 'password') {
                    inp.type = 'text';
                    icon.className = 'bi bi-eye-slash';
                } else {
                    inp.type = 'password';
                    icon.className = 'bi bi-eye';
                }
            });
        });
    }

    /* ─── Success screen ──────────────────────────────── */
    function showSuccess(title, msg) {
        $('successTitle').textContent = title;
        $('successMsg').textContent   = msg;
        showView('success');
        setTimeout(closeModal, 2500);
    }

    /* ─── Open / Close ─────────────────────────────────── */
    function openModal() {
        clearErrors();
        showView('landing');
        $('authOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
        setTimeout(() => $('landing-email')?.focus(), 300);
    }

    function closeModal() {
        $('authOverlay').classList.remove('active');
        document.body.style.overflow = '';
    }

    /* ─── Attach to all join-btn triggers ─────────────── */
    function attachTriggers() {
        document.querySelectorAll('.join-btn, [data-auth-trigger]').forEach(el => {
            el.addEventListener('click', e => {
                e.preventDefault();
                openModal();
            });
        });
    }

    /* ─── Bootstrap ─────────────────────────────────────── */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => { init(); attachTriggers(); });
    } else {
        init(); attachTriggers();
    }

    // Expose globally in case needed
    window.TCMAuth = { open: openModal, close: closeModal };

})();
