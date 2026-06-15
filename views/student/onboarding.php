<h1>Set up your profile</h1>
<p class="sub">Tell us a bit about yourself so we can tailor your journey.</p>

<form method="post" action="<?= base_url('/student/onboarding') ?>">
    <?= csrf_field() ?>
    <div class="tcm-field">
        <label for="headline">Headline</label>
        <input class="tcm-input" id="headline" name="headline" placeholder="e.g. Aspiring Full Stack Developer"
               value="<?= e($profile['headline'] ?? '') ?>">
    </div>
    <div class="tcm-field">
        <label for="bio">Short bio</label>
        <textarea class="tcm-textarea" id="bio" name="bio" placeholder="What are you learning and building?"><?= e($profile['bio'] ?? '') ?></textarea>
    </div>
    <div class="tcm-grid-2">
        <div class="tcm-field">
            <label for="college">College / Organisation</label>
            <input class="tcm-input" id="college" name="college" value="<?= e($profile['college'] ?? '') ?>">
        </div>
        <div class="tcm-field">
            <label for="graduation_year">Graduation year</label>
            <input class="tcm-input" type="number" id="graduation_year" name="graduation_year"
                   value="<?= e((string)($profile['graduation_year'] ?? '')) ?>">
        </div>
    </div>
    <div class="tcm-grid-2">
        <div class="tcm-field">
            <label for="experience_level">Experience level</label>
            <select class="tcm-select" id="experience_level" name="experience_level">
                <?php foreach (['beginner','intermediate','advanced'] as $lvl): ?>
                    <option value="<?= $lvl ?>" <?= ($profile['experience_level'] ?? '') === $lvl ? 'selected' : '' ?>>
                        <?= ucfirst($lvl) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="tcm-field">
            <label for="location">Location</label>
            <input class="tcm-input" id="location" name="location" value="<?= e($profile['location'] ?? '') ?>">
        </div>
    </div>
    <div class="tcm-field">
        <label for="goal">Your main goal</label>
        <input class="tcm-input" id="goal" name="goal" placeholder="e.g. Land a frontend internship"
               value="<?= e($profile['goal'] ?? '') ?>">
    </div>
    <div class="tcm-grid-2">
        <div class="tcm-field">
            <label for="github_url">GitHub URL</label>
            <input class="tcm-input" id="github_url" name="github_url" value="<?= e($profile['github_url'] ?? '') ?>">
        </div>
        <div class="tcm-field">
            <label for="linkedin_url">LinkedIn URL</label>
            <input class="tcm-input" id="linkedin_url" name="linkedin_url" value="<?= e($profile['linkedin_url'] ?? '') ?>">
        </div>
    </div>
    <button class="tcm-btn primary" style="width:100%;justify-content:center;">
        <i class="bi bi-check2-circle"></i> Save & Continue
    </button>
</form>
