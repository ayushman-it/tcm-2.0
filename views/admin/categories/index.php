<div class="tcm-page-head">
    <div><h2>Categories</h2><p>Group courses by audience and topic.</p></div>
</div>

<div class="tcm-grid-2" style="align-items:start;">
    <div class="tcm-card">
        <table class="tcm-table">
            <thead><tr><th>Name</th><th>Audience</th><th>Order</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($categories as $c): ?>
                <tr>
                    <td><i class="bi <?= e($c['icon'] ?? 'bi-collection') ?>"></i> <?= e($c['name']) ?></td>
                    <td class="muted"><?= e($c['audience']) ?></td>
                    <td class="muted"><?= (int)$c['sort_order'] ?></td>
                    <td>
                        <form method="post" action="<?= base_url('/admin/categories/' . $c['id'] . '/delete') ?>" onsubmit="return confirm('Delete category?');">
                            <?= csrf_field() ?>
                            <button class="tcm-btn sm danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($categories === []): ?><tr><td colspan="4" class="muted">No categories.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="tcm-card">
        <h3 class="mt-0">Add category</h3>
        <form method="post" action="<?= base_url('/admin/categories') ?>">
            <?= csrf_field() ?>
            <div class="tcm-field"><label>Name</label><input class="tcm-input" name="name" required></div>
            <div class="tcm-field"><label>Description</label><input class="tcm-input" name="description"></div>
            <div class="tcm-grid-2">
                <div class="tcm-field"><label>Icon</label><input class="tcm-input" name="icon" value="bi-collection"></div>
                <div class="tcm-field">
                    <label>Audience</label>
                    <select class="tcm-select" name="audience">
                        <option value="general">General</option>
                        <option value="college">College</option>
                        <option value="beginners">Beginners</option>
                        <option value="working">Working</option>
                    </select>
                </div>
            </div>
            <div class="tcm-field" style="max-width:120px;"><label>Sort order</label><input class="tcm-input" type="number" name="sort_order" value="0"></div>
            <button class="tcm-btn primary"><i class="bi bi-plus-lg"></i> Add</button>
        </form>
    </div>
</div>
