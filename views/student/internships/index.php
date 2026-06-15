<?php use TCM\Models\InternshipApplication; ?>
<div class="tcm-page-head">
    <div><h2>My Applications</h2><p>Track your internship applications.</p></div>
    <a class="tcm-btn primary" href="<?= base_url('/student/programs?type=internship') ?>"><i class="bi bi-search"></i> Find internships</a>
</div>

<div class="tcm-card">
    <table class="tcm-table">
        <thead><tr><th>Internship</th><th>Applied</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($applications as $a): ?>
            <tr>
                <td><a href="<?= base_url('/student/programs/' . $a['program_slug']) ?>"><?= e($a['program_title']) ?></a></td>
                <td class="muted"><?= e(date('d M Y', strtotime($a['created_at']))) ?></td>
                <td>
                    <?php
                    $cls = ['submitted'=>'amber','under_review'=>'purple','shortlisted'=>'purple','selected'=>'green','rejected'=>'red'][$a['status']] ?? 'gray';
                    ?>
                    <span class="tcm-badge <?= $cls ?>"><?= e(InternshipApplication::STATUSES[$a['status']] ?? $a['status']) ?></span>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if ($applications === []): ?>
            <tr><td colspan="3" class="muted">No applications yet. <a href="<?= base_url('/student/programs?type=internship') ?>">Browse internships →</a></td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
