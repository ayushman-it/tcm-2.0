<div class="tcm-stat-grid">
    <div class="tcm-stat"><i class="bi bi-people icon"></i><div class="label">Students</div><div class="value"><?= number_format($stats['students']) ?></div></div>
    <div class="tcm-stat"><i class="bi bi-journal-code icon"></i><div class="label">Courses</div><div class="value"><?= number_format($stats['courses']) ?></div></div>
    <div class="tcm-stat"><i class="bi bi-calendar-event icon"></i><div class="label">Events</div><div class="value"><?= number_format($stats['events']) ?></div></div>
    <div class="tcm-stat"><i class="bi bi-mortarboard icon"></i><div class="label">Enrollments</div><div class="value"><?= number_format($stats['enrollments']) ?></div></div>
    <div class="tcm-stat"><i class="bi bi-currency-rupee icon"></i><div class="label">Revenue</div><div class="value"><?= money($stats['revenue']) ?></div></div>
    <div class="tcm-stat"><i class="bi bi-envelope icon"></i><div class="label">New messages</div><div class="value"><?= number_format($stats['new_messages']) ?></div></div>
</div>

<div class="tcm-grid-2" style="margin-top:18px;align-items:start;">
    <div class="tcm-card">
        <div class="flex-between" style="margin-bottom:14px;">
            <h3 class="mt-0 mb-0">Recent orders</h3>
            <a class="muted" href="<?= base_url('/admin/students') ?>" style="font-size:.85rem;">View students</a>
        </div>
        <table class="tcm-table">
            <thead><tr><th>Order</th><th>Student</th><th>Item</th><th>Amount</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($recentOrders as $o): ?>
                <tr>
                    <td><?= e($o['order_number']) ?></td>
                    <td><?= e($o['student_name']) ?></td>
                    <td><?= e($o['item_title'] ?? ucfirst($o['item_type'])) ?></td>
                    <td><?= money($o['amount']) ?></td>
                    <td><span class="tcm-badge <?= $o['status'] === 'paid' ? 'green' : 'amber' ?>"><?= e($o['status']) ?></span></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($recentOrders === []): ?>
                <tr><td colspan="5" class="muted">No orders yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="tcm-card">
        <h3 class="mt-0" style="margin-bottom:14px;">New students</h3>
        <table class="tcm-table">
            <thead><tr><th>Name</th><th>Email</th><th>Joined</th></tr></thead>
            <tbody>
            <?php foreach ($recentStudents as $s): ?>
                <tr>
                    <td><a href="<?= base_url('/admin/students/' . $s['id']) ?>"><?= e($s['name']) ?></a></td>
                    <td class="muted"><?= e($s['email']) ?></td>
                    <td class="muted"><?= e(date('d M Y', strtotime($s['created_at']))) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($recentStudents === []): ?>
                <tr><td colspan="3" class="muted">No students yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
