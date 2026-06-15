<?php
$success = flash('success');
$error   = flash('error');
?>
<?php if ($success !== null): ?>
    <div class="tcm-flash success">
        <i class="bi bi-check-circle-fill"></i>
        <?= e($success) ?>
    </div>
<?php endif; ?>
<?php if ($error !== null): ?>
    <div class="tcm-flash error">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <?= e($error) ?>
    </div>
<?php endif; ?>
