<h1>Create your account</h1>
<p class="sub">Start learning and build your developer portfolio.</p>

<form method="post" action="<?= base_url('/auth/register') ?>">
    <?= csrf_field() ?>
    <div class="tcm-field">
        <label for="name">Full name</label>
        <input class="tcm-input" type="text" id="name" name="name" value="<?= e(old('name')) ?>" required autofocus>
    </div>
    <div class="tcm-field">
        <label for="email">Email</label>
        <input class="tcm-input" type="email" id="email" name="email" value="<?= e(old('email')) ?>" required>
    </div>
    <div class="tcm-grid-2">
        <div class="tcm-field">
            <label for="password">Password</label>
            <input class="tcm-input" type="password" id="password" name="password" required>
        </div>
        <div class="tcm-field">
            <label for="password_confirmation">Confirm</label>
            <input class="tcm-input" type="password" id="password_confirmation" name="password_confirmation" required>
        </div>
    </div>
    <button class="tcm-btn primary" style="width:100%;justify-content:center;">
        <i class="bi bi-person-plus"></i> Create Account
    </button>
</form>

<p class="muted" style="margin-top:18px;text-align:center;font-size:.88rem;">
    Already have an account? <a href="<?= base_url('/auth/login') ?>">Sign in</a>
</p>
