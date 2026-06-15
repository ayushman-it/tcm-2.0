<?php use TCM\Models\InternshipApplication; ?>
<div class="tcm-page-head">
    <div><h2>Internship Applications</h2><p>Review applicants, shortlist and select candidates.</p></div>
</div>

<div class="tcm-stat-grid" style="margin-bottom:18px;">
    <div class="tcm-stat"><i class="bi bi-inbox icon"></i><div class="label">Submitted</div><div class="value"><?= (int)$counts['submitted'] ?></div></div>
    <div class="tcm-stat"><i class="bi bi-star icon"></i><div class="label">Shortlisted</div><div class="value"><?= (int)$counts['shortlisted'] ?></div></div>
    <div class="tcm-stat"><i class="bi bi-check2-circle icon"></i><div class="label">Selected</div><div class="value"><?= (int)$counts['selected'] ?></div></div>
</div>

<div class="tcm-card">
    <form method="get" class="d-flex gap-8 flex-wrap" style="margin-bottom:16px;">
        <input class="tcm-input" name="q" placeholder="Search applicant..." value="<?= e($_GET['q'] ?? '') ?>" style="flex:1;min-width:180px;">
        <select class="tcm-select" name="program_id" style="width:200px;" onchange="this.form.submit()">
            <option value="">All internships</option>
            <?php foreach ($programs as $p): ?>
                <option value="<?= $p['id'] ?>" <?= (int)($_GET['program_id'] ?? 0) === (int)$p['id'] ? 'selected' : '' ?>><?= e($p['title']) ?></option>
            <?php endforeach; ?>
        </select>
        <select class="tcm-select" name="status" style="width:160px;" onchange="this.form.submit()">
            <option value="">All status</option>
            <?php foreach (InternshipApplication::STATUSES as $k => $lbl): ?>
                <option value="<?= $k ?>" <?= ($_GET['status'] ?? '') === $k ? 'selected' : '' ?>><?= e($lbl) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="tcm-btn primary">Filter</button>
    </form>

    <table class="tcm-table">
        <thead><tr><th>Applicant</th><th>Internship</th><th>Applied</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($applications as $a): ?>
            <tr>
                <td><strong><?= e($a['full_name']) ?></strong><br><span class="muted" style="font-size:.78rem;"><?= e($a['email']) ?> · <?= e($a['phone'] ?? '') ?></span></td>
                <td class="muted"><?= e($a['program_title']) ?></td>
                <td class="muted" style="font-size:.8rem;"><?= e(date('d M Y', strtotime($a['created_at']))) ?></td>
                <td>
                    <?php $cls = ['submitted'=>'amber','under_review'=>'purple','shortlisted'=>'purple','selected'=>'green','rejected'=>'red'][$a['status']] ?? 'gray'; ?>
                    <span class="tcm-badge <?= $cls ?>"><?= e(InternshipApplication::STATUSES[$a['status']] ?? $a['status']) ?></span>
                </td>
                <td><a class="tcm-btn sm" href="<?= base_url('/admin/internships/' . $a['id']) ?>">Review</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if ($applications === []): ?><tr><td colspan="5" class="muted">No applications yet.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
