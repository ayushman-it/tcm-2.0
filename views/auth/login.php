<h1>Welcome back</h1>
<p class="sub">Sign in to your Code Munk account.</p>

<form method="post" action="<?= base_url('/auth/login') ?>">
    <?= csrf_field() ?>
    <div class="tcm-field">
        <label for="email">Email</label>
        <input class="tcm-input" type="email" id="email" name="email" value="<?= e(old('email')) ?>" required autofocus>
    </div>
    <div class="tcm-field">
        <label for="password">Password</label>
        <input class="tcm-input" type="password" id="password" name="password" required>
    </div>
    <button class="tcm-btn primary" style="width:100%;justify-content:center;">
        <i class="bi bi-box-arrow-in-right"></i> Sign In
    </button>
</form>

<p class="muted" style="margin-top:18px;text-align:center;font-size:.88rem;">
    New here? <a href="<?= base_url('/auth/register') ?>">Create an account</a>
</p>

<div class="tcm-card" style="margin-top:18px;padding:14px;font-size:.8rem;">
    <strong>Demo logins</strong><br>
    Admin: admin@thecodemunk.com / admin123<br>
    Student: student@thecodemunk.com / student123
</div>
