<div class="tcm-page-head">
    <div><h2>Site Settings</h2><p>Global content shown across the public site.</p></div>
</div>

<form method="post" action="<?= base_url('/admin/settings') ?>">
    <?= csrf_field() ?>
    <div class="tcm-card" style="max-width:640px;">
        <div class="tcm-grid-2">
            <div class="tcm-field"><label>Site name</label><input class="tcm-input" name="site_name" value="<?= e($settings['site_name'] ?? '') ?>"></div>
            <div class="tcm-field"><label>Currency</label><input class="tcm-input" name="currency" value="<?= e($settings['currency'] ?? 'INR') ?>"></div>
        </div>
        <div class="tcm-field"><label>Tagline</label><input class="tcm-input" name="site_tagline" value="<?= e($settings['site_tagline'] ?? '') ?>"></div>
        <div class="tcm-grid-2">
            <div class="tcm-field"><label>Contact email</label><input class="tcm-input" name="contact_email" value="<?= e($settings['contact_email'] ?? '') ?>"></div>
            <div class="tcm-field"><label>Contact phone</label><input class="tcm-input" name="contact_phone" value="<?= e($settings['contact_phone'] ?? '') ?>"></div>
        </div>
        <div class="tcm-grid-2">
            <div class="tcm-field"><label>Students count (display)</label><input class="tcm-input" name="students_count" value="<?= e($settings['students_count'] ?? '') ?>"></div>
            <div class="tcm-field"><label>Courses count (display)</label><input class="tcm-input" name="courses_count" value="<?= e($settings['courses_count'] ?? '') ?>"></div>
        </div>
        <hr style="border-color:var(--tcm-border);margin:18px 0;">
        <h3 class="mt-0" style="font-size:1.05rem;"><i class="bi bi-whatsapp" style="color:#25d366;"></i> WhatsApp lead routing</h3>
        <p class="muted" style="margin-top:-6px;font-size:.84rem;">Enquiries route to this number via click-to-chat. International format, digits only (e.g. 919876543210).</p>
        <div class="tcm-grid-2">
            <div class="tcm-field"><label>WhatsApp number</label><input class="tcm-input" name="whatsapp_number" value="<?= e($settings['whatsapp_number'] ?? '') ?>" placeholder="919876543210"></div>
            <div class="tcm-field"><label>Default message prefix</label><input class="tcm-input" name="whatsapp_message" value="<?= e($settings['whatsapp_message'] ?? 'Hi The Code Munk! I am interested in') ?>"></div>
        </div>
        <button class="tcm-btn primary"><i class="bi bi-check2"></i> Save Settings</button>
    </div>
</form>
