<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'The Code Munk') ?> · The Code Munk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('/assets/dashboard.css') ?>">

    <style>
    /* ── Auth / Onboarding page shell ─────────────────── */
    body { background: #f5f5f5; }

    .auth-shell {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Top nav bar */
    .auth-nav {
        background: #fff;
        border-bottom: 1px solid #ececec;
        padding: 0 32px;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .auth-nav-brand {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: .95rem;
        font-weight: 800;
        color: #111;
        text-decoration: none;
        letter-spacing: -.2px;
    }
    .auth-nav-brand i { font-size: 1rem; }
    .auth-nav-right {
        font-size: .78rem;
        color: #888;
    }
    .auth-nav-right a { color: #111; font-weight: 600; }

    /* Page body */
    .auth-body {
        flex: 1;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 40px 16px 60px;
    }

    /* Card */
    .auth-card {
        width: 100%;
        max-width: 600px;
        background: #fff;
        border: 1px solid #e5e5e5;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 32px rgba(0,0,0,.07);
    }

    /* Card header */
    .auth-card-header {
        padding: 28px 32px 24px;
        border-bottom: 1px solid #f0f0f0;
        background: #fff;
    }

    /* Card body */
    .auth-card-body {
        padding: 28px 32px 32px;
    }

    @media (max-width: 640px) {
        .auth-body { padding: 20px 12px 40px; }
        .auth-card { border-radius: 16px; }
        .auth-card-header { padding: 22px 20px 18px; }
        .auth-card-body { padding: 20px 20px 24px; }
        .auth-nav { padding: 0 16px; }
    }
    </style>
</head>
<body>
<div class="auth-shell">

    <!-- Top bar -->
    <nav class="auth-nav">
        <a href="<?= base_url('/') ?>" class="auth-nav-brand">
            <i class="bi bi-code-slash"></i> The Code Munk
        </a>
        <div class="auth-nav-right">
            Need help? <a href="<?= base_url('/') ?>">Visit homepage</a>
        </div>
    </nav>

    <!-- Body -->
    <div class="auth-body">
        <div class="auth-card">
            <?= $content ?? '' ?>
        </div>
    </div>

</div>
</body>
</html>
