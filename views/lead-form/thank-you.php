<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You! · The Code Munk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',system-ui,sans-serif;background:#f7f7f7;min-height:100vh;
     display:grid;place-items:center;padding:24px;-webkit-font-smoothing:antialiased}
.ty-card{background:#fff;border:1px solid #e5e5e5;border-radius:20px;
         max-width:420px;width:100%;padding:40px 32px;text-align:center;
         box-shadow:0 4px 24px rgba(0,0,0,.07)}
.ty-icon{width:64px;height:64px;background:#f0fdf4;border:2px solid #bbf7d0;
         border-radius:50%;display:grid;place-items:center;margin:0 auto 20px;
         font-size:1.6rem;color:#16a34a;
         animation:pop .5s cubic-bezier(.34,1.56,.64,1) both}
@keyframes pop{0%{transform:scale(0);opacity:0}100%{transform:scale(1);opacity:1}}
.ty-title{font-size:1.4rem;font-weight:800;color:#111;letter-spacing:-.4px;margin-bottom:8px}
.ty-msg{font-size:.88rem;color:#666;line-height:1.6;margin-bottom:28px}
.ty-btn{display:inline-flex;align-items:center;gap:7px;padding:11px 22px;
        background:#111;color:#fff;border-radius:10px;font-size:.85rem;
        font-weight:700;text-decoration:none;transition:background .15s}
.ty-btn:hover{background:#333;color:#fff}
.ty-footer{margin-top:20px;font-size:.74rem;color:#bbb}
.ty-footer a{color:#111;font-weight:600;text-decoration:none}
</style>
</head>
<body>
<div class="ty-card">
    <div class="ty-icon"><i class="bi bi-check-lg"></i></div>
    <div class="ty-title">You're all set! 🎉</div>
    <div class="ty-msg">
        <?= e($form['thank_you_message'] ?? 'Thank you! We will be in touch shortly.') ?>
    </div>
    <a href="<?= base_url('/') ?>" class="ty-btn">
        <i class="bi bi-house"></i> Back to Homepage
    </a>
    <div class="ty-footer" style="margin-top:14px;">
        Powered by <a href="<?= base_url('/') ?>">The Code Munk</a>
    </div>
</div>
</body>
</html>
