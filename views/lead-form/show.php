<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($form['title']) ?> · The Code Munk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body {
    font-family: 'Inter', system-ui, sans-serif;
    background: #f7f7f7;
    min-height: 100%;
    -webkit-font-smoothing: antialiased;
}

/* Page */
.lf-page {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 32px 16px;
}

/* Card */
.lf-card {
    width: 100%;
    max-width: 480px;
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 28px rgba(0,0,0,.07);
}

/* Card header */
.lf-header {
    background: #111;
    padding: 28px 28px 22px;
    position: relative;
    overflow: hidden;
}
.lf-header::after {
    content: '';
    position: absolute;
    top: -50px; right: -50px;
    width: 200px; height: 200px;
    background: radial-gradient(circle, rgba(255,255,255,.05) 0%, transparent 70%);
}
.lf-brand {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-size: .78rem;
    font-weight: 700;
    color: rgba(255,255,255,.5);
    margin-bottom: 14px;
    text-decoration: none;
}
.lf-brand-icon {
    width: 22px; height: 22px;
    background: rgba(255,255,255,.15);
    border-radius: 6px;
    display: grid; place-items: center;
    font-size: .7rem; color: #fff;
}
.lf-header h1 {
    font-size: 1.3rem;
    font-weight: 800;
    color: #fff;
    letter-spacing: -.4px;
    margin-bottom: 6px;
    line-height: 1.25;
}
.lf-header p {
    font-size: .84rem;
    color: rgba(255,255,255,.55);
    line-height: 1.55;
}
<?php if ($form['context_type'] !== 'general' && $form['context_title']): ?>
.lf-context-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.15);
    border-radius: 999px;
    padding: 4px 12px;
    font-size: .72rem;
    font-weight: 600;
    color: rgba(255,255,255,.7);
    margin-bottom: 12px;
}
<?php endif; ?>

/* Form body */
.lf-body { padding: 24px 28px 28px; }

.lf-field { margin-bottom: 16px; }
.lf-label {
    display: block;
    font-size: .76rem;
    font-weight: 700;
    color: #444;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: 6px;
}
.lf-required { color: #dc2626; margin-left: 2px; }
.lf-input, .lf-textarea, .lf-select {
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
.lf-input:focus, .lf-textarea:focus, .lf-select:focus {
    border-color: #111;
    box-shadow: 0 0 0 3px rgba(0,0,0,.06);
}
.lf-input::placeholder, .lf-textarea::placeholder { color: #ccc; }
.lf-textarea { min-height: 88px; resize: vertical; }
.lf-select { appearance: none; cursor: pointer; }

.lf-btn {
    width: 100%;
    padding: 14px;
    background: #111;
    color: #fff;
    border: none;
    border-radius: 11px;
    font-size: .95rem;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    margin-top: 6px;
    transition: background .18s, transform .12s, box-shadow .18s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    letter-spacing: -.1px;
}
.lf-btn:hover {
    background: #333;
    transform: translateY(-1px);
    box-shadow: 0 5px 18px rgba(0,0,0,.18);
}
.lf-btn:active { transform: scale(.98); box-shadow: none; }

.lf-note {
    text-align: center;
    font-size: .72rem;
    color: #bbb;
    margin-top: 12px;
    line-height: 1.5;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
}

/* Footer */
.lf-footer {
    text-align: center;
    margin-top: 18px;
    font-size: .76rem;
    color: #aaa;
}
.lf-footer a { color: #111; font-weight: 600; text-decoration: none; }

@media (max-width: 520px) {
    .lf-page { padding: 16px 12px; }
    .lf-header { padding: 22px 20px 18px; }
    .lf-body  { padding: 20px 20px 24px; }
    .lf-header h1 { font-size: 1.15rem; }
}
</style>
</head>
<body>
<div class="lf-page">

    <div class="lf-card">

        <!-- Header -->
        <div class="lf-header">
            <a href="<?= base_url('/') ?>" class="lf-brand">
                <div class="lf-brand-icon"><i class="bi bi-code-slash"></i></div>
                The Code Munk
            </a>

            <?php if ($form['context_type'] !== 'general' && $form['context_title']): ?>
                <div class="lf-context-badge">
                    <i class="bi bi-<?= $form['context_type'] === 'event' ? 'calendar-event' : 'journal-code' ?>"></i>
                    <?= e($form['context_title']) ?>
                </div>
            <?php endif; ?>

            <h1><?= e($form['title']) ?></h1>

            <?php if (!empty($form['description'])): ?>
                <p><?= e($form['description']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Form body -->
        <div class="lf-body">
            <form method="post"
                  action="<?= base_url('/form/' . $form['slug'] . '/submit') ?>">
                <?= csrf_field() ?>

                <?php foreach ($fields as $f): ?>
                <div class="lf-field">
                    <label class="lf-label" for="lf_<?= e($f['name']) ?>">
                        <?= e($f['label'] ?? $f['name']) ?>
                        <?php if ($f['required'] ?? false): ?>
                            <span class="lf-required">*</span>
                        <?php endif; ?>
                    </label>

                    <?php $type = $f['type'] ?? 'text'; ?>
                    <?php if ($type === 'textarea'): ?>
                        <textarea class="lf-textarea"
                                  id="lf_<?= e($f['name']) ?>"
                                  name="<?= e($f['name']) ?>"
                                  placeholder="<?= e($f['label'] ?? '') ?>"
                                  <?= ($f['required'] ?? false) ? 'required' : '' ?>></textarea>
                    <?php elseif ($type === 'select'): ?>
                        <select class="lf-select"
                                id="lf_<?= e($f['name']) ?>"
                                name="<?= e($f['name']) ?>"
                                <?= ($f['required'] ?? false) ? 'required' : '' ?>>
                            <option value="">Select...</option>
                            <?php foreach (($f['options'] ?? []) as $opt): ?>
                                <option value="<?= e($opt) ?>"><?= e($opt) ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input class="lf-input"
                               type="<?= e($type) ?>"
                               id="lf_<?= e($f['name']) ?>"
                               name="<?= e($f['name']) ?>"
                               placeholder="<?= e($f['label'] ?? '') ?>"
                               <?= ($f['required'] ?? false) ? 'required' : '' ?>>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>

                <button type="submit" class="lf-btn">
                    <i class="bi bi-send"></i>
                    <?= e($form['cta_text'] ?: 'Submit') ?>
                </button>

                <div class="lf-note">
                    <i class="bi bi-lock-fill" style="font-size:.64rem;"></i>
                    Your information is private and secure.
                </div>
            </form>
        </div>

    </div>

    <div class="lf-footer">
        Powered by <a href="<?= base_url('/') ?>">The Code Munk</a>
    </div>

</div>
</body>
</html>
