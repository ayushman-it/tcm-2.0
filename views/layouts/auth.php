<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'The Code Munk') ?> · The Code Munk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('/assets/dashboard.css') ?>">
</head>
<body>
    <div class="tcm-auth">
        <div class="tcm-auth-card">
            <div class="tcm-brand" style="border:0;padding:0;margin-bottom:18px;">
                <i class="bi bi-code-slash"></i> The Code Munk
            </div>
            <?php require dirname(__DIR__) . '/partials/flash.php'; ?>
            <?= $content ?? '' ?>
        </div>
    </div>
</body>
</html>
