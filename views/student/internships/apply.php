<div class="tcm-page-head">
    <div><h2>Apply for Internship</h2><p><?= e($program['title']) ?></p></div>
    <a class="tcm-btn" href="<?= base_url('/student/programs/' . $program['slug']) ?>"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<form method="post" action="<?= base_url('/student/internships/' . $program['id'] . '/apply') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="tcm-card" style="max-width:720px;">
        <div class="tcm-grid-2">
            <div class="tcm-field"><label>Full name</label><input class="tcm-input" name="full_name" value="<?= e(old('full_name', $user['name'])) ?>" required></div>
            <div class="tcm-field"><label>Email</label><input class="tcm-input" type="email" name="email" value="<?= e(old('email', $user['email'])) ?>" required></div>
        </div>
        <div class="tcm-grid-2">
            <div class="tcm-field"><label>Phone (WhatsApp)</label><input class="tcm-input" name="phone" value="<?= e(old('phone', $user['phone'] ?? '')) ?>" required></div>
            <div class="tcm-field"><label>College</label><input class="tcm-input" name="college" value="<?= e(old('college', $profile['college'] ?? '')) ?>"></div>
        </div>
        <div class="tcm-field"><label>Skills (comma separated)</label><input class="tcm-input" name="skills" value="<?= e(old('skills')) ?>" placeholder="HTML, CSS, JavaScript, React"></div>
        <div class="tcm-field"><label>Portfolio / GitHub URL</label><input class="tcm-input" name="portfolio_url" value="<?= e(old('portfolio_url', $profile['github_url'] ?? '')) ?>"></div>
        <div class="tcm-field">
            <label>Why do you want this internship? *</label>
            <textarea class="tcm-textarea" name="why" required placeholder="Tell us about your goals and what you'll bring (min 20 characters)."><?= e(old('why')) ?></textarea>
        </div>
        <div class="tcm-field">
            <label>Resume (PDF / DOC / DOCX, max 5MB) *</label>
            <input class="tcm-input" type="file" name="resume" accept=".pdf,.doc,.docx" required>
        </div>
        <button class="tcm-btn primary"><i class="bi bi-send"></i> Submit Application</button>
    </div>
</form>
