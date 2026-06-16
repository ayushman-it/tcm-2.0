<?php
$isEdit  = $form !== null;
$action  = $isEdit ? base_url('/admin/lead-forms/' . $form['id']) : base_url('/admin/lead-forms');
$val     = static fn(string $k, $d = '') => e((string)($form[$k] ?? $d));
$fields  = json_decode($form['fields_json'] ?? '[]', true) ?: [
    ['name'=>'name',    'label'=>'Full Name',     'type'=>'text',     'required'=>true],
    ['name'=>'email',   'label'=>'Email Address', 'type'=>'email',    'required'=>true],
    ['name'=>'phone',   'label'=>'Phone Number',  'type'=>'tel',      'required'=>false],
    ['name'=>'message', 'label'=>'Message',       'type'=>'textarea', 'required'=>false],
];
$shareUrl = $isEdit ? base_url('/form/' . ($form['slug'] ?? '')) : null;
?>

<div class="tcm-page-head">
    <div>
        <h2><?= $isEdit ? 'Edit Lead Form' : 'New Lead Form' ?></h2>
        <p><?= $isEdit ? e($form['title']) : 'Create a shareable lead capture page.' ?></p>
    </div>
    <div class="d-flex gap-8">
        <?php if ($isEdit && $shareUrl): ?>
            <a href="<?= $shareUrl ?>" target="_blank" class="tcm-btn sm">
                <i class="bi bi-eye"></i> Preview
            </a>
        <?php endif; ?>
        <a href="<?= base_url('/admin/lead-forms') ?>" class="tcm-btn ghost sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<?php if ($isEdit && $shareUrl): ?>
<!-- Share URL Banner -->
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;
            padding:12px 16px;margin-bottom:16px;display:flex;align-items:center;gap:12px;">
    <i class="bi bi-share-fill" style="color:#16a34a;font-size:1rem;flex-shrink:0;"></i>
    <div style="flex:1;min-width:0;">
        <div style="font-size:.78rem;font-weight:700;color:#15803d;margin-bottom:2px;">
            Shareable Link
        </div>
        <div style="font-size:.82rem;color:#555;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
            <?= e($shareUrl) ?>
        </div>
    </div>
    <button onclick="navigator.clipboard.writeText('<?= e($shareUrl) ?>').then(()=>{ this.innerHTML='<i class=\'bi bi-check2\'></i> Copied!';this.style.color='#16a34a'; setTimeout(()=>{this.innerHTML='<i class=\'bi bi-copy\'></i> Copy';this.style.color='';},2000)})"
            class="tcm-btn sm" style="flex-shrink:0;">
        <i class="bi bi-copy"></i> Copy
    </button>
</div>
<?php endif; ?>

<form method="post" action="<?= $action ?>" id="leadFormBuilder">
    <?= csrf_field() ?>

    <div style="display:grid;grid-template-columns:1fr 340px;gap:16px;align-items:start;">

        <!-- Left: Form config -->
        <div>

            <!-- Basic info -->
            <div class="tcm-card" style="margin-bottom:14px;">
                <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;
                            color:var(--muted);margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid #f0f0f0;">
                    <i class="bi bi-info-circle" style="margin-right:5px;"></i> Form Details
                </div>

                <div class="tcm-field">
                    <label>Form Title *</label>
                    <input class="tcm-input" name="title" required
                           value="<?= $val('title') ?>"
                           placeholder="e.g. React Masterclass Registration">
                </div>

                <div class="tcm-field">
                    <label>Description</label>
                    <textarea class="tcm-textarea" name="description"
                              style="min-height:70px;"
                              placeholder="What is this form for?"><?= $val('description') ?></textarea>
                </div>

                <div class="tcm-grid-2">
                    <div class="tcm-field">
                        <label>Context Type</label>
                        <select class="tcm-select" name="context_type" id="ctxType"
                                onchange="updateContextList()">
                            <?php foreach (['general','event','course','program'] as $ct): ?>
                                <option value="<?= $ct ?>"
                                    <?= ($form['context_type'] ?? 'general') === $ct ? 'selected' : '' ?>>
                                    <?= ucfirst($ct) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="tcm-field" id="ctxIdWrap">
                        <label>Link to (optional)</label>
                        <select class="tcm-select" name="context_id" id="ctxId">
                            <option value="">— None —</option>
                            <?php foreach ($events as $ev): ?>
                                <option value="<?= $ev['id'] ?>"
                                        data-type="event"
                                        data-title="<?= e($ev['title']) ?>"
                                    <?= (int)($form['context_id'] ?? 0) === (int)$ev['id']
                                        && ($form['context_type'] ?? '') === 'event' ? 'selected' : '' ?>>
                                    [Event] <?= e($ev['title']) ?>
                                </option>
                            <?php endforeach; ?>
                            <?php foreach ($courses as $c): ?>
                                <option value="<?= $c['id'] ?>"
                                        data-type="course"
                                        data-title="<?= e($c['title']) ?>"
                                    <?= (int)($form['context_id'] ?? 0) === (int)$c['id']
                                        && ($form['context_type'] ?? '') === 'course' ? 'selected' : '' ?>>
                                    [Course] <?= e($c['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <input type="hidden" name="context_title" id="ctxTitle"
                       value="<?= $val('context_title') ?>">
            </div>

            <!-- Field Builder -->
            <div class="tcm-card" style="margin-bottom:14px;">
                <div style="display:flex;align-items:center;justify-content:space-between;
                            margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid #f0f0f0;">
                    <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;
                                letter-spacing:.08em;color:var(--muted);">
                        <i class="bi bi-ui-checks" style="margin-right:5px;"></i> Form Fields
                    </div>
                    <button type="button" onclick="addField()" class="tcm-btn sm primary">
                        <i class="bi bi-plus"></i> Add Field
                    </button>
                </div>

                <div id="fieldsContainer">
                    <?php foreach ($fields as $i => $f): ?>
                    <div class="adm-field-row" data-idx="<?= $i ?>">
                        <div style="display:flex;align-items:center;gap:6px;margin-bottom:8px;">
                            <span style="font-size:.72rem;font-weight:700;color:var(--muted);
                                         background:#f5f5f5;border-radius:5px;padding:2px 7px;">
                                #<?= $i + 1 ?>
                            </span>
                            <button type="button" onclick="removeField(this)"
                                    class="tcm-btn sm danger" style="padding:3px 7px;margin-left:auto;">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr 100px 80px;gap:8px;
                                    background:#f9f9f9;border:1px solid #ececec;border-radius:10px;padding:12px;">
                            <div>
                                <div style="font-size:.7rem;font-weight:700;color:var(--muted);margin-bottom:4px;">
                                    Field Name (slug)
                                </div>
                                <input class="tcm-input" name="field_name[]"
                                       value="<?= e($f['name']) ?>"
                                       placeholder="e.g. college" required
                                       style="font-size:.82rem;">
                            </div>
                            <div>
                                <div style="font-size:.7rem;font-weight:700;color:var(--muted);margin-bottom:4px;">
                                    Label (shown to user)
                                </div>
                                <input class="tcm-input" name="field_label[]"
                                       value="<?= e($f['label']) ?>"
                                       placeholder="e.g. College Name"
                                       style="font-size:.82rem;">
                            </div>
                            <div>
                                <div style="font-size:.7rem;font-weight:700;color:var(--muted);margin-bottom:4px;">
                                    Type
                                </div>
                                <select class="tcm-select" name="field_type[]" style="font-size:.82rem;">
                                    <?php foreach (['text','email','tel','number','textarea','select','date'] as $t): ?>
                                        <option value="<?= $t ?>"
                                            <?= ($f['type'] ?? 'text') === $t ? 'selected' : '' ?>>
                                            <?= ucfirst($t) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <div style="font-size:.7rem;font-weight:700;color:var(--muted);margin-bottom:4px;">
                                    Required
                                </div>
                                <select class="tcm-select" name="field_required[]" style="font-size:.82rem;">
                                    <option value="0" <?= !($f['required'] ?? false) ? 'selected' : '' ?>>No</option>
                                    <option value="1" <?= ($f['required'] ?? false)   ? 'selected' : '' ?>>Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>

        </div>

        <!-- Right: Settings + Save -->
        <div>

            <div class="tcm-card" style="margin-bottom:14px;">
                <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;
                            color:var(--muted);margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid #f0f0f0;">
                    <i class="bi bi-gear" style="margin-right:5px;"></i> Settings
                </div>

                <div class="tcm-field">
                    <label>CTA Button Text</label>
                    <input class="tcm-input" name="cta_text"
                           value="<?= $val('cta_text', 'Submit') ?>"
                           placeholder="e.g. Register Now">
                </div>

                <div class="tcm-field">
                    <label>Thank You Message</label>
                    <textarea class="tcm-textarea" name="thank_you_message"
                              style="min-height:70px;"><?= $val('thank_you_message', 'Thank you! We will be in touch shortly.') ?></textarea>
                </div>

                <div class="tcm-field">
                    <label>Status</label>
                    <select class="tcm-select" name="status">
                        <option value="active" <?= ($form['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>
                            ✅ Active (public)
                        </option>
                        <option value="inactive" <?= ($form['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>
                            ⏸️ Inactive (hidden)
                        </option>
                    </select>
                </div>

                <div class="tcm-field">
                    <label style="display:flex;align-items:center;gap:8px;font-size:.84rem;cursor:pointer;font-weight:normal;text-transform:none;letter-spacing:0;">
                        <input type="checkbox" name="whatsapp_redirect" value="1"
                               <?= (int)($form['whatsapp_redirect'] ?? 1) ? 'checked' : '' ?>
                               style="accent-color:#111;width:15px;height:15px;">
                        Redirect to WhatsApp after submit
                    </label>
                    <div style="font-size:.72rem;color:var(--muted);margin-top:4px;">
                        Lead is captured first, then user is redirected to your WhatsApp.
                    </div>
                </div>

            </div>

            <button type="submit" class="tcm-btn primary w-full"
                    style="justify-content:center;padding:13px;">
                <i class="bi bi-check2-circle"></i>
                <?= $isEdit ? 'Save Changes' : 'Create Form' ?>
            </button>

            <?php if ($isEdit && $shareUrl): ?>
            <a href="<?= $shareUrl ?>" target="_blank"
               class="tcm-btn w-full" style="justify-content:center;margin-top:8px;">
                <i class="bi bi-box-arrow-up-right"></i> Open Live Form
            </a>
            <?php endif; ?>

        </div>
    </div>

</form>

<template id="fieldRowTpl">
    <div class="adm-field-row">
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:8px;">
            <span class="field-num"
                  style="font-size:.72rem;font-weight:700;color:var(--muted);
                         background:#f5f5f5;border-radius:5px;padding:2px 7px;"></span>
            <button type="button" onclick="removeField(this)"
                    class="tcm-btn sm danger" style="padding:3px 7px;margin-left:auto;">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 100px 80px;gap:8px;
                    background:#f9f9f9;border:1px solid #ececec;border-radius:10px;padding:12px;">
            <div>
                <div style="font-size:.7rem;font-weight:700;color:var(--muted);margin-bottom:4px;">Field Name</div>
                <input class="tcm-input" name="field_name[]" placeholder="field_slug" required style="font-size:.82rem;">
            </div>
            <div>
                <div style="font-size:.7rem;font-weight:700;color:var(--muted);margin-bottom:4px;">Label</div>
                <input class="tcm-input" name="field_label[]" placeholder="Display label" style="font-size:.82rem;">
            </div>
            <div>
                <div style="font-size:.7rem;font-weight:700;color:var(--muted);margin-bottom:4px;">Type</div>
                <select class="tcm-select" name="field_type[]" style="font-size:.82rem;">
                    <option value="text">Text</option>
                    <option value="email">Email</option>
                    <option value="tel">Phone</option>
                    <option value="number">Number</option>
                    <option value="textarea">Textarea</option>
                    <option value="select">Select</option>
                    <option value="date">Date</option>
                </select>
            </div>
            <div>
                <div style="font-size:.7rem;font-weight:700;color:var(--muted);margin-bottom:4px;">Required</div>
                <select class="tcm-select" name="field_required[]" style="font-size:.82rem;">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </div>
        </div>
    </div>
</template>

<script>
function addField() {
    var tpl   = document.getElementById('fieldRowTpl').content.cloneNode(true);
    var cont  = document.getElementById('fieldsContainer');
    cont.appendChild(tpl);
    renumber();
    cont.lastElementChild.querySelector('input[name="field_name[]"]').focus();
}
function removeField(btn) {
    btn.closest('.adm-field-row').remove();
    renumber();
}
function renumber() {
    document.querySelectorAll('#fieldsContainer .adm-field-row').forEach(function(row, i) {
        var num = row.querySelector('.field-num');
        if (num) num.textContent = '#' + (i + 1);
    });
}
function updateContextList() {
    var type   = document.getElementById('ctxType').value;
    var sel    = document.getElementById('ctxId');
    var opts   = sel.querySelectorAll('option');
    opts.forEach(function(o) {
        if (!o.value) { o.style.display = ''; return; }
        o.style.display = (o.getAttribute('data-type') === type || type === 'general') ? '' : 'none';
    });
    sel.value = '';
    document.getElementById('ctxIdWrap').style.display = type === 'general' ? 'none' : '';
}
sel.addEventListener('change', function() {
    var opt = this.options[this.selectedIndex];
    document.getElementById('ctxTitle').value = opt ? (opt.getAttribute('data-title') || '') : '';
});
// Init
var sel = document.getElementById('ctxId');
document.getElementById('ctxIdWrap').style.display =
    document.getElementById('ctxType').value === 'general' ? 'none' : '';
</script>
