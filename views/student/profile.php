<div class="tcm-page-head">
    <div><h2>My Profile</h2><p>Manage your account and personal details.</p></div>
</div>

<div class="tcm-grid-2" style="align-items:start;">
    <form method="post" action="<?= base_url('/student/profile') ?>" class="tcm-card">
        <?= csrf_field() ?>
        <h3 class="mt-0">Details</h3>
        <div class="tcm-grid-2">
            <div class="tcm-field"><label>Name</label><input class="tcm-input" name="name" value="<?= e($user['name']) ?>" required></div>
            <div class="tcm-field"><label>Phone</label><input class="tcm-input" name="phone" value="<?= e($user['phone'] ?? '') ?>"></div>
        </div>
        <div class="tcm-field"><label>Email</label><input class="tcm-input" value="<?= e($user['email']) ?>" disabled></div>
        <div class="tcm-field"><label>Headline</label><input class="tcm-input" name="headline" value="<?= e($profile['headline'] ?? '') ?>"></div>
        <div class="tcm-field"><label>Bio</label><textarea class="tcm-textarea" name="bio"><?= e($profile['bio'] ?? '') ?></textarea></div>
        <div class="tcm-grid-2">
            <div class="tcm-field"><label>College</label><input class="tcm-input" name="college" value="<?= e($profile['college'] ?? '') ?>"></div>
            <div class="tcm-field"><label>Graduation year</label><input class="tcm-input" type="number" name="graduation_year" value="<?= e((string)($profile['graduation_year'] ?? '')) ?>"></div>
        </div>
        <div class="tcm-grid-2">
            <div class="tcm-field">
                <label>Experience</label>
                <select class="tcm-select" name="experience_level">
                    <?php foreach (['beginner','intermediate','advanced'] as $lvl): ?>
                        <option value="<?= $lvl ?>" <?= ($profile['experience_level'] ?? '') === $lvl ? 'selected' : '' ?>><?= ucfirst($lvl) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="tcm-field"><label>Location</label><input class="tcm-input" name="location" value="<?= e($profile['location'] ?? '') ?>"></div>
        </div>
        <div class="tcm-field"><label>Goal</label><input class="tcm-input" name="goal" value="<?= e($profile['goal'] ?? '') ?>"></div>
        <div class="tcm-grid-2">
            <div class="tcm-field"><label>GitHub</label><input class="tcm-input" name="github_url" value="<?= e($profile['github_url'] ?? '') ?>"></div>
            <div class="tcm-field"><label>LinkedIn</label><input class="tcm-input" name="linkedin_url" value="<?= e($profile['linkedin_url'] ?? '') ?>"></div>
        </div>
        <div class="tcm-grid-2">
            <div class="tcm-field"><label>Website</label><input class="tcm-input" name="website_url" value="<?= e($profile['website_url'] ?? '') ?>"></div>
            <div class="tcm-field"><label>Twitter / X</label><input class="tcm-input" name="twitter_url" value="<?= e($profile['twitter_url'] ?? '') ?>"></div>
        </div>
        <button class="tcm-btn primary"><i class="bi bi-check2"></i> Save Profile</button>
    </form>

    <div>
        <form method="post" action="<?= base_url('/student/profile/password') ?>" class="tcm-card">
            <?= csrf_field() ?>
            <h3 class="mt-0">Change password</h3>
            <div class="tcm-field"><label>Current password</label><input class="tcm-input" type="password" name="current_password" required></div>
            <div class="tcm-field"><label>New password</label><input class="tcm-input" type="password" name="password" required></div>
            <div class="tcm-field"><label>Confirm new password</label><input class="tcm-input" type="password" name="password_confirmation" required></div>
            <button class="tcm-btn primary"><i class="bi bi-shield-lock"></i> Update Password</button>
        </form>

        <div class="tcm-card" style="margin-top:18px;">
            <h3 class="mt-0">Order history</h3>
            <?php foreach ($orders as $o): ?>
                <div class="flex-between" style="padding:8px 0;border-bottom:1px solid var(--tcm-border);">
                    <span><?= e($o['item_title'] ?? $o['item_type']) ?></span>
                    <span class="muted"><?= money($o['amount']) ?> · <span class="tcm-badge <?= $o['status'] === 'paid' ? 'green' : 'amber' ?>"><?= e($o['status']) ?></span></span>
                </div>
            <?php endforeach; ?>
            <?php if ($orders === []): ?><p class="muted mb-0">No orders yet.</p><?php endif; ?>
        </div>
    </div>
</div>
